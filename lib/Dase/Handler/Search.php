<?php

class Dase_Handler_Search extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'search',
	);

	protected function setup($r)
	{
		$this->user = $r->getUser();
	}

	public function getSearch($r) 
	{
		$t = new Dase_Template($r);
		$q = $r->get('q');
		$set = array();
		$have_only = $r->get('restrict_to_have');
		$t->assign('restrict_to_have',$have_only);
		$faculty = new Dase_DBO_Faculty($this->db);
		$faculty->orderBy('lastname');
		$faculty->addWhere('search_text',"%$q%",'like');
		if ($r->get('sort')) {
				$faculty->orderBy('college, dept, lastname');
				$t->assign('sort',1);
		}

		if ($r->get('tenure')) {
				if ('none' == $r->get('tenure')) {
						$faculty->addWhere('tenure','null','is');
				} else {
						$faculty->tenure = $r->get('tenure');
				}
				$t->assign('tenure',$r->get('tenure'));
		} else {
				$t->assign('tenure','any');
		}


		$set = $faculty->findAll(1);
		$new_set = array();
		$have_set = array();
		foreach ($set as $f) {
				if (file_exists('/mnt/www-data/publications/by_eid/'.$f->eid)) {
						$f->have_cv = 1;
						$have_set[] = $f;
				}
				$new_set[] = $f;
		}

		if ($have_only) {
				$new_set = $have_set;
		}

		$result_message = "Search for \"$q\" matched ".count($new_set)." records";
		$t->assign('result_message',$result_message);
		$t->assign('set',$new_set);
		$t->assign('q',$q);
		$r->renderResponse($t->fetch('home.tpl'));
	}

}

