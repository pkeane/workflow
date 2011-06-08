<?php

require_once 'Dase/DBO/Autogen/Version.php';

class Dase_DBO_Version extends Dase_DBO_Autogen_Version 
{
		public $lines = array();

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

		public function getLines($show_hidden=false)
		{
				$lines = new Dase_DBO_Line($this->db);
				$lines->version_id = $this->id;
				if (!$show_hidden) {
						$lines->is_hidden = 0;
				}
				$this->lines = $lines->findAll(1);
				return $this->lines;

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
