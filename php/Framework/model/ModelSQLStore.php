<?php
require_once("ModelStore.php");
class ModelSQLStore extends ModelStore{
	public $sql;
	public $table;
	public $fields;
	public function construct(){
		if(!$this->sql)$this->sql=$this->model->framework->sql;
		if(!$this->table)$this->table=strtolower($this->model->id);
		if(!$this->fields)$this->fields=$this->model->fields;
		$formats=array();
		$fields=array();
		foreach($this->model->fields as $index=>$key){
			$formats[]="'%s'";
			$fields[]="`{$this->fields[$index]}` as `{$key}`";
		}
		$this->_insert="insert into `{$this->table}` (`".implode("`,`",$this->fields)."`)values(".implode(",",$formats).")";
		$this->_select="select `{$this->id}`,".implode(",",$fields)." from `{$this->table}` where";
		parent::construct();
	}
	public function add($record){
		$values=array();
		foreach($this->model->fields as $index=>$key){
			$values[]=$record->get($key);
		}
		$this->sql->query($this->_insert,$values);
		return $this->sql->affectedRows()==1;
	}
	public function query($key,$value,$start=0,$limit=0){
		return $this->where("`$key` like '$value'","",$start,$limit);
	}
	public function where($where,$order="",$start=0,$limit=0){
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
