<?php

require_once 'Dase/DBO/Autogen/UploadedFile.php';

class Dase_DBO_UploadedFile extends Dase_DBO_Autogen_UploadedFile 
{

		public $versions = array();

		public function __get($key)
		{
				if ('ago' == $key) {
						$ts = strtotime($this->uploaded);
						$now = time();
						$diff = $now - $ts;
						$ago['days'] = $diff/(3600*24);
						$ago['hours'] = $diff/3600;
						$ago['minutes'] = $diff/60;
						$ago['seconds'] = $diff;
						foreach ($ago as $str => $num) {
								if ($num > 2) {
										$num = floor($num);
										return "$num $str ago";
								}
						}
				}
				return parent::__get($key);
		}

		public function getVersions()
		{
				$v = new Dase_DBO_Version($this->db);
				$v->uploaded_file_id = $this->id;
				$v->orderBy('timestamp DESC');
				$this->versions = $v->findAll(1);
				return $this->versions;
		}

		public function file2text($filename,$new_path,$mime)
		{
				$rawtext = '';
				if ('application/pdf' == $mime) {
						copy($filename,$new_path);
						$outfile = $new_path.'.txt';
						$cmd = 'pdftotext -enc UTF-8 -layout "'.$new_path.'" "'.$outfile.'"';
						print system($cmd);
						$rawtext = @file_get_contents($outfile);
				}

				if ('application/msword' == $mime) {
						copy($filename,$new_path);
						$outfile = $new_path.'.txt';
						$cmd = 'antiword -m UTF-8 '.$new_path.' > '.$outfile;
						print system($cmd);
						$rawtext = @file_get_contents($outfile);
				}

				if ('application/applefile' == $mime) {
						copy($filename,$new_path);
						$outfile = $new_path.'.txt';
						$cmd = 'antiword -m UTF-8 '.$new_path.' > '.$outfile;
						print system($cmd);
						$rawtext = @file_get_contents($outfile);
				}

				if ('application/vnd.openxmlformats-officedocument.wordprocessingml.document' == $mime) {
						$new_path = str_replace('.unknown','.docx',$new_path);
						copy($filename,$new_path);
						$outfile = str_replace('.docx','.txt',$new_path);
						$cmd = '/usr/bin/perl '.BASE_PATH.'/bin/docx2txt.pl "'.$new_path.'" "'.$outfile.'"';
						print system($cmd);
						$rawtext = @file_get_contents($outfile);
				}
				if (!$rawtext) {
						$rawtext = "no text";
				}
				return $rawtext;
		}

		public function convert()
		{
				$filename = '/mnt/www-data/publications/by_eid/'.$this->eid.'/'.$this->name;
				$new_path = '/mnt/www-data/publications/processing/'.$this->eid.'_'.$this->name;
				$mime = $this->upload_mime;
				$rawtext = $this->file2text($filename,$new_path,$mime);
				$this->rawtext = $rawtext;
				$this->rawtext_md5 = md5($rawtext);
				$this->rawtext_size = strlen($rawtext);
				$this->converted = date(DATE_ATOM);
				$this->workflow_state = 'converted';
				$this->update();

		}
}
