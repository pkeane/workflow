<?php

class Dase_Handler_Report extends Dase_Handler
{
		public $resource_map = array(
				'need_cv' => 'need_cv',
				'null_status' => 'null_status',
				'recent_uploads' => 'recent_uploads',
				'problem_files' => 'problem_files',
				'misassigned_files' => 'misassigned_files',
				'problem_faculty' => 'problem_faculty',
				'faculty_poss_dups' => 'faculty_poss_dups',
				'noprob_faculty' => 'noprob_faculty',
				'faculty_no_pref_versions' => 'faculty_no_pref_versions',
				'faculty_has_pref_versions' => 'faculty_has_pref_versions',
				'pref_cv_multiple_versions' => 'pref_cv_multiple_versions',
				'file/{id}' => 'file',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
				if ($this->user->is_admin) {
						//ok
				} else {
						$r->renderError(401);
				}
		}

		public function getPrefCvMultipleVersions($r)
		{
				$t = new Dase_Template($r);
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->is_preferred = 1;
				$set = array();
				foreach ($file->find() as $f) {
						$f = clone($f);
						$v = new Dase_DBO_Version($this->db);
						$v->uploaded_file_id = $f->id;
						$v->is_preferred = 1;
						if ($v->findCount() > 1) {
								$set[] = $f;
						}
				}
				$t->assign('files',$set);
				$r->renderResponse($t->fetch('report_problem_files.tpl'));
		}


		public function getFacultyNoPrefVersions($r)
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');

				$has_pref = array();
				$versions = new Dase_DBO_Version($this->db);
				foreach ($versions->find() as $v) {
						$v = clone($v);
						if ($v->is_preferred) {
								$has_pref[$v->faculty_eid] = 1;
								unset($v);
						}
				}

				$set = array();
				foreach ($faculty->findAll(1) as $fac) {
						if (isset($has_pref[$fac->eid])) {
								//$set[] = $fac;
						} else {
								$set[] = $fac;
						}
				}
				$t->assign('facs',$set);
				$r->renderResponse($t->fetch('report_fac_no_pref_versions.tpl'));
		}

		public function getFacultyHasPrefVersions($r)
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');

				$has_pref = array();
				$versions = new Dase_DBO_Version($this->db);
				foreach ($versions->find() as $v) {
						$v = clone($v);
						if ($v->is_preferred) {
								if (!isset($has_pref[$v->faculty_eid])) {
										$has_pref[$v->faculty_eid] = 0;
								};
								$has_pref[$v->faculty_eid] += 1;
								unset($v);
						}
				}

				$set = array();
				foreach ($faculty->findAll(1) as $fac) {
						if (isset($has_pref[$fac->eid])) {
								$fac->pref_count = $has_pref[$fac->eid];
								$set[] = $fac;
						} else {
								//$set[] = $fac;
						}
				}
				$t->assign('facs',$set);
				$r->renderResponse($t->fetch('report_fac_has_pref_versions.tpl'));
		}

		public function getFacultyPossDups($r)
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				//$faculty->orderBy('college, dept, lastname');

				$set = array();
				$count = 0;
				foreach ($faculty->findAll() as $fac) {
						$line = new Dase_DBO_Line($this->db);
						$line->addWhere('poss_dups_count',0,'>');
						$line->faculty_eid = $fac->eid;
						$count  = $line->findCount();
						if ($count) {
								$fac->poss_dups = $count;
								$set[] = $fac;
						}
				}
				$t->assign('facs',$set);
				$r->renderResponse($t->fetch('report_fac_with_poss_dups.tpl'));
		}

		public function getFile($r)
		{
				$t = new Dase_Template($r);
				$file = new Dase_DBO_UploadedFile($this->db);
				if ($file->load($r->get('id'))) {
						$fac = new Dase_DBO_Faculty($this->db);
						$fac->eid = $file->eid;
						$fac->findOne();
						$t->assign('fac',$fac);
						$t->assign('file',$file);
						$r->renderResponse($t->fetch('report_file.tpl'));
				} else {
						$r->renderRedirect('report/misassigned_files');
				}
		}

		public function getRecentUploads($r)
		{
				$t = new Dase_Template($r);
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->orderBy('uploaded DESC');
				$file->setLimit(100);
				$t->assign('files',$file->findAll());
				$r->renderResponse($t->fetch('report_recent_uploads.tpl'));
		}

		public function postToFile($r)
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				$file->problem_note = $r->get('problem_note');
				if ($file->problem_note) {
						$file->has_problem = 1;
				} else {
						$file->has_problem = 0;
				}
				$file->eid = $r->get('eid');
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $file->eid;
				if ($fac->findOne()) {
						$file->update();
						$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
				} else {
						$params['msg'] = "No faculty with EID $fac->eid";
						$r->renderRedirect('report/file/'.$file->id,$params);
				}
		}

		public function deleteFile($r)
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				if ($file->delete()) {
						$r->renderOk();
				}
		}

		public function getNeedCv($r) 
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');
				$set = array();
				$no_tenure_set = array();

				$complete = array();
				$files = new Dase_DBO_UploadedFile($this->db);
				if ($r->get('have')) {
						//pass
				} else {
						$files->status = 'complete';
				}

				foreach ($files->find() as $f) {
						$f = clone($f);
						$complete[$f->eid] = 1;
						unset($f);
				}

				foreach ($faculty->findAll(1) as $fac) {
						if (in_array($fac->tenure,array('TT','TN'))) {
								if (!isset($complete[$fac->eid])) {
										$set[] = $fac;
								}
						} else {
								if (!isset($complete[$fac->eid])) {
										$no_tenure_set[] = $fac;
								}
						}
				}
				$this->user->getWatchlist();
				$t->assign('set',$set);
				if ($r->get('noten')) {
						$t->assign('set',$no_tenure_set);
				}
				$r->renderResponse($t->fetch('report_need_cv.tpl'));
		}

		public function getNullStatus($r) 
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');
				$set = array();
				$no_tenure_set = array();

				$null = array();
				$files = new Dase_DBO_UploadedFile($this->db);
				foreach ($files->find() as $f) {
						$f = clone($f);
						if (!$f->status) {
								$null[$f->eid] = 1;
						}
						unset($f);
				}

				foreach ($faculty->findAll(1) as $fac) {
						if (in_array($fac->tenure,array('TT','TN'))) {
								if (isset($null[$fac->eid])) {
										$set[] = $fac;
								}
						} else {
								if (isset($null[$fac->eid])) {
										$no_tenure_set[] = $fac;
								}
						}
				}
				$this->user->getWatchlist();
				$t->assign('set',$set);
				if ($r->get('noten')) {
						$t->assign('set',$no_tenure_set);
				}
				$r->renderResponse($t->fetch('report_null_status.tpl'));
		}

		public function getProblemFiles($r)
		{
				$t = new Dase_Template($r);
				$files = new Dase_DBO_UploadedFile($this->db);
				$files->has_problem = 1;
				$t->assign('files',$files->findAll(1));
				$r->renderResponse($t->fetch('report_problem_files.tpl'));
		}


		public function getMisassignedFiles($r)
		{
				$t = new Dase_Template($r);
				$files = new Dase_DBO_UploadedFile($this->db);
				$files->addWhere('problem_note','%MISASSIGNED%','like');
				$t->assign('files',$files->findAll(1));
				$r->renderResponse($t->fetch('report_misassigned_files.tpl'));
		}


		public function getProblemFaculty($r)
		{
				$t = new Dase_Template($r);
				$facs = new Dase_DBO_Faculty($this->db);
				$facs->has_problem = 1;
				$t->assign('facs',$facs->findAll(1));
				$r->renderResponse($t->fetch('report_problem_facs.tpl'));
		}

		public function getProblemFacultyCsv($r)
		{
				$t = new Dase_Template($r);
				$facs = new Dase_DBO_Faculty($this->db);
				$facs->has_problem = 1;
				$t->assign('facs',$facs->findAll(1));
				$r->renderResponse($t->fetch('report_problem_facs_csv.tpl'));
		}

		public function getNoprobFaculty($r)
		{
				$t = new Dase_Template($r);
				$facs = new Dase_DBO_Faculty($this->db);
				$set = array();
				foreach ($facs->findAll() as $f) {
						if (1 != $f->has_problem) {
								$set[] = $f;
						}
				}
				$t->assign('facs',$set);
				$r->renderResponse($t->fetch('report_noprob_facs.tpl'));
		}

		public function getNoprobFacultyCsv($r)
		{
				$t = new Dase_Template($r);
				$facs = new Dase_DBO_Faculty($this->db);
				$set = array();
				foreach ($facs->findAll() as $f) {
						if (1 != $f->has_problem) {
								$set[] = $f;
						}
				}
				$t->assign('facs',$set);
				$r->renderResponse($t->fetch('report_noprob_facs_csv.tpl'));
		}


		public function getNullStatusCsv($r) 
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');
				$set = array();
				$no_tenure_set = array();

				$null = array();
				$files = new Dase_DBO_UploadedFile($this->db);
				foreach ($files->find() as $f) {
						$f = clone($f);
						if (!$f->status) {
								$null[$f->eid] = 1;
						}
						unset($f);
				}

				foreach ($faculty->findAll(1) as $fac) {
						if (in_array($fac->tenure,array('TT','TN'))) {
								if (isset($null[$fac->eid])) {
										$set[] = $fac;
								}
						} else {
								if (isset($null[$fac->eid])) {
										$no_tenure_set[] = $fac;
								}
						}
				}
				$this->user->getWatchlist();
				$t->assign('set',$set);
				if ($r->get('noten')) {
						$t->assign('set',$no_tenure_set);
				}
				$r->renderResponse($t->fetch('report_need_cv_csv.tpl'));
		}

		public function getNeedCvCsv($r) 
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('college, dept, lastname');
				$set = array();
				$no_tenure_set = array();

				$complete = array();
				$files = new Dase_DBO_UploadedFile($this->db);
				if ($r->get('have')) {
						//pass
				} else {
						$files->status = 'complete';
				}
				foreach ($files->find() as $f) {
						$f = clone($f);
						$complete[$f->eid] = 1;
						unset($f);
				}

				foreach ($faculty->findAll(1) as $fac) {
						if (in_array($fac->tenure,array('TT','TN'))) {
								if (!isset($complete[$fac->eid])) {
										$set[] = $fac;
								}
						} else {
								if (!isset($complete[$fac->eid])) {
										$no_tenure_set[] = $fac;
								}
						}
				}
				$t->assign('set',$set);
				if ($r->get('noten')) {
						$t->assign('set',$no_tenure_set);
				}
				$r->renderResponse($t->fetch('report_need_cv_csv.tpl'));
		}
}

