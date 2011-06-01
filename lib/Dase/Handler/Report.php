<?php

class Dase_Handler_Report extends Dase_Handler
{
		public $resource_map = array(
				'need_cv' => 'need_cv',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getNeedCv($r) 
		{
				$t = new Dase_Template($r);
				$faculty = new Dase_DBO_Faculty($this->db);
				$faculty->orderBy('tenure, college, dept, lastname');
				$set = array();

				$complete = array();
				$files = new Dase_DBO_UploadedFile($this->db);
				$files->status = 'complete';
				foreach ($files->findAll(1) as $f) {
						$complete[$f->eid] = 1;
				}

				foreach ($faculty->findAll(1) as $fac) {
						if ($fac->tenure == 'TT' || $fac->tenure = 'TN') {
								if (!isset($complete[$fac->eid])) {
										$set[] = $fac;
								}
						}
				}
				$t->assign('set',$set);
				$r->renderResponse($t->fetch('report_need_cv.tpl'));
		}
}

