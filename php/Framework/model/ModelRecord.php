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
	public function add(){
		return $this->model->store()->add($this);
	}
	public function commit(){
		return $this->model->store()->commit($this);
	}
	public function save(){
		if($this->id)$this->commit();
		else $this->add();
	}
	public function remove(){
		return $this->model->store()->remove($this);
	}
	public function isValid(){
		foreach($this->model->fields as $field){
			if(!$field->allowBlank&&!$this->get($field->name))return false;
		}
		return true;
	}
}
?>
