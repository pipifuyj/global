<?php
require_once("ModelStore.php");
class ModelMongoStore extends ModelStore{
	public $collection;
	public $collectionref;

	public function construct(){
		$cols=array();
		if($this->collection) $cols=$this->model->framework->mongodb->listCollections();
		$count=0;
		foreach($cols as $i=>$v) if($this->collection==$v->getName()) $count++;	
		if($count==0) $this->_create();
		if($count>1) exit("too many collections with the same name");
		$this->collectionref=$this->model->framework->mongodb->selectCollection($this->collection);
		parent::construct();
	}
	/**
	 *function: insert a document
	 *notice:
	 *(1)an empty array will not be inserted
	 *(2)duplicate array can be inserted
	 *(3)insert($doc) will assign $doc["_id"] a value, if it is not changed next time, it is impossible to insert $doc again.
	 *returns:
	 *     true=insert sucessfully
	 *     false=insert failed
	 */
	public function add($doc=array()){
		if($doc){
			unset($doc['_id']);
			return $this->collectionref->insert($doc,array('safe'=>true));	
		}else return false;
	}
	public function batchAdd($docs=array()){
		if($docs){
			foreach($docs as $doc) unset($doc['_id']);
			return $this->collectionref->batchInsert($docs,array('safe'=>true));
		}else return false;
	}
	/**
	 *function:  query this collection
	 *parameters:
	 *    $query: the fields for which to search
	 *    $order: results should be sorted by what? ASC(1) or DESC(-1)?
	 *    $fields: Fields of the results to return
	 *return:
	 *    the array of search result
	 *usage:
	 *$query example: 
	 *      array('fruit' => 'Apple') or array('x'=>2,'y'=>3)
	 *      array( '$or' => array( array('fruit' => 'Apple'), array('fruit' => 'Orange') ) 
	 *      array('x' => array( '$gt' => 5, '$lt' => 20 ))
	 *      array('type' => array('$in' => array('homepage', 'editorial')))  //$in match only when more than one element in 'type' is in the latter
	 *      array('type' => array('$all' => array('homepage', 'editorial'))) //$all specifies a minimum set of elements that must be matched.
         *      array('name' => array('$size'=>2))  //The $size operator matches any array with the specified number of elements.
	 *      array('university.name'=>'ustc')
	 *$order example:
	 *      array('_id'=>1)
	 *						  asc sort by _id
	 *      array('date'=>1,'age' => -1) 
	 *						  asc sort by date and desc sort by age
	 */
	public function where($query=array(),$fields=array(),$order=array('_id'=>1),$start=0,$limit=0){
		$cursor=$this->collectionref->find($query,$fields);
		$cursor->sort($order);
		if($limit)$cursor->limit($limit);
		if($start)$cursor->skip($start);
		$docs=array();
		foreach($cursor as $index=>$value){
			$docs[]=$this->model->record($value,$value['_id']);
			//$docs[]=$value;
		}
		return $docs;
	}
	/**
	 * @delete documents described by a given criteria
	 */
	public function remove($filters=array()){
		return $this->collectionref->remove($filters);
	} 
	/**
	 *@update documents described by a given criteria
	 *parameters:
	 *          filters: description of the objects to update
	 *          newobj: the object with which to update the matching record
	 *          array("multiple"=>true): it updates all matching documents by default
	 *usage:
	 *         array('$set'=>array('name'=>'fuyanjie'))
	 */
	public function update($filters=array(),$newdoc=array()){
		return $this->collectionref->update($filters,$newdoc,array("multiple" => true));	
	}
	/**
	 *@count document number descripbed by $query
	 *parameters:
	 *    $query: the restriction to those documents you want to count 
	 *    $limit: indicate that if the cursor limit and skip information is applicable to the count function
	 *return: the number of documents returned by the query
	 */
	public function getTotalCount($query=array(),$limit=false){
		$cursor=$this->collectionref->find($query);
		return $cursor->count($limit);
	}
	public function getCurrCount(){
		return $this->collectionref->count();
	}
	/**
	 *@get collection name
	 */
	public function getCollectionName(){
		return $this->collectionref->getName();
	}
	/**
	 * @truncate all the documents
	 */
	public function truncate(){
		return $this->remove();
	} 
	/**
	 * @query the collection index
	 */
	public function getIndex(){
		return $this->collectionref->getIndexInfo();
	}
	/**
	 *warming: drop the collection in mongodb
	 */
	private function _drop(){
		$result=$this->collectionref->drop();
		if($result['ok']) return true;
		else return false;
	}
	/**
	 *warming: drop the collection in mongodb
	 */
	private function _create(){
		return $this->model->framework->mongodb->createCollection($this->collection);
	}
}
?>
