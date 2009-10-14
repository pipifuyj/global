<?php
require_once(dirname(__file__)."/abstract/sql.php");
class sqlite extends sql{
	var $__charset="utf-8";
	var $_charset="utf-8";
	var $_offset;
	//begin of abstract methods
	function affectedRows(){
		return sqlite_changes($this->id);
	}
	function close(){
		return sqlite_close($this->id);
	}
	function connect($server,$username,$password){
		return sqlite_open($server,$username,$password);
	}
	function escapeString($string){
		return sqlite_escape_string($string);
	}
	function execute($sql){
		return sqlite_query($sql,$this->id);
	}
	function fetchField($result,$offset=null){
		if($offset===null)$offset=$this->_offset++;
		return @sqlite_field_name($result,$offset);
	}
	function fetchRow($result){
		return sqlite_fetch_array($result,SQLITE_ASSOC);
	}
	function free($result){
	}
	function fetchAllTableNames($database=null){
		$result=$this->execute("select name from sqlite_master where type='table'");
		$names=array();
		while($row=$this->fetchRow($result)){
			$names[]=$row['name'];
		}
		return $names;
	}
	function fetchColumn($table,$column){
		$types=sqlite_fetch_column_types($table,$this->id,SQLITE_ASSOC);
		return array(
			"name"=>$column,
			"key"=>&$row['Key'],
			"type"=>&$types[$column],
			"default"=>&$row['Default'],
			"comment"=>&$row['Comment']
			);
	}
	function fetchAllColumnNames($table){
		return array_keys(sqlite_fetch_column_types($table,$this->id,SQLITE_ASSOC));
	}
	function insertId(){
		return sqlite_last_insert_rowid($this->id);
	}
	function numRows($result){
		return @sqlite_num_rows($result);
	}
	function numFields($result){
		return @sqlite_num_fields($result);
	}
	function setCharset($charset){
		$this->_charset=$charset;
	}
	function setDatabase($database){
	}
	//end of abstract methods
	function getAllColumns($table){
		$types=sqlite_fetch_column_types($table,$this->id,SQLITE_ASSOC);
		$columns=array();
		foreach($types as $column=>$type){
			$columns[$column]=array(
				"name"=>$column,
				"key"=>&$row['Key'],
				"type"=>&$types[$column],
				"default"=>&$row['Default'],
				"comment"=>&$row['Comment']
				);
		}
		return $columns;
	}
	function fetchAllRows($result){
		return sqlite_fetch_all($result,SQLITE_ASSOC);
	}
	//end of rewriting methods
}
?>
