<?php
/**
framework
	model
		default.php
		Model.php
		ModelStore.php
		ModelRecord.php
		ObjectModel.php
		ObjectModelStore.php
		ObjectModelRecord.php
	view
		default.php
	controller
		default.php
			
	plugin
		test
			beforeEvent.php
			Event.php
			afterEvent.php


*/
class framework{
	public $id;
	public $path;
	
	public $actions=array();
	public $plugins=array();
	
	public $base="";
	public $askmark="?";
	public $eqnmark="=";
	public $andmark="&";
	public $baseParams=array();
	
	public $value=array();
	public $action="default";
	public $method="Index";
	
	public $stdout=array();
	public $stderr=array();
	
	public function framework($id,$path="framework"){
		global $_FRAMEWORK;
		$this->id=$id;
		$_FRAMEWORK[$id]=&$this;
		$this->path=realpath($path);
		$this->setPlugins("{$this->path}/plugin");
	}
	public function getInstance($id){
		global $_FRAMEWORK;
		return $_FRAMEWORK[$id];
	}
	public function setPlugins($path){
		$path=realpath($path);
		$plugins=@scandir($path);
		while(list($k,$v)=@each($plugins)){
			//avoid ., .., .svn, ....
			if(substr($v,0,1)!=".")$this->plugins["$path/$v"]=scandir("$path/$v");
		}
	}
	public function getModel($id){
		require_once("Framework/model/ModelField.php");
		require_once("Framework/model/Model.php");
		$name="{$id}Model";
		require_once("{$this->path}/model/$name.php");
		$model=@new $name();
		$model->framework=&$this;
		$model->id=$id;
		$model->construct();
		return $model;
	}
	public function toUrl($value=array()){
		$value=array_merge($this->baseParams,$value);
		$s="{$this->base}{$this->askmark}";
		while(list($k,$v)=each($value)){
			$v=urlencode($v);
			$s.="$k{$this->eqnmark}$v{$this->andmark}";
		}
		return $s;
	}
	//Main
	public function main(){
		//Initialize
		$framework_flag=$this->BAEvent("Initialize","before");
		if(preg_match("/^([A-Za-z0-9]+)/",$_SERVER['QUERY_STRING'],$framework_t)){
			$this->action=$framework_t[1];
		}
		$this->value=$_REQUEST;
		if($this->value[$this->action])$this->method=$this->value[$this->action];
		if($framework_flag)$framework_flag=$this->BAEvent("Initialize");
		if($framework_flag)$framework_flag=$this->BAEvent("Initialize","after");
		//Route
		$framework_flag=$this->BAEvent("Route","before");
		$framework_path="{$this->path}/model/{$this->action}.php";
		if(is_file($framework_path)){
			require($framework_path);
		}
		require_once("Framework/controller/Controller.php");
		$framework_path="{$this->path}/controller/{$this->action}.php";
		if(is_file($framework_path)){
			require_once($framework_path);
		}
		$framework_t="{$this->action}Controller";
		if(class_exists($framework_t)&&is_subclass_of($framework_t,"Controller")){
			$this->controller=new $framework_t();
		}else{
			$this->controller=new Controller();
		}
		$this->controller->framework=&$this;
		$this->controller->{$this->method}();
		if($framework_flag)$framework_flag=$this->BAEvent("Route");
		if($framework_flag)$framework_flag=$this->BAEvent("Route","after");
		//Render
		$framework_flag=$this->BAEvent("Render","before");
		require_once("Framework/view/View.php");
		$framework_path="{$this->path}/view/{$this->action}.php";
		if(is_file($framework_path)){
			require_once($framework_path);
		}else{
			$framework_path="{$this->path}/view/default.php";
			if(is_file($framework_path)){
				require($framework_path);
			}
		}
		$framework_t="{$this->action}View";
		if(class_exists($framework_t)&&is_subclass_of($framework_t,"View")){
			$this->view=new $framework_t();
		}else{
			$this->view=new View();
		}
		$this->view->framework=&$this;
		$this->view->{$this->method}();
		if($framework_flag)$framework_flag=$this->BAEvent("Render");
		if($framework_flag)$framework_flag=$this->BAEvent("Render","after");
		//Output
		$framework_flag=$this->BAEvent("Output","before");
		echo implode("",$this->stdout);
		if($framework_flag)$framework_flag=$this->BAEvent("Output");
		if($framework_flag)$framework_flag=$this->BAEvent("Output","after");
	}
	//Event mechanism
	private function BAEvent($framework_eventName,$framework_BA=""){
		$framework_flag=true;
		reset($this->plugins);
		while(list($framework_k,$framework_v)=each($this->plugins)){
			$framework_path="$framework_k/$framework_BA$framework_eventName.php";
			if(is_file($framework_path)){
				$framework_flag=require($framework_path);
				if($framework_flag){
					continue;
				}else break;
			}
		}
		return $framework_flag;
	}
}
?>
