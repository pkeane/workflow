<?php

class Dase_Handler_Faculty extends Dase_Handler
{
		public $resource_map = array(
				'{eid}' => 'faculty',
				'{eid}/file/{id}' => 'file',
				'{eid}/file/{id}/versioner' => 'versioner',
				'{eid}/file/{id}/metadata' => 'metadata',
				'{eid}/file/{uploaded_file_id}/version/{id}' => 'version',
				'{eid}/file/{uploaded_file_id}/version/{id}/lines' => 'lines',
				'{eid}/file/{uploaded_file_id}/version/{id}/diff' => 'diff',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getLines($r)
		{
				$t = new Dase_Template($r);

				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				$t->assign('v',$v);
				$t->assign('lines',$v->getLines($r->get('show_hidden')));
				$t->assign('show_hidden',$r->get('show_hidden'));
				$r->renderResponse($t->fetch('lines.tpl'));
		}

		public function postToLines($r)
		{
				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				$i = 0;
				$timestamp = date(DATE_ATOM);
				foreach (preg_split('/(\r\n|\r|\n)/', $v->text) as $line) {
						$l = new Dase_DBO_Line($this->db);
						$l->text = trim($line);
						if ($l->text) {
								$i++;
								$l->text_md5 = md5($l->text);
								$l->faculty_eid = $r->get('eid');
								$l->created_by = $this->user->eid;
								$l->is_citation = 1;
								$l->is_hidden = 0;
								$l->cv_id = $r->get('uploaded_file_id');
								$l->version_id = $v->id;
								$l->timestamp = $timestamp;
								$l->sort_order = $i;
								$l->insert();
						}
				}
				$v->has_citations = $i;
				$v->update();
				$r->renderRedirect('faculty/'.$r->get('eid').'/file/'.$r->get('uploaded_file_id').'/version/'.$v->id.'/lines');
		}

		public function getVersion($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				if ($v->load($r->get('id'))) {
						$t->assign('v',$v);
						$r->renderResponse($t->fetch('faculty_cv_version.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
				}
		}

		public function getDiff($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				if ($v->load($r->get('id'))) {
						$old = explode("\n",$file->rawtext);
						$new = explode("\n",$v->text);
						$diff     = new Text_Diff('auto', array($old, $new));
					//	$renderer = new Text_Diff_Renderer_unified();
						if ($r->get('context')) {
								$renderer = new Text_Diff_Renderer_context();
						} else {
								$renderer = new Text_Diff_Renderer_inline();
						}
						$t->assign('diff',$renderer->render($diff));
						$t->assign('v',$v);
						$r->renderResponse($t->fetch('diff.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
				}
		}

		public function getFile($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());
				$file = new Dase_DBO_UploadedFile($this->db);

				if ($file->load($r->get('id'))) {
						if (!$file->rawtext_md5 || $r->get('reconvert')) {
								$file->convert();
						}
						$t->assign('versions',$file->getVersions());
						$t->assign('file',$file);
						$r->renderResponse($t->fetch('faculty_file.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid);
				}
		}

		public function postToVersioner($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));

				$v = new Dase_DBO_Version($this->db);
				$v->uploaded_file_id = $file->id;
				$v->edited_by = $this->user->eid;
				$text = $r->get('text');
				//if text, it is spawned from a version
				//else it is a new version of undeited text
				if ($text) {
						if ($r->get('remove')) {
								$i = 0;
								foreach (preg_split('/(\r\n|\r|\n)/', $text) as $line) {
										$this_line = trim($line);
										if ($this_line) {
												if (!isset($lines[$i])) {
														$lines[$i] = '';
												}
												$lines[$i] .= $this_line;
												$lines[$i] .= ' ';
										} else {
												$i++;
										}
								}
								$new_lines = array();
								foreach ($lines as $ln) {
										$ln = preg_replace('/\xc2\xa0/',' ',$ln);
										$ln = preg_replace('/\s\s+/',' ', $ln);
										$new_lines[] = trim($ln);
								}
								$text = join("\n\n",$new_lines);
								$v->linebreaks_removed = 1;
						}
						$v->text = $text;
						$v->text_md5 = md5($text);
						$v->note = $r->get('note');
				} else {
						$v->text = $file->rawtext;
						$v->text_md5 = $file->rawtext_md5;
						$v->note = 'unedited copy of CV';
				}
				$v->timestamp = date(DATE_ATOM);
				$v->insert();
				$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
		}

		public function postToMetadata($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				$file->date_on_cv = $r->get('date_on_cv');
				$file->status = $r->get('status');
				$file->note = $r->get('note');
				$file->update();

				$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
		}

		public function deleteVersion($r) 
		{
				//todo: really need to check here to see if 
				//work has been done on the lines
				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				foreach ($v->getLines() as $line) {
						$line->delete();
				}
				$v->delete();
				$r->renderOk();
		}

		public function deleteFile($r) 
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				if (!count($file->getVersions()) && $this->user->eid == $file->uploaded_by) {
						$file->delete();
				}
				$r->renderOk();
		}

		public function getFaculty($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());
				$files = new Dase_DBO_UploadedFile($this->db);
				$files->eid = $fac->eid;
				$set = array();
				foreach ($files->findAll(1) as $f) {
						$f->getVersions();
						$set[] = $f;
				}
				$t->assign('files',$set);
				$r->renderResponse($t->fetch('faculty.tpl'));
		}

}

