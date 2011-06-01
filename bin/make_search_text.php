<?php


include 'config.php';


$fac = new Dase_DBO_Faculty($db);
foreach ($fac->findAll() as $f) {
		$search_text = "";
		$search_text .= $f->firstname." ";
		$search_text .= $f->middlename." ";
		$search_text .= $f->lastname." ";
		$search_text .= $f->email." ";
		$search_text .= $f->dept." ";
		$search_text .= $f->college." ";
		$search_text .= $f->eid;
		$f->search_text = $search_text;
		$f->update();
}

