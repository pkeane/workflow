<?php

require_once 'Dase/DBO.php';

/*
 * DO NOT EDIT THIS FILE
 * it is auto-generated by the
 * script 'bin/class_gen.php
 * 
 */

class Dase_DBO_Autogen_Version extends Dase_DBO 
{
	public function __construct($db,$assoc = false) 
	{
		parent::__construct($db,'version', array('edited_by','text','text_md5','timestamp','note','uploaded_file_id','has_citations','linebreaks_removed'));
		if ($assoc) {
			foreach ( $assoc as $key => $value) {
				$this->$key = $value;
			}
		}
	}
}