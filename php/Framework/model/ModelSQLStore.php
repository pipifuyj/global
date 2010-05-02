<?php
require_once("ModelStore.php");
class ModelSQLStore extends ModelStore{
	public $sql;
	public $table;
	public $fields=array();
	public $where="true";
	public function construct(){
		if(!$this->sql)$this->sql=$this->model->framework->sql;
		if(!$this->table)$this->table=strtolower($this->model->id);
		foreach($this->fields as $index=>$mapping){
			$this->model->fields[$index]->mapping=$mapping;
		}
		$mappings=array();
		$formats=array();
		$sets=array();
		$fields=array();
		foreach($this->model->fields as $field){
			$mappings[]=$field->mapping;
			$formats[]="'%s'";
			$sets[]="`{$field->mapping}`='%s'";
			$fields[]="`{$field->mapping}` as `{$field->name}`";
		}
		$fields[]="`{$this->id}`";
		$this->_insert="insert into `{$this->table}` (`".implode("`,`",$mappings)."`)values(".implode(",",$formats).")";
		$this->_update="update `{$this->table}` set ".implode(",",$sets)." where `{$this->id}`='%s' limit 1";
		$this->_select_fields=implode(",",$fields);
		$this->_select_from="`{$this->table}`";
		$this->_select_where=$this->where;
		$this->_select="select %s from {$this->_select_from} where {$this->_select_where}";
		parent::construct();
	}
	public function add(&$record){
		$values=array();
		foreach($this->model->fields as $field){
			$values[]=$record->get($field->name);
		}
		$this->sql->query($this->_insert,$values);
		if($this->sql->affectedRows()==1){
			$record->id=$this->sql->insertId();
			return true;
		}else return false;
	}
	public function commit($record){
		$values=array();
		foreach($this->model->fields as $field){
			$values[]=$record->get($field->name);
		}
		$this->sql->query($this->_update,$values,$record->id);
		return $this->sql->affectedRows()==1;
	}
	public function remove($record){
		$this->sql->query("delete from `{$this->table}` where `{$this->id}`='%s' limit 1",$record->id);
		return $this->sql->affectedRows()==1;
	}
	public function filter($filters=array(),$start=0,$limit=0){
		return $this->where($this->parseFilters($filters),"",$start,$limit);
	}
	public function collect($key,$filters=array()){
		$mapping=$this->mapping($key);
		$field=$this->model->field($key);
		$where=$this->parseFilters($filters);
		$this->sql->query("$this->_select and $where","distinct($mapping) as `{$field->name}`");
		$collect=array();
		while($row=$this->sql->getRow()){
			$collect[]=$row[$field->name];
		}
		return $collect;
	}
	public function getTotalCount($filters=array()){
		$where=$this->parseFilters($filters);
		$this->sql->query("$this->_select and $where","count(`{$this->id}`) as `TotalCount`");
		$row=$this->sql->getRow();
		$TotalCount=$row['TotalCount'];
		return $TotalCount;
	}
	public function mapping($key){
		$mapping=$this->model->field($key)->mapping;
		return "`$mapping`";
	}
	public function parseFilters($filters=array()){
		foreach($filters as &$filter){
			$mapping=$this->mapping($filter[0]);
			$filter="$mapping like '{$filter[1]}'";
		}
		$where=implode(" and ",$filters);
		if(!$where)$where="true";
		return $where;
	}
	public function where($where="",$order="",$start=0,$limit=0){
		if(!$where)$where="true";
		if($order)$order="order by $order";
		else $order="";
		if($limit)$limit="limit $start, $limit";
		else $limit="";
		$this->sql->query("$this->_select and $where $order $limit",$this->_select_fields);
		//echo $this->sql->lastClause;
		$records=array();
		while($row=$this->sql->getRow()){
			$records[]=$this->model->record($row,$row[$this->id]);
		}
		return $records;
	}
}
?>
