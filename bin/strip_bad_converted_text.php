<?php

include 'config.php';

$uf = new Dase_DBO_UploadedFile($db);
$uf->addWhere('rawtext','','!=');
$i = 0;
foreach ($uf->findAll(1) as $u) {


		/*
		$u->rawtext = '';
		$u->rawtext_md5 = '';
		$u->rawtext_size = 0;
		 */
		//$u->update();
		if (7 == $u->rawtext_size) {
				$i++;
				print "\n=====\n";
				print $u->rawtext_md5."\n";;
				$u->convert();
				print $u->rawtext_md5."\n";;
				$u->problem_note = $u->problem_note." pk:7=raw";
				$u->has_problem = 1;
				$u->update();
				print "faculty/$u->eid/file/$u->id\n";
				foreach ($u->getVersions() as $v) {
								print "\t".$v->id."\n";
						//	$v->delete();
				}
		}
}

		print "\n$i\n";
