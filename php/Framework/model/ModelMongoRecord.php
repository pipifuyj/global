<?php
require_once("ModelRecord.php");
class ModelMongoRecord extends ModelRecord{
	public $model=null;
	public $id=null;
	public $data=array();

	public function construct($data=array(),$id=null){
		$this->id=$id;
		$this->data=$data;
	}
	public function __toString(){
		return json_encode($this->data);
	}
	public function __get($name){
		return $this->get($name);
	}
	public function travel($arr=array(),$name='id',&$res=array(),$visit='colvisit',$pre=''){
		if(is_array($arr)){
			foreach($arr as $key => $val ) {
				if ($key!==$name) {
					$this->travel($val,$name,$res,$visit,$key);
				}else $this->$visit($pre,$key,$val,$res);
			}
		} 
	}
	public function colvisit($pre,$key,$val,&$res){
		$res[$key]=$val;	
	}
	public function rowvisit($pre,$key,$val,&$res){
		$res[$pre]=$val;	
	}
	public function getCol($key){
		$result=array();
		$this->travel($this->data,$key,$result,'colvisit');
		if(count($result)==0)return false;
		else return $result;
	}
	public function getRowById($id){
		$result=array('id'=>$id);
		$this->travel($this->data,$id,$result,'rowvisit');
		if(count($result)==0)return false;
		else return $result;
	}
}
?>
