<?php
class Model{
	public $framework=null;
	public $id="";
	public $fields=array();
	public function hasKey($key){
		return in_array($key,$this->keys);
	}
	public function record($data=array(),$id=null){
		require_once("ModelRecord.php");
		$name="{$this->id}ModelRecord";
		require_once("{$this->framework->path}/model/$name.php");
		$record=new $name();
		$record->model=&$this;
		$record->construct($data,$id);
		return $record;
	}
	public function store(){
		require_once("ModelStore.php");
		$name="{$this->id}ModelStore";
		require_once("{$this->framework->path}/model/$name.php");
		$store=new $name();
		$store->model=&$this;
		$store->construct();
		return $store;
	}
}
?>
