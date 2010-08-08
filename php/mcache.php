<?php
class mcache{
	public function mcache(){
	}
	public function add($key,$var){
		if($this->has($key))return false;
		return $this->set($key,$var);
	}
	public function decrement($key,$int=1){
		$var=$this->get($key);
		$var-=$int;
		if($this->set($key,$var))return $var;
		return false;
	}
	public function delete($key,$timeout=null){
	}
	public function flush(){
	}
	public function get($key){
	}
	public function has($key){
	}
	public function increment($key,$int=1){
		return $this->decrement($key,-$int);
	}
	public function replace($key,$var){
		if($this->has($key))return $this->set($key,$var);
		return false;
	}
	public function set($key,$var){
	}
}
