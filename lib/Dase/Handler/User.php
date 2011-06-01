<?php

class Dase_Handler_User extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'user',
		'settings' => 'settings',
		'email' => 'email',
		'watchlist' => 'watchlist',
		'{eid}/watchlist/{faculty_eid}' => 'watchlist_entry'
	);

	protected function setup($r)
	{
		$this->user = $r->getUser();
	}

	public function deleteWatchlistEntry($r)
	{
			$w = new Dase_DBO_Watchlist($this->db);
			$w->user_eid = $r->get('eid');
			$w->faculty_eid = $r->get('faculty_eid');
			if ($w->findOne()) {
					$w->delete();
					$r->renderOk();
			} else {
					$r->renderError(400);
			}
	}

	public function putWatchlistEntry($r)
	{
			$w = new Dase_DBO_Watchlist($this->db);
			$w->user_eid = $r->get('eid');
			$w->faculty_eid = $r->get('faculty_eid');
			if (!$w->findOne()) {
					$w->insert();
					$r->renderOk();
			} else {
					$r->renderError(400);
			}
	}

	public function getSettings($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('user_settings.tpl'));
	}

	public function getWatchlist($r) 
	{
		$t = new Dase_Template($r);
		$t->assign('set',$this->user->getWatchlist());
		$r->renderResponse($t->fetch('user_watchlist.tpl'));
	}

	public function getUser($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('home.tpl'));
	}

	public function postToEmail($r)
	{
		$this->user->email = $r->get('email');
		$this->user->update();
		$r->renderRedirect('user/settings');
	}
}

