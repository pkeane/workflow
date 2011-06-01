<?php

class Dase_Handler_File extends Dase_Handler
{
		public $resource_map = array(
				'{eid}/{filename}/download' => 'file',
				'{eid}/{filename}/view' => 'file_text',
				'{eid}/{filename}/convertor' => 'file_convertor',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getFile($r) 
		{
				$uploaded_file = new Dase_DBO_UploadedFile($this->db);
				$uploaded_file->eid = $r->get('eid');
				$uploaded_file->name = $r->get('filename');
				$f = $uploaded_file->findOne();
				$filename = '/mnt/www-data/publications/by_eid/'.$f->eid.'/'.$f->name;
				$r->serveFile($filename,$f->upload_mime,true);
		}

		public function getFileText($r) 
		{

				$t = new Dase_Template($r);

				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$uploaded_file = new Dase_DBO_UploadedFile($this->db);
				$uploaded_file->eid = $r->get('eid');
				$uploaded_file->name = $r->get('filename');
				$f = $uploaded_file->findOne();
				$filename = '/mnt/www-data/publications/by_eid/'.$f->eid.'/'.$f->name;
				$new_path = '/mnt/www-data/publications/processing/'.$f->eid.'_'.$f->name;
				$mime = $f->upload_mime;

				$rawtext = $this->convert($filename,$new_path,$mime);

				$t->assign('file',$uploaded_file);
				$t->assign('raw',$rawtext);
				$r->renderResponse($t->fetch('faculty_file.tpl'));
		}

}

