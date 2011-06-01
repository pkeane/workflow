<?php

class Dase_Handler_Admin extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'admin',
		'users' => 'users',
		'reports' => 'reports',
		'add_user_form/{eid}' => 'add_user_form',
		'user/{id}/is_admin' => 'is_admin',
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

	public function initTemplate($t)
	{
		//useful for menu stuff
		//$t->assign('exercise_sets',Dase_DBO_ExerciseSet::getAll($this->db));
	}

	public function getAdmin($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$r->renderResponse($t->fetch('admin.tpl'));
	}

	public function getUsers($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$users = new Dase_DBO_User($this->db);
		$users->orderBy('name');
		$t->assign('users', $users->findAll(1));
		$r->renderResponse($t->fetch('admin_users.tpl'));
	}

	public function getReports($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('admin_reports.tpl'));
	}

	public function getAddUserForm($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$record = Utlookup::getRecord($r->get('eid'));
		$u = new Dase_DBO_User($this->db);
		$u->eid = $r->get('eid');
		if ($u->findOne()) {
			$t->assign('user',$u);
		}
		$t->assign('record',$record);
		$r->renderResponse($t->fetch('add_user_form.tpl'));
	}

	public function postToUsers($r)
	{
		$record = Utlookup::getRecord($r->get('eid'));
		$user = new Dase_DBO_User($this->db);
		$user->eid = $record['eid'];
		if (!$user->findOne()) {
			$user->name = $record['name'];
			$user->email = $record['email'];
			$user->insert();
		} else {
			//$user->update();
		}
		$r->renderRedirect('admin');

	}

	public function deleteIsAdmin($r) 
	{
		$user = new Dase_DBO_User($this->db);
		$user->load($r->get('id'));
		$user->is_admin = 0;
		$user->update();
		$r->renderResponse('deleted privileges');
	}

	public function putIsAdmin($r) 
	{
		$user = new Dase_DBO_User($this->db);
		$user->load($r->get('id'));
		$user->is_admin = 1;
		$user->update();
		$r->renderResponse('added privileges');
	}


}

