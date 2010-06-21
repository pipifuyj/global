<?php
function rmdir_r($path){
	$dir=dir($path);
	while(($file=$dir->read())!==false)if($file!="."&&$file!=".."){
		$p="{$dir->path}/$file";
		if(is_dir($p)){
			rmdir_r($p);
		}else{
			unlink($p);
		}
	}
	$dir->close();
	rmdir($path);
}
?>
