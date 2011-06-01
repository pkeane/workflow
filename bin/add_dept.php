<?php

include 'config.php';

$facs = new Dase_DBO_Faculty($db);




foreach ($facs->findAll(1) as $f)  {

		print $f->eid."\n";

		$data = file_get_contents('https://dase.laits.utexas.edu/search.json?c=ut_faculty&q=eid:'.$f->eid);
		$pdata = Dase_Json::toPhp($data);
		print "\n------------------\n";
		$f->dept = $pdata['items'][0]['metadata']['dept-short-title-primary'][0];
		$f->college = $pdata['items'][0]['metadata']['coll-title-primary'][0];
		$f->update();
		
		/*
		$rec  = Utlookup::getRecord($f->eid);
		$f->dept = $rec['unit'];
		 */
}

