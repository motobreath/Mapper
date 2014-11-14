<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class Mapper
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($params=array(),$order="", $limit="")
    {        
        $select=$this->tableGateway->getSql()->select();
        if(!empty($params)){
            $select->where($params);
        }
        if(!empty($order)){
            $select->order($order);
        }        
        if(!empty($limit)){
            $select->limit($limit);
        }
               
        $resultSet = $this->tableGateway->selectWith($select);
        
        $results=array();
        foreach($resultSet as $rs){
            $results[]=$rs;
        }
        return $results;
    }

    public function get($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function find(Array $params){
        $rowset=$this->tableGateway->select($params);
        return $rowset->current();
    }

    public function save($object)
    {
        $data = $object->getArrayCopy();
        
        //remove empty values;
        foreach($data as $key=>$value){
            if(is_null($value)){
                unset($data[$key]);
            }
        }

        $id = (int)$object->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } 
        else {
            $this->tableGateway->update($data, array('id' => $id));
        }
        return $id;
    }
    
    public function updateFromParams($object,$params){
        $data=$object->getArrayCopy();
        unset($data["id"]);
        $this->tableGateway->update($data,$params);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    public function deleteByParam($key,$value){
        $this->tableGateway->delete(array($key=>$value));
    }
   
}