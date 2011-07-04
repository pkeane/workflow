<?php

require_once 'Dase/DBO/Autogen/Line.php';

class Dase_DBO_Line extends Dase_DBO_Autogen_Line 
{
		public $possible_dups = array();
		public $dups = array();

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

		public function getVersion()
		{
				$v = new Dase_DBO_Version($this->db);
				$v->load($this->version_id);
				return $v;
		}

		public function probCite()
		{
				$matches = array();
				if (preg_match_all('/(\.|,)/',$this->text,$matches)) {
						return true;
				} else {
						return false;
				}
		}
}
