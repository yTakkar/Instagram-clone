<?php
$parent_dir = dirname( dirname(__FILE__) );
	$dir = $parent_dir.'/config/class/';
	$disk_scan = array_diff(scandir($dir,1),array('..','.'));
 foreach($disk_scan as $files){
	 require_once "class/".$files;
 }
?>
