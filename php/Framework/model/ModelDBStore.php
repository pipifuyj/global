<?php
require_once("ModelSQLStore.php");
class ModelDBStore extends ModelSQLStore{
	public $ids;
	public $tables;
	public $Fields;
	public $autoAdd=true;
	public $autoCommit=true;
	public $autoRemove=false;
	public function construct(){
		if(!$this->ids)$this->ids=array($this->id);
		if(!$this->sql)$this->sql=$this->model->framework->sql;
		if(!$this->tables){
			if(!$this->table)$this->table=strtolower($this->model->id);
			$this->tables=array($this->table);
		}
		$this->tables[0]=array($this->tables[0]);
		$this->_join=array();
		for($i=1,$ii=count($this->tables);$i<$ii;$i++){
			$table=&$this->tables[$i];
			if(!is_array($table))$table=array($table);
			$count=count($table);
			if($count==1){
				$table[1]="`{$table[0]}`.`{$this->ids[$i]}`=`{$this->tables[0][0]}`.`{$this->ids[0]}`";
				$table[2]="inner";
			}elseif($count==2){
				$table[2]="inner";
			}
			$this->_join[]="{$table[2]} join `{$table[0]}` on {$table[1]}";
			unset($table);
		}
		$this->_join=implode(" ",$this->_join);
		if(!$this->Fields){
			if(!$this->fields)foreach($this->model->fields as $field)$this->fields[]=$field->mapping;
			$this->Fields=array($this->fields);
		}
		$index=0;
		foreach($this->Fields as $i=>$fields)foreach($fields as $j=>$mapping){
			$this->model->fields[$index]->mapping=array($this->tables[$i][0],$mapping);
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
			$table=$this->tables[$index][0];
			$this->_insert[$table]="insert into `$table` (`".implode("`,`",$mappings[$table])."`)values(".implode(",",$formats[$table]).")";
			$this->_update[$table]="update `$table` set ".implode(",",$sets[$table])." where `{$this->ids[$index]}`='%s' limit 1";
			$fields[]="`$table`.`{$this->ids[$index]}` as `_id_$index`";
		}
		$this->_select_fields=implode(",",$fields);
		$this->_select_from="`{$this->tables[0][0]}` {$this->_join}";
		$this->_select_where=$this->where;
		$this->_select="select %s from {$this->_select_from} where {$this->_select_where}";
		ModelStore::construct();
	}
	public function add(&$record){
		$values=array();
		foreach($this->model->fields as $field){
			$values[$field->mapping[0]][]=$record->get($field->name);
		}
		foreach($this->Fields as $index=>$fields){
			$table=$this->tables[$index][0];
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
			$table=$this->tables[$index][0];
			$this->sql->query($this->_update[$table],$values[$table],$record->data["_id_$index"]);
			if($this->sql->affectedRows()<0)return false;
		}
		return true;
	}
	public function remove($record){
		if($this->autoRemove)foreach($this->Fields as $index=>$fields){
			$table=$this->tables[$index][0];
			$this->sql->query("delete from `$table` where `{$this->ids[$index]}`='%s' limit 1",$record->data["_id_$index"]);
			if($this->sql->affectedRows()<0)return false;
		}else{
			$this->sql->query("delete from `{$this->tables[0][0]}` where `{$this->ids[0]}`='%s' limit 1",$record->data["_id_0"]);
			return $this->sql->affectedRows()==1;
		}
		return true;
	}
	public function mapping($key){
		$mapping=$this->model->field($key)->mapping;
		return "`{$mapping[0]}`.`{$mapping[1]}`";
	}
}
?>