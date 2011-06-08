<?php

function sortByName($a,$b)
{
		$a_str = strtolower($a['name']);
		$b_str = strtolower($b['name']);
		if ($a_str == $b_str) {
				return 0;
		}
		return ($a_str < $b_str) ? -1 : 1;
}

class Dase_Handler_Upload extends Dase_Handler
{
		public $resource_map = array(
				'search' => 'uploader_search',
				//for POSTs
				'files' => 'uploader',
				'{eid}/ingester' => 'ingester',
				'{eid}' => 'uploader',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getUploaderSearch($r)
		{
				$t = new Dase_Template($r);
				if ($r->get('lastname')) {
						$key = 'sn';
						$term = $r->get('lastname');
						if ('eid:' == substr($term,0,4)) {
								$key = 'uid';
								$term = substr($term,4);
						}

						$results = Utlookup::lookup($term,$key);

						if (0 == count($results) && 'uid' == $key) {
								$fac = new Dase_DBO_Faculty($this->db);
								$fac->eid = $term;
								$fac->findOne();
								$results = array
										(
												array( 'eid' => $fac->eid,'name' => $fac->firstname." ".$fac->lastname,),
										);
						}

						usort($results,'sortByName');
						$t->assign('lastname',$r->get('lastname'));
						$t->assign('results',$results);
				}
				$cv = new Dase_DBO_UploadedFile($this->db);
				$cv->uploaded_by = $this->user->eid;
				$t->assign('files',$cv->findAll(1));
				$r->renderResponse($t->fetch('upload_search.tpl'));
		}

		public function getUploader($r)
		{
				$t = new Dase_Template($r);
				$f = new Dase_DBO_Faculty($this->db);
				$f->eid = $r->get('eid');
				if ($f->findOne()) {
						$t->assign('eid',$f->eid);
						$t->assign('name',$f->firstname.' '.$f->lastname);
						$files = new Dase_DBO_UploadedFile($this->db);
						$files->eid = $f->eid;
						$t->assign('files',$files->findAll(1));
				} else {
						$t->assign('msg',$r->get('eid').' is NOT in faculty list');
				}

				$r->renderResponse($t->fetch('upload.tpl'));
		}


		public function postToIngester($r)
		{
				$url = $r->get('url');
				$html = file_get_contents($url);

				if( preg_match("#<title>(.+)<\/title>#iU", $html, $t))  {
						$name = trim($t[1]);
				} else {
						$name = 'html CV';
				}

				$base_dir = "/mnt/www-data/publications/by_eid/".$r->get('eid');

				if (!file_exists($base_dir)) {
						mkdir($base_dir);
				}

				$newname = md5(time().$name).'.html';
				$new_path = $base_dir.'/'.$newname;
				file_put_contents($new_path,$html);
				chmod($new_path,0755);
				$cv = new Dase_DBO_UploadedFile($this->db);
				$cv->upload_mime = 'text/html';
				$cv->upload_note = 'ingester';
				$cv->other_updated = '';
				$cv->uploaded_by = $this->user->eid;
				$cv->orig_name  = $name;
				$cv->url = $url;
				$cv->name  = $newname;
				$cv->eid = $r->get('eid');
				$cv->uploaded = date(DATE_ATOM);
				$text_path = str_replace('.html','.txt',$new_path);
				$cmd = '/usr/bin/lynx -dump '.$url.' > '.$text_path;
				print system($cmd);
				$cv->rawtext = file_get_contents($text_path);
				$cv->rawtext_md5 = md5($cv->rawtext);
				$cv->rawtext_size = strlen($cv->rawtext);
				$cv->converted = date(DATE_ATOM);
				$cv->workflow_state = 'converted';
				$cv->insert();

				$r->renderRedirect('upload/'.$cv->eid);
		}

		public function postToUploader($r)
		{
				$file = $r->_files['uploaded_file'];
				if ($file && is_file($file['tmp_name'])) {
						$name = $file['name'];
						$path = $file['tmp_name'];
						$type = $file['type'];
						if (!is_uploaded_file($path)) {
								$r->renderError(400,'no go upload');
						}
				}

				$base_dir = "/mnt/www-data/publications/by_eid/".$r->get('cv_eid');

				if (!file_exists($base_dir)) {
						mkdir($base_dir);
				}

				$ext = 'unknown';
				if ('application/pdf' == $type) {
						$ext = 'pdf';
				}
				if ('application/msword' == $type) {
						$ext = 'doc';
				}
				if ('application/vnd.openxmlformats-officedocument.wordprocessingml.document' == $type) {
						$ext = 'docx';
				}

				$newname = md5(time().$name).'.'.$ext;
				$new_path = $base_dir.'/'.$newname;
				rename($path,$new_path);
				chmod($new_path,0755);
				$cv = new Dase_DBO_UploadedFile($this->db);
				$cv->upload_mime = $type;
				$cv->upload_note = 'from workflow';
				$cv->other_updated = '';
				$cv->uploaded_by = $this->user->eid;
				$cv->orig_name  = $name;
				$cv->name  = $newname;
				$cv->eid = $r->get('cv_eid');
				$cv->uploaded = date(DATE_ATOM);
				$cv->insert();

				$r->renderRedirect('upload/'.$cv->eid);

		}
}

