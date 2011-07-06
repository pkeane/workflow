<?php


include 'config.php';


$facs = new Dase_DBO_Faculty($db);
/*
$facs->eid = 'bussdm';
$facs->findOne();
$facs->dedupLines('pkeane');
exit;
 */

foreach ($facs->find() as $fac) {
		$fac = clone($fac);
		print "working on $fac->firstname $fac->lastname\n";
		print $fac->dedupLines('pkeane')." possible dups\n";
}


/*

foreach ($facs->find() as $fac) {
		$fac = clone($fac);
		print "working on $fac->firstname $fac->lastname\n";
		$np_lines = array();
		$pref_lines = array();
		foreach ($fac->getVersions() as $v) {
				if (!$v->has_citations) {
						$v->generateLines('pkeane');
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
				if ($pref_line->poss_dups_count) {
						$pref_line->poss_dups_count = 0;
						$pref_line->update();
				}
				foreach ($np_lines as $np_line) {
						$plen = strlen(trim($pref_line->text));
						$nplen = strlen(trim($np_line->text));
						//print $plen."/".$nplen."\n";
						//print "====================".$np_line->is_dup_of."\n";
						//ignore lines we know are dups
						if (1 || !$np_line->is_dup_of) {

								if (trim($pref_line->text) == trim($np_line->text)) {
										//print "\n ****************** dup\n";
										$np_line->is_dup_of = $pref_line->id;
										$np_line->update();
								} else {
										//print "\n else *****************\n";
										$test_p = str_replace('- ','-',$pref_line->text);
										$test_np = str_replace('- ','-',$np_line->text);
										if ($test_p == $test_np) {
												print "DASH SPACE !! \n";
												print $pref_line->text."\n";
												print $np_line->text."\n";
												$pref_line->text = $test_p;
												$pref_line->update();
												$np_line->text = $test_p;
												$np_line->is_dup_of = $pref_line->id;
												$np_line->update();
										} else {
												if ($plen > 40 && $plen < 255 && $nplen < 255 ) {
														$lev = levenshtein(trim($pref_line->text),trim($np_line->text));
														//print $lev."\n";
														if ($lev > 0 && $lev < 20) {
																//avoid headers
																$np_line->is_poss_dup_of = $pref_line->id;
																$np_line->levenshtein = $lev;
																$np_line->is_hidden = 1;
																$np_line->update();

																print "possible dup\n";

																$pref_line->poss_dups_count += 1;
																$pref_line->update();
														}
												}
										}
								}
						}
				}
		}
}
 */
