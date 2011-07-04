<?php

class Dase_Handler_Faculty extends Dase_Handler
{
		public $resource_map = array(
				'{eid}' => 'faculty',
				'{eid}/problem' => 'faculty_problem',
				'{eid}/dedup' => 'dedup',
				'{eid}/poss_dups' => 'poss_dups',
				'{eid}/file/{id}' => 'file',
				'{eid}/file/{id}/versioner' => 'versioner',
				'{eid}/file/{id}/metadata' => 'metadata',
				'{eid}/file/{id}/problem' => 'problem',
				'{eid}/file/{id}/preferred' => 'preferred',
				'{eid}/file/{uploaded_file_id}/version/{id}' => 'version',
				'{eid}/file/{uploaded_file_id}/version/{id}/lines' => 'lines',
				'{eid}/file/{uploaded_file_id}/version/{id}/diff' => 'diff',
				'{eid}/file/{uploaded_file_id}/version/{id}/preferred' => 'preferred_version',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function postToDedup($r)
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');

				$np_lines = array();
				foreach ($fac->getVersions() as $v) {
						if (!$v->has_citations) {
								$v->generateLines($this->user->eid);
						}
						if ($v->is_preferred) {
								if ($v->isFromPrefCv()) {
										$pref_lines = $v->getLines();
								} else {
										$np_lines = array_merge($np_lines,$v->getLines());
								}
						}
				}

				foreach ($pref_lines as $pref_line) {
						foreach ($np_lines as $np_line) {
								//ignore lines we know are dups
								if (!$np_line->is_dup_of) {
										if (trim($pref_line->text) == trim($np_line->text)) {
												$np_line->is_dup_of = $pref_line->id;
												$np_line->update();
										} else {
												if (strlen(trim($pref_line->text)) < 255 && strlen(trim($np_line->text)) < 255 ) {
														$lev = levenshtein(trim($pref_line->text),trim($np_line->text));
														if ($lev > 0 && $lev < 20) {
																$np_line->is_poss_dup_of = $pref_line->id;
																$np_line->levenshtein = $lev;
																$np_line->update();

																$pref_line->poss_dups_count += 1;
																$pref_line->update();
														}
												}
										}
								}
						}
				}
				$r->renderRedirect('faculty/'.$r->get('eid'));
		}

		public function getPossDups($r)
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());
				$lines = new Dase_DBO_Line($this->db);
				$lines->faculty_eid = $fac->eid;
				$fac_lines = $lines->findAll();

				$pref_versions = new Dase_DBO_Version($this->db);
				$pref_versions->faculty_eid = $fac->eid;
				$pref_versions->addWhere('is_preferred',1,'=');
				$pref_ids = array();
				foreach ($pref_versions->findAll(1) as $pv) {
						$pref_ids[] = $pv->id;
				}

				foreach ($fac_lines as $fline) {
						$v_id = $fline->version_id;
						//get ONLY lines from preferred versions
						if (in_array($v_id,$pref_ids)) {
								if ($fline->is_poss_dup_of) {
										$fac_lines[$fline->is_poss_dup_of]->possible_dups[] = $fline;;
								}
						} else {
								unset($fac_lines[$fline->id]);
						}
				}
				$t->assign('lines',$fac_lines);
				$r->renderResponse($t->fetch('fac_poss_dups.tpl'));
		}

		public function getLines($r)
		{
				$t = new Dase_Template($r);

				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				$t->assign('v',$v);
				$lines = $v->getLines($r->get('show_hidden'));
				$t->assign('first_line',$v->getOneLine());
				$t->assign('lines',$lines);
				$t->assign('show_hidden',$r->get('show_hidden'));
				$r->renderResponse($t->fetch('lines.tpl'));
		}

		public function postToPreferredVersion($r)
		{
				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				$v->makePreferred();
				$r->renderRedirect('faculty/'.$r->get('eid').'/file/'.$r->get('uploaded_file_id').'/version/'.$v->id);
		}

		public function postToLines($r)
		{
				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				$v->generateLines($this->user->eid);
				$r->renderRedirect('faculty/'.$r->get('eid').'/file/'.$r->get('uploaded_file_id').'/version/'.$v->id.'/lines');
		}

		public function getVersion($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				if ($v->load($r->get('id'))) {
						$t->assign('v',$v);
						$r->renderResponse($t->fetch('faculty_cv_version.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
				}
		}

		public function getDiff($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('uploaded_file_id'));
				$t->assign('file',$file);

				$v = new Dase_DBO_Version($this->db);
				if ($v->load($r->get('id'))) {
						$old = explode("\n",$file->rawtext);
						$new = explode("\n",$v->text);
						$diff     = new Text_Diff('auto', array($old, $new));
					//	$renderer = new Text_Diff_Renderer_unified();
						if ($r->get('context')) {
								$renderer = new Text_Diff_Renderer_context();
						} else {
								$renderer = new Text_Diff_Renderer_inline();
						}
						$t->assign('diff',$renderer->render($diff));
						$t->assign('v',$v);
						$r->renderResponse($t->fetch('diff.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
				}
		}

		public function getFile($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());
				$file = new Dase_DBO_UploadedFile($this->db);

				if ($file->load($r->get('id'))) {
						if (!$file->rawtext_md5 || $r->get('reconvert')) {
								$file->convert();
						}
						$t->assign('versions',$file->getVersions());
						$t->assign('file',$file);
						$r->renderResponse($t->fetch('faculty_file.tpl'));
				} else {
						$r->renderRedirect('faculty/'.$fac->eid);
				}
		}

		public function postToVersioner($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));

				$v = new Dase_DBO_Version($this->db);
				$v->uploaded_file_id = $file->id;
				$v->edited_by = $this->user->eid;
				$v->faculty_eid = $fac->eid;
				$text = $r->get('text');
				//if text, it is spawned from a version
				//else it is a new version of undeited text
				if ($text) {
						if ($r->get('delete_short')) {
								$new_lines = array();
								foreach (preg_split('/(\r\n|\r|\n)/', $text) as $line) {
										$this_line = trim($line);
										if ($this_line && strlen($this_line) >= $r->get('shortline_length')) {
												$new_lines[] = $this_line;
										}
								}
								$text = join("\n\n",$new_lines);
						}
						if ($r->get('add_space')) {
								$new_lines = array();
								foreach (preg_split('/(\r\n|\r|\n)/', $text) as $line) {
										$this_line = trim($line);
										if ($this_line) {
												$new_lines[] = $this_line;
										}
								}
								$text = join("\n\n",$new_lines);
						}
						if ($r->get('remove')) {
								$i = 0;
								foreach (preg_split('/(\r\n|\r|\n)/', $text) as $line) {
										$this_line = trim($line);
										if ($this_line) {
												if (!isset($lines[$i])) {
														$lines[$i] = '';
												}
												$lines[$i] .= $this_line;
												$lines[$i] .= ' ';
										} else {
												$i++;
										}
								}
								$new_lines = array();
								foreach ($lines as $ln) {
										$ln = preg_replace('/\xc2\xa0/',' ',$ln);
										$ln = preg_replace('/\s\s+/',' ', $ln);
										$new_lines[] = trim($ln);
								}
								$text = join("\n\n",$new_lines);
								$v->linebreaks_removed = 1;
						}
						$v->text = $text;
						$v->text_md5 = md5($text);
						$v->note = $r->get('note');
				} else {
						$v->text = $file->rawtext;
						$v->text_md5 = $file->rawtext_md5;
						$v->note = 'unedited copy of CV';
				}
				$v->timestamp = date(DATE_ATOM);
				$v->insert();
				$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
		}

		public function postToMetadata($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				$file->date_on_cv = $r->get('date_on_cv');
				$file->status = $r->get('status');
				$file->note = $r->get('note');
				$file->update();

				$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
		}

		public function postToProblem($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();

				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				$file->problem_note = $r->get('problem_note');
				if ($file->problem_note) {
						$file->has_problem = 1;
				} else {
						$file->has_problem = 0;
				}
				$file->update();
				$r->renderRedirect('faculty/'.$fac->eid.'/file/'.$file->id);
		}

		public function postToFacultyProblem($r) 
		{
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$fac->findOne();
				$fac->problem_note = $r->get('problem_note');
				if ($fac->problem_note) {
						$fac->has_problem = 1;
				} else {
						$fac->has_problem = 0;
				}
				$fac->update();
				$r->renderRedirect('faculty/'.$fac->eid);
		}

		public function postToPreferred($r) 
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				$file->makePreferred();
				$r->renderRedirect('faculty/'.$r->get('eid').'/file/'.$file->id);
		}

		public function deleteVersion($r) 
		{
				//todo: really need to check here to see if 
				//work has been done on the lines
				$v = new Dase_DBO_Version($this->db);
				$v->load($r->get('id'));
				foreach ($v->getLines() as $line) {
						$line->delete();
				}
				$v->delete();
				$r->renderOk();
		}

		public function deleteFile($r) 
		{
				$file = new Dase_DBO_UploadedFile($this->db);
				$file->load($r->get('id'));
				if (!count($file->getVersions()) && $this->user->eid == $file->uploaded_by) {
						$file->delete();
				}
				$r->renderOk();
		}

		public function getFaculty($r) 
		{
				$t = new Dase_Template($r);
				$fac = new Dase_DBO_Faculty($this->db);
				$fac->eid = $r->get('eid');
				$t->assign('fac',$fac->findOne());

				$pref_versions = array();
				foreach ($fac->getVersions() as $v) {
						if ($v->is_preferred) {
								$v->isFromPrefCv();
								$pref_versions[] = $v;
						}
				}

				$t->assign('pref_versions',$pref_versions);

				$files = new Dase_DBO_UploadedFile($this->db);
				$files->eid = $fac->eid;
				$set = array();
				foreach ($files->findAll(1) as $f) {
						$f->getVersions();
						$set[] = $f;
				}
				if (Dase_DBO_Watchlist::check($this->db,$this->user->eid,$fac->eid)) {
						$t->assign('on_watchlist',1);
				}
				$t->assign('files',$set);
				$r->renderResponse($t->fetch('faculty.tpl'));
		}

}

