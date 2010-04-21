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
		$sets=array();
		$fields=array();
		foreach($this->model->fields as $index=>$key){
			$formats[]="'%s'";
			$sets[]="`{$this->fields[$index]}`='%s'";
			$fields[]="`{$this->fields[$index]}` as `{$key}`";
		}
		$this->_insert="insert into `{$this->table}` (`".implode("`,`",$this->fields)."`)values(".implode(",",$formats).")";
		$this->_update="update `{$this->table}` set ".implode(",",$sets)." where `{$this->id}`='%s' limit 1";
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
	public function commit($record){
		$values=array();
		foreach($this->model->fields as $key){
			$values[]=$record->get($key);
		}
		$this->sql->query($this->_update,$values,$record->id);
		return $this->sql->affectedRows()==1;
	}
	public function remove($record){
		$this->sql->query("delete from `{$this->table}` where `{$this->id}`='%s' limit 1",$record->id);
		return $this->sql->affectedRows()==1;
	}
	public function filter($filters,$start=0,$limit=0){
		foreach($filters as &$filter){
			$filter="`{$filter[0]}` like '{$filter[1]}'";
		}
		return $this->where(implode(" and ",$filters),"",$start,$limit);
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
