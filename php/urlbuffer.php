<?php
class urlbuffer{
	var $id="urlbuffer";
	var $time_buffer;
	var $time_now;
	var $time_last=0;
	var $path_buffer;
	var $query_string;
	var $file_buffer;
	function urlbuffer($id=null,$time_buffer=60){
		if(is_string($id))$this->id=$id;
		else $this->id=$_SERVER["SCRIPT_NAME"];
		$this->time_buffer=$time_buffer;
		$this->time_now=time();
		$this->path_buffer="/tmp/".urlencode($this->id);
		//if(!file_exists($this->path_buffer))
			@mkdir($this->path_buffer);
		$this->query_string=$_SERVER['QUERY_STRING'];
		$this->file_buffer=$this->path_buffer."/~".urlencode($this->query_string);
		if(file_exists($this->file_buffer)){
			echo file_get_contents($this->file_buffer);
			ob_end_flush();
			flush();
			ob_start(create_function('$s','return "";'));
			$this->time_last=filemtime($this->file_buffer);
			if($this->time_now-$this->time_last<$this->time_buffer)exit;
		}else{
			ob_start();
		}
	}
	function save(){
		if($this->time_buffer>0){
			$f=fopen($this->file_buffer,"w");
			flock($f,LOCK_EX);
			fwrite($f,ob_get_contents());
			fclose($f);
		}
	}
}
?>
