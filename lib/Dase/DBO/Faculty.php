<?php

require_once 'Dase/DBO/Autogen/Faculty.php';

class Dase_DBO_Faculty extends Dase_DBO_Autogen_Faculty 
{
		public function hasCompleteCv()
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->status = 'complete';
				$file->eid = $this->eid;
				if ($file->findCount()) {
						return true;
				}
				return false;
		}
}
