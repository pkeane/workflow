<?php

require_once 'Dase/DBO/Autogen/Faculty.php';

class Dase_DBO_Faculty extends Dase_DBO_Autogen_Faculty 
{
		public $versions = array();
		public $pref_count;

		public static function get($db, $eid) 
		{
				$fac = new Dase_DBO_Faculty($db);
				$fac->eid = $eid;
				return $fac->findOne();
		}

		public function getVersions()
		{
				$versions = new Dase_DBO_Version($this->db);
				$versions->faculty_eid = $this->eid;
				$this->versions = $versions->findAll(1);
				return $this->versions;
		}

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

		public function hasPrefCv()
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->is_preferred = 1;
				$file->eid = $this->eid;
				if ($file->findCount()) {
						return true;
				}
				return false;
		}

		public function getCvCount()
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->eid = $this->eid;
				return $file->findCount();
		}

		public function hasLines()
		{
				$lines = new Dase_DBO_Line($this->db);
				$lines->faculty_eid = $this->eid;
				if ($lines->findCount()) {
						return true;
				}
				return false;
		}

		public function isReady()
		{
				//if ($this->hasCompleteCv() && 'READY' == $this->problem_note) {
				if ($this->hasLines() && 'READY' == $this->problem_note) {
						return true;
				}
				return false;
		}

		public function getReadyVersions()
		{
				$set = array();
				$versions = new Dase_DBO_Version($this->db);
				$versions->faculty_eid = $this->eid;
				foreach ($versions->findAll(1) as $v) {
						if ($v->has_citations) {
								$v->getCv();
								$v->getLines();
								$set[] = $v;
						}
				}
				return $set;
		}
}
