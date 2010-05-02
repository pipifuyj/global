<?php
require_once("ModelSQLStore.php");
class ModelDBStore extends ModelSQLStore{
	public $ids;
	public $tables;
	public $Fields;
	public $relation;
	public $addRelation=true;
	public $commitRelation=true;
	public $removeRelation=false;
	public function construct(){
		if(!$this->ids)$this->ids=array($this->id);
		if(!$this->sql)$this->sql=$this->model->framework->sql;
		if(!$this->tables){
			if(!$this->table)$this->table=strtolower($this->model->id);
			$this->tables=array($this->table);
		}
		if(!$this->Fields){
			if(!$this->fields)foreach($this->model->fields as $field)$this->fields[]=$field->mapping;
			$this->Fields=array($this->fields);
		}
		if(!$this->relation){
			$this->relation=array();
			foreach($this->ids as $index=>$id){
				$this->relation[]="`{$this->tables[$index]}`.`{$this->ids[$index]}`";
			}
			$this->relation=implode("=",$this->relation);
		}
		$index=0;
		foreach($this->Fields as $i=>$fields)foreach($fields as $j=>$mapping){
			$this->model->fields[$index]->mapping=array($this->tables[$i],$mapping);
			$index++;
		}
		$mappings=array();
		$formats=array();
		$sets=array();
		$fields=array();
		foreach($this->model->fields as $field){
			$mappings[$field->mapping[0]][]=$field->mapping[1];
			$formats[$field->mapping[0]][]="'%s'";
			$sets[$field->mapping[0]][]="`{$field->mapping[1]}`='%s'";
			$fields[]="`{$field->mapping[0]}`.`{$field->mapping[1]}` as `{$field->name}`";
		}
		foreach($this->ids as $index=>$id){
			$table=$this->tables[$index];
			$this->_insert[$table]="insert into `$table` (`".implode("`,`",$mappings[$table])."`)values(".implode(",",$formats[$table]).")";
			$this->_update[$table]="update `$table` set ".implode(",",$sets[$table])." where `{$this->ids[$index]}`='%s' limit 1";
			$fields[]="`$table`.`{$this->ids[$index]}` as `_id_$index`";
		}
		$this->_select_fields=implode(",",$fields);
		$this->_select_from="`".implode("`,`",$this->tables)."`";
		$this->_select_where="{$this->where} and {$this->relation}";
		$this->_select="select %s from {$this->_select_from} where {$this->_select_where}";
		ModelStore::construct();
	}
	public function add(&$record){
		$values=array();
		foreach($this->model->fields as $field){
			$values[$field->mapping[0]][]=$record->get($field->name);
		}
		foreach($this->Fields as $index=>$fields){
			$table=$this->tables[$index];
			$this->sql->query($this->_insert[$table],$values[$table]);
			if($this->sql->affectedRows()==1){
				$record->data["_id_$index"]=$this->sql->insertId();
			}else{
				return false;
			}
		}
		$record->id=$record->data['_id_0'];
		return true;
	}
	public function commit($record){
		$values=array();
		foreach($this->model->fields as $field){
			$values[$field->mapping[0]][]=$record->get($field->name);
		}
		foreach($this->Fields as $index=>$fields){
			$table=$this->tables[$index];
			$this->sql->query($this->_update[$table],$values[$table],$record->data["_id_$index"]);
			if($this->sql->affectedRows()<0)return false;
		}
		return true;
	}
	public function remove($record){
		if($this->removeRelation)foreach($this->Fields as $index=>$fields){
			$table=$this->tables[$index];
			$this->sql->query("delete from `$table` where `{$this->ids[$index]}`='%s' limit 1",$record->data["_id_$index"]);
			if($this->sql->affectedRows()<0)return false;
		}else{
			$this->sql->query("delete from `{$this->tables[0]}` where `{$this->ids[0]}`='%s' limit 1",$record->data["_id_0"]);
			return $this->sql->affectedRows()==1;
		}
		return true;
	}
	public function collect($key,$filters=array()){
		$key=$this->model->field($key);
		$where=$this->parseFilters($filters);
		$this->sql->query("$this->_select and $where","distinct(`{$key->mapping[0]}`.`{$key->mapping[0]}`) as `{$key->name}`");
		$collect=array();
		while($row=$this->sql->getRow()){
			$collect[]=$row[$key->name];
		}
		return $collect;
	}
	public function parseFilters($filters=array()){
		foreach($filters as &$filter){
			$filter[0]=$this->model->field($filter[0])->mapping;
			$filter="`{$filter[0][0]}`.`{$filter[0][1]}` like '{$filter[1]}'";
		}
		$where=implode(" and ",$filters);
		if(!$where)$where="true";
		return $where;
	}
}
?>
