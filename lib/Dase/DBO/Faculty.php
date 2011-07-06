<?php

require_once 'Dase/DBO/Autogen/Faculty.php';

class Dase_DBO_Faculty extends Dase_DBO_Autogen_Faculty 
{
		public $versions = array();
		public $pref_count;
		public $dup_count;
		public $poss_dups;

		public static function get($db, $eid) 
		{
				$fac = new Dase_DBO_Faculty($db);
				$fac->eid = $eid;
				return $fac->findOne();
		}

		public function __get($key)
		{
				if ('initial' == $key) {
						return (substr($this->firstname,0,1));
				}
				return parent::__get($key);
		}

		public function getVersions()
		{
				$versions = new Dase_DBO_Version($this->db);
				$versions->faculty_eid = $this->eid;
				$this->versions = $versions->findAll(1);
				return $this->versions;
		}

		public function getDupCount()
		{
				$line = new Dase_DBO_Line($this->db);
				$line->addWhere('poss_dups_count',0,'>');
				$line->faculty_eid = $this->eid;
				$this->dup_count  = $line->findCount();
				return $this->dup_count;
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

		public function  dedupLines($user_eid)
		{
				$debug = array();
				$possible_dups_count = 0;
				$pref_lines = array();
				$np_lines = array();
				foreach ($this->getVersions() as $v) {
						if ($v->is_preferred) {
								if (!$v->has_citations) {
										$v->generateLines($user_eid);
								}
								if ($v->isFromPrefCv()) {
										$pref_lines = $v->getLines(1);
								} else {
										//$np_lines = array_merge($np_lines,$v->getLines(1));
										foreach ($v->getLines(1) as $np) {
												$np_lines[$np->id] = $np;
										}
								}
						}
				}
				//print count($pref_lines).' X '.count($np_lines)."\n";

				foreach ($pref_lines as $pref_line) {
						//reset
						if ($pref_line->poss_dups_count) {
								$pref_line->poss_dups_count = 0;
								$pref_line->update();
						}
						foreach ($np_lines as $np_line) {
								//straight dup
								if (trim($pref_line->text) == trim($np_line->text)) {
										$np_line->is_dup_of = $pref_line->id;
										$np_line->update();
								} else {
										//dash space issue
										$test_p = str_replace('- ','-',$pref_line->text);
										$test_np = str_replace('- ','-',$np_line->text);
										if ($test_p == $test_np) {
												$pref_line->text = $test_p;
												$pref_line->update();
												$np_line->text = $test_np;
												$np_line->is_dup_of = $pref_line->id;
												$np_line->update();
										} else {
												//look for poss dups
												$plen = strlen(trim($pref_line->text));
												$nplen = strlen(trim($np_line->text));
												//if ($plen < 255 && $nplen < 255 ) {
												if ($plen > 40 && $plen < 255 && $nplen < 255 ) {
														$lev = levenshtein(trim($pref_line->text),trim($np_line->text));
														if ($lev > 0 && $lev < 20) {
																$np_line->is_poss_dup_of = $pref_line->id;
																$np_line->levenshtein = $lev;
																$np_line->is_hidden = 1;
																if ($np_line->update()) {

																		//$debug[] = $np_line->id;
																		$possible_dups_count += 1;
																		$pref_line->poss_dups_count += 1;
																		$pref_line->update();
																}
														}
												}
										}
								}
						}
				}
				//print_r( $debug); exit;
				return $possible_dups_count;
		}
}
