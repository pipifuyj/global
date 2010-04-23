<?php
require_once("ModelStore.php");
class ModelSQLStore extends ModelStore{
	public $sql;
	public $table;
	public $fields=array();
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
		$this->_insert="insert into `{$this->table}` (`".implode("`,`",$mappings)."`)values(".implode(",",$formats).")";
		$this->_update="update `{$this->table}` set ".implode(",",$sets)." where `{$this->id}`='%s' limit 1";
		$this->_select="select `{$this->id}`,".implode(",",$fields)." from `{$this->table}`";
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
		foreach($filters as &$filter){
			$filter="`{$filter[0]}` like '{$filter[1]}'";
		}
		return $this->where(implode(" and ",$filters),"",$start,$limit);
	}
	public function getTotalCount($filters){
		foreach($filters as &$filter){
			$filter="`{$filter[0]}` like '{$filter[1]}'";
		}
		$where=implode(" and ",$filters);
		if($where)$where="where $where";
		$this->sql->query("select count(`{$this->id}`) as `TotalCount` from `{$this->table}` $where");
		$row=$this->sql->getRow();
		$TotalCount=$row['TotalCount'];
		return $TotalCount;
	}
	public function where($where="",$order="",$start=0,$limit=0){
		if($where)$where="where $where";
		else $where="";
		if($order)$order="order by $order";
		else $order="";
		if($limit)$limit="limit $start, $limit";
		else $limit="";
		$this->sql->query("$this->_select $where $order $limit");
		//echo $this->sql->lastClause;
		$records=array();
		while($row=$this->sql->getRow()){
			$records[]=$this->model->record($row,$row[$this->id]);
		}
		return $records;
	}
}
?>
