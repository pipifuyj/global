<?php
class PyList{
	public $_List;
	public $len;
	public function PyList(){
		$this->_List=func_get_args();
		$this->len=func_num_args();
	}
	public function append(){
		$this->extend(func_get_args());
	}
	public function count($value){
		$count=0;
		for($i=0;$i<$this->len;$i++)if($this->_List[$i]==$value)$count++;
		return $count;
	}
	public function extend($seq){
		array_unshift($seq,$this->len);
		call_user_func_array(array($this,"insert"),$seq);
	}
	public function get($index){
		if($this->isIndex($index))return $this->_List[$index];
		else throw new Exception("IndexError");
	}
	public function index($value,$from=0,$to=null){
		if($to===null)$to=$this->len;
		for($i=$from;$i<$to;$i++)if($this->_List[$i]==$value)return $i;
		throw new Exception("ValueError");
	}
	public function insert(){
		$argc=func_num_args();
		$argv=func_get_args();
		$index=$argv[0];
		$len=$argc-1;
		for($i=$this->len-1+$len,$ii=$index-1+$len;$i>$ii;$i--)$this->_List[$i]=$this->_List[$i-$len];
		for($i=$index,$ii=$index+$len;$i<$ii;$i++)$this->_List[$i]=$argv[$i-$index+1];
		$this->len+=$len;
	}
	public function isIndex($index){
		return is_int($index)&&$index>=0&&$index<$this->len;
	}
	public function pop($index=null){
		if($index===null)$index=$this->len;
		$value=$this->_List[$index];
		for($i=$index;$i<$this->len;$i++)$this->_List[$i]=$this->_List[$i+1];
		$this->len--;
		return $value;
	}
	public function remove($value){
		$this->pop($this->index($value));
	}
	public function reverse(){
		for($i=0,$ii=$this->len-1;$i<$ii;$i++,$ii--){
			$t=$this->_List[$i];
			$this->_List[$i]=$this->_List[$ii];
			$this->_List[$ii]=$t;
		}
	}
	public function set($index,$value){
		if($this->isIndex($index))$this->_List[$index]=$value;
		else throw new Exception("IndexError");
	}	
	public function slice($from,$to=null){
		if($to===null)$to=$this->len;
		$this->len=$to-$from;
		for($i=0;$i<$this->len;$i++)$this->_List[$i]=$this->_List[$i+$from];
	}
}
if(array_shift(get_included_files())==__file__){
	$list=new PyList(1,2,3,"a","b","c");
	print_r($list);
	$list->append(4,5,6);
	print_r($list);
	print_r($list->count(1));
	$list->extend(array(7,8,9));
	print_r($list);
	print_r($list->get(2));
	print_r($list->index(3));
	$list->insert(4,"h","e","l","l","o");
	print_r($list);
	print_r($list->isIndex(5));
	print_r($list->pop(6));
	print_r($list);
	$list->remove(4);
	print_r($list);
	$list->reverse();
	print_r($list);
	$list->set(8,8);
	print_r($list);
	$list->slice(6,8);
	print_r($list);
}
?>
