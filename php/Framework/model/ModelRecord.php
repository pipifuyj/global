<?php
class ModelRecord{
	public $model=null;
	public $id=null;
	public $data=array();
	public function construct($data=array(),$id=null){
		$this->id=$id;
		$this->data=$data;
	}
	public function get($key){
		return $this->data[$key];
	}
	public function set($key,$value){
		$this->data[$key]=$value;
	}
	public function commit(){
		return $this->model->store()->commit($this);
	}
}
?>
