<?php

class Dase_Handler_Line extends Dase_Handler
{
		public $resource_map = array(
				'{id}' => 'line',
				'{id}/poss_dups' => 'poss_dups',
				'{id}/selfdiff' => 'selfdiff',
				'{id}/diff' => 'diff',
				'{id}/problem' => 'line_problem',
				'{id}/{attribute}/{state}' => 'line_state',
				'{id}/text' => 'line_text',
				'{id}/annotation_text' => 'line_annotation_text',
				'{id}/test_parse' => 'test_parse',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getSelfdiff($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));

				$new = array($line->revised_text);
				$old = array($line->text);

				$diff     = new Text_Diff('auto', array($old, $new));
				$renderer = new Text_Diff_Renderer_inline();
				$r->renderResponse($renderer->render($diff));
		}

		public function getDiff($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));

				$prefline = new Dase_DBO_Line($this->db);
				$prefline->load($line->is_poss_dup_of);

				if ($prefline->revised_text) {
						$old = $prefline->revised_text;
				} else {
						$old = $prefline->text;
				}	
				$old = array($old);
				$new = array($line->text);

				$diff     = new Text_Diff('auto', array($old, $new));
				$renderer = new Text_Diff_Renderer_inline();
				$r->renderResponse($renderer->render($diff));
		}

		public function getPossDupsJson($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->is_poss_dup_of = $r->get('id');
				$set = array();
				foreach ($line->findAll(1) as $l) {
						$sub['id'] = $l->id;
						$sub['text'] = $l->text;
						$sub['revised_text'] = $l->revised_text;
						$sub['levenshtein'] = $l->levenshtein;
						$set[] = $sub;
				}
				if (count($set)) {
						$r->renderResponse(Dase_Json::get($set));
				} else {
						$r->renderResponse('[{"text":"no dups"}]');
				}
		}

		public function getTestParseJson($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$url = "http://quickdraw.laits.utexas.edu/bibparse";
				if ($line->revised_text) {
						$text = $line->revised_text;
				} else {
						$text = $line->text;
				}
				$res = Dase_Http::post($url,$text,'','');
				$r->renderResponse($res[1]);
		}

		public function postToLineAnnotationText($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$line->annotation_text = $r->get('annotation_text');
				if (!$r->get('cancel')) {
						$line->update();
				}
				$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
		}

		public function postToLineProblem($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));
				$line->problem_note = $r->get('problem_note');
				if (!$r->get('cancel')) {
						$line->update();
				}
				$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
		}

		public function postToLineText($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));

				if ($r->get('split')) {
						$fragments = preg_split('/(\r\n|\r|\n)/', trim($r->get('text')));
						$line->revised_text = array_shift($fragments);
						$line->revised_text_md5 = md5($line->revised_text);
						$line->update();

						$newline = new Dase_DBO_Line($this->db);
						$newline->text = $line->text;
						$newline->revised_text = trim(join(' ',$fragments));

						if ($newline->revised_text) {
								$newline->revised_text_md5 = md5($newline->revised_text);
								$newline->faculty_eid = $line->faculty_eid;
								$newline->created_by = $this->user->eid;
								$newline->is_citation = $line->is_citation;
								$newline->is_hidden = 0;
								$newline->cv_id = $line->cv_id;
								$newline->version_id = $line->version_id;
								$newline->timestamp = date(DATE_ATOM); 
								$newline->insert();

								$version = new Dase_DBO_Version($this->db);
								$version->load($line->version_id);
								$version->insertLineAfter($line->sort_order,$newline);
						}

						$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
				}

				if ($r->get('annotation')) {
						$fragments = preg_split('/(\r\n|\r|\n)/', trim($r->get('text')));
						$line->revised_text = array_shift($fragments);
						$line->revised_text_md5 = md5($line->revised_text);
						$line->annotation_text = trim(join(' ',$fragments));
						$line->update();
						$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
				}

				if ($r->get('merge')) {
						if ($line->sort_order < 2) {
								$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
						}
						//note that we lose some "original text" w/ this (text field)
						$prevline = new Dase_DBO_Line($this->db);
						$prevline->version_id = $line->version_id;
						$prevline->sort_order = $line->sort_order - 1;
						$prevline->findOne();
						if (!$prevline->revised_text) {
								$prevline->revised_text = $prevline->text;
						}
						$prevline->revised_text .= ' '.trim($r->get('text'));
						$prevline->update();
						$line->is_hidden = 1;
						$line->update();
						$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$prevline->id);
				}


				$line->revised_text = $r->get('text');
				$line->revised_text_md5 = md5($line->revised_text);
				if (!$r->get('cancel')) {
						$line->update();
				}
				$r->renderRedirect('faculty/'.$line->faculty_eid.'/file/'.$line->cv_id.'/version/'.$line->version_id.'/lines#line'.$line->id);
		}

		public function putLineState($r)
		{
				$line = new Dase_DBO_Line($this->db);
				$line->load($r->get('id'));

				//too much overhead??
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $line->faculty_eid;
				$fac->findOne();
				$fac->lines_last_edited_by = $this->user->eid;
				$fac->update();

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

