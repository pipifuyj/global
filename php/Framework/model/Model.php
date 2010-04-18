<?php
class Model{
	public $_record="ModelRecord";
	public $_store="ModelStore";
	public $framework=null;
	public $id="";
	public $fields=array();
	public function hasKey($key){
		return in_array($key,$this->keys);
	}
	public function record($data=array(),$id=null){
		require_once("{$this->_record}.php");
		$name="{$this->id}{$this->_record}";
		require_once("{$this->framework->path}/model/$name.php");
		$record=new $name();
		$record->model=&$this;
		$record->construct($data,$id);
		return $record;
	}
	public function store(){
		require_once("{$this->_store}.php");
		$name="{$this->id}{$this->_store}";
		require_once("{$this->framework->path}/model/$name.php");
		$store=new $name();
		$store->model=&$this;
		$store->construct();
		return $store;
	}
}
?>
