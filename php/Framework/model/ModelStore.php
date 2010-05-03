<?php
class ModelStore{
	public $model=null;
	public $id="id";
	public $filters=array();
	public function construct(){
	}
	public function add(&$record){
		$record->id=md5(uniqid(rand(),true));
		return true;
	}
	public function commit($record){
		return true;
	}
	public function remove($record){
		return true;
	}
	public function filter($filters=array(),$start=0,$limit=0){
		$records=array();
		return $records;
	}
	public function filterBy($filters,$c="and"){
		$this->filters=$this->logic($this->filters,$filters,$c);
	}
	public function logic($filters1,$filters2,$c="and"){
		return array($filters1,$c=>$filters2);
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
	public function collect($key,$filters=array()){
		$records=$this->filter($filters);
		$collect=array();
		foreach($records as $record)$collect[]=$record->get($key);
		$collect=array_unique($collect);
		return $collect;
	}
	public function getTotalCount($filters=array()){
		return count($this->filter($filters));
	}
}
?>
