<?php


include 'config.php';


$uf = new Dase_DBO_UploadedFile($db);

$base = '/mnt/www-data/publications/by_eid/';
foreach ($uf->findAll(1) as $file) {
		$path = $base.$file->eid.'/'.$file->name;
		if (file_exists($path)) {
			//	print "OK\n";
				/*
				$size = filesize($path);
				print $size."\n";
				if ($size < 5000) {
						print $path."\n";
				}
				 */
				$db_size = $file->rawtext_size."\n";
				if ($db_size < 100) {
						print $path."\n";
				}
		} else {
				print "missing $path\n";
				//$file->delete();
		}
}

