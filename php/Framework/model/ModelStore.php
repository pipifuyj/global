<?php
class ModelStore{
	public $model=null;
	public $id="id";
	public function construct(){
	}
	public function add($record){
		return true;
	}
	public function commit($record){
		return true;
	}
	public function remove($record){
		return true;
	}
	public function filter($filters,$start=0,$limit=0){
		$records=array();
		return $records;
	}
	public function query($key,$value,$start=0,$limit=0){
		return $this->filter(array(array($key,$value)),$start,$limit);
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
