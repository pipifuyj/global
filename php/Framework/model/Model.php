<?php
class Model{
	public $_record="ModelRecord";
	public $_store="ModelStore";
	public $framework=null;
	public $id="";
	public $fields=array();
	function construct(){
		foreach($this->fields as &$field){
			if(is_string($field))$field=new ModelField($field);
			elseif(is_array($field)){
				$name="Model{$field['type']}Field";
				$field=new $name($field);
			}
		}
	}
	public function hasField($name){
		foreach($this->fields as $field){
			if($field->name==$name)return true;
		}
		return false;
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
