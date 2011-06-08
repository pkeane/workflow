<?php

class Dase_Handler_Report extends Dase_Handler
{
		public $resource_map = array(
				'need_cv' => 'need_cv',
				'null_status' => 'null_status',
				'problem_files' => 'problem_files',
				'problem_faculty' => 'problem_faculty',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
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


		public function getProblemFaculty($r)
		{
				$t = new Dase_Template($r);
				$facs = new Dase_DBO_Faculty($this->db);
				$facs->has_problem = 1;
				$t->assign('facs',$facs->findAll(1));
				$r->renderResponse($t->fetch('report_problem_facs.tpl'));
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

