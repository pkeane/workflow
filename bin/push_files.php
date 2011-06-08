<?php

include 'config.php';

$data = Dase_Json::toPhp(file_get_contents('hb_list.json'));
print count($data);

/*
foreach ($data as $eid => $pair) {
		$name = $pair['filename'];
		$path = 'hb_cvs/'.$eid.'.pdf';
		$base_dir = "/mnt/www-data/publications/by_eid/".$eid;
		if (!file_exists($base_dir)) {
				mkdir($base_dir);
		}
		$newname = md5(time().$name).'.pdf';
		$new_path = $base_dir.'/'.$newname;
		copy($path,$new_path);
		chmod($new_path,0755);
		$cv = new Dase_DBO_UploadedFile($db);
		$cv->upload_mime = 'application/pdf';
		$cv->upload_note = 'hb2504';
		$cv->uploaded_by = 'pkeane';
		$cv->orig_name  = $name;
		$cv->name  = $newname;
		$cv->eid = $eid;
		$time = strtotime($pair['updated']);
		$cv->other_updated = date(DATE_ATOM,$time);
		$cv->uploaded = date(DATE_ATOM);
		print $name."\n";
		$cv->insert();
}
 */
