<?php

class Dase_Handler_Line extends Dase_Handler
{
		public $resource_map = array(
				'{id}' => 'line',
				'{id}/{attribute}/{state}' => 'line_state',
				'{id}/text' => 'line_text',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function postToLineText($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$line->revised_text = $r->get('text');
				if (!$r->get('cancel')) {
						$line->update();
				}
				$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines');
		}

		public function putLineState($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$att = $r->get('attribute');
				if ($line->hasMember($att)) {
						$line->$att = $r->get('state');
						if ($line->update()) {
								$r->renderOk();
						}
				}
				$r->renderError(400);
		}

		public function getLineJson($r) 
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$set = array();
				foreach ($line as $k => $v) {
						$set[$k] = $v;
				}
				$r->renderResponse(Dase_Json::get($set));
		}


}

