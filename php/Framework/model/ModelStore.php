<?php
class ModelStore{
	public $model=null;
	public $id="id";
	public function construct(){
	}
	public function add($record){
		return true;
	}
	public function query($key,$value,$start=0,$limit=0){
		$records=array();
		return $records;
	}
	public function find($key,$value){
		$records=$this->query($key,$value,0,1);
		return $records[0];
	}
	public function getById($id){
		return $this->find($this->id,$id);
	}
}
?>
