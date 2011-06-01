<?php

require_once 'Dase/DBO/Autogen/Watchlist.php';

class Dase_DBO_Watchlist extends Dase_DBO_Autogen_Watchlist 
{

		public static function check($db,$user_eid,$faculty_eid)
		{
				$w = new Dase_DBO_Watchlist($db);
				$w->user_eid = $user_eid;
				$w->faculty_eid = $faculty_eid;
				return $w->findOne();
		}

		public function getFaculty()
		{
				$f = new Dase_DBO_Faculty($this->db);
				$f->eid = $this->faculty_eid;
				return $f->findOne();
		}
}
