<?php

include 'config.php';

$uf = new Dase_DBO_UploadedFile($db);
$uf->addWhere('rawtext','','!=');
foreach ($uf->findAll(1) as $u) {


		/*
		$u->rawtext = '';
		$u->rawtext_md5 = '';
		$u->rawtext_size = 0;
		 */
		//$u->update();
		print "\n------------------------------\n";	
		print $u->rawtext_md5."\n";;
		$u->convert();
		print $u->rawtext_md5."\n";;
		foreach ($u->getVersions() as $v) {
				print "\t".$v->id."\n";
				$v->delete();
		}
}

