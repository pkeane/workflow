<?php

require_once 'Dase/DBO/Autogen/Version.php';

class Dase_DBO_Version extends Dase_DBO_Autogen_Version 
{
		public $lines = array();
		public $cv = null;
		public $is_from_pref_cv = false;

		public function __get($key)
		{
				if ('ago' == $key) {
						$ts = strtotime($this->timestamp);
						$now = time();
						$diff = $now - $ts;
						$ago['days'] = $diff/(3600*24);
						$ago['hours'] = $diff/3600;
						$ago['minutes'] = $diff/60;
						$ago['seconds'] = $diff;
						foreach ($ago as $str => $num) {
								if ($num > 2) {
										$num = floor($num);
										return "$num $str ago";
								}
						}
				}
				return parent::__get($key);
		}

		public function generateLines($by_eid)
		{
				if ($this->has_citations) {
						return;
				}
				$i = 0;
				//lines n a conversion always share timestamp
				$timestamp = date(DATE_ATOM);
				foreach (preg_split('/(\r\n|\r|\n)/', $this->text) as $string) {

						//get rid of curlies && em-dash
						$string = preg_replace('/\xe2\x80\x99/',"'",$string);
						$string = preg_replace('/\xe2\x80\x98/',"'",$string);
						$string = preg_replace('/\xe2\x80\x9c/','"',$string);
						$string = preg_replace('/\xe2\x80\x9d/','"',$string);
						$string = preg_replace('/\xe2\x80\x94/','-',$string);

						$string = preg_replace('/\xca\xbc/',"'",$string);
						$string = preg_replace('/\xca\xbb/',"'",$string);


						$l = new Dase_DBO_Line($this->db);
						$l->text = trim($string);
						$l->text = trim($l->text, "\x00..\x1F");
						if ($l->text) {

								$i++;
								$l->text_md5 = md5($l->text);
								$l->faculty_eid = $this->faculty_eid;
								$l->created_by = $by_eid;
								$l->is_citation = 1;
								$l->is_hidden = 0;
								$l->cv_id = $this->uploaded_file_id;
								$l->version_id = $this->id;
								$l->timestamp = $timestamp;
								$l->sort_order = $i;
								$l->insert();
						}
				}
				$this->has_citations = $i;
				$this->update();
				return $i;
		}

		public function insertLineAfter($sort_order,$line) 
		{
				foreach ($this->getLines() as $l) {
						if ($l->sort_order > $sort_order) {
								$l->sort_order += 1;
								$l->update();
						}
						if ($l->id == $line->id) {
								$l->sort_order = $sort_order+1;
								$l->update();
						}
				}
		}

		public function getLines($show_hidden=false)
		{
				$lines = new Dase_DBO_Line($this->db);
				$lines->version_id = $this->id;
				$lines->orderBy('sort_order');
				if (!$show_hidden) {
						$lines->is_hidden = 0;
				}
				$this->lines = $lines->findAll(1);
				return $this->lines;
		}

		public function getCv()
		{
				$cv = new Dase_DBO_UploadedFile($this->db);
				$cv->load($this->uploaded_file_id);
				$this->cv = $cv;
				return $cv;
		}

		public function isFromPrefCv()
		{
				if ($this->getCv()->is_preferred) {
						$this->is_from_pref_cv = true;
						return true;
				} else {
						return false;
				}
		}

		public function getOneLine($show_hidden=false)
		{
				$lines = new Dase_DBO_Line($this->db);
				$lines->version_id = $this->id;
				return $lines->findOne();
		}

		public function makePreferred()
		{
				$ver = new Dase_DBO_Version($this->db);
				$ver->uploaded_file_id = $this->uploaded_file_id;
				foreach ($ver->findAll(1) as $v) {
						if ($v->id == $this->id) {
								$v->is_preferred = 1;
						} else {
								$v->is_preferred = 0;
						}
						$v->update();
				}
		}
}
