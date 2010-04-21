<?php
class View{
	public $framework=null;
	public function index($request=null,$session=null){
	}
	public function __call($method,$args){
		$this->index();
	}
}
?>