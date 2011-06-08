<?php


include 'config.php';

$uf = new Dase_DBO_UploadedFile($db);

$uf->upload_note = 'hb2504';

foreach ($uf->findAll(1) as $f) {
		$fac = new Dase_DBO_Faculty($db);
		$fac->eid = $f->eid;
		if (!$fac->findOne()) {
				print "$f->eid\n";
		}
}

