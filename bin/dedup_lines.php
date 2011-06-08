<?php


include 'config.php';

$lines = new Dase_DBO_Line($db);

$fac = array();
foreach ($lines->find() as $l) {
		$l = clone($l);
		if (!isset($fac[$l->faculty_eid])) {
				$fac[$l->faculty_eid] = array();
		}
		$fac[$l->faculty_eid][] = $l;
}

foreach ($fac as $eid => $lines) {
		$new_lines = $lines;
		foreach ($lines as $line) {
				$testline = array_pop($new_lines);
				foreach ($new_lines as $t2line) {
						if (strlen($testline->text) < 255 && strlen($t2line->text) < 255) {
								if (strlen($testline->text) > 25 && strlen($t2line->text) > 25) {
										$lev = levenshtein($testline->text,$t2line->text);
										if ($lev > 0 && $lev < 10) {
												print "\nDUP:\n";
												print $testline->id." : ".$testline->text."\n";
												print $t2line->id." : ".$t2line->text."\n";
										}
								}
						}
				}
		}
}
