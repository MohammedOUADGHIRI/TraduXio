<?php

/**
 * Tag controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */ 
 
class TagController extends Tdxio_Controller_Abstract
{
    protected $_modelname='Taggable'; 
    
    protected function tagSentence()
    {
        
    }

    public function tagAction(){
        
        $model= $this->_getModel();
        $user = Tdxio_Auth::getUserName();
        $user = !is_null($user)?$user:Tdxio_Auth::getUserRole();
        $request = $this->getRequest();
        $taggable_id = $request->getParam('id');
        $genre = $request->getParam('genre');
        $tag = $request->getParam('tag');
        $data = array('username'=> $user, 'taggable_id'=> $taggable_id,'genre'=> $genre, 'comment' => $tag);
        $model->tag($data); 
        $histModel = new Model_History();
        
        Tdxio_Log::info('ADD HISTORY TAG');
        $histModel->addHistory($taggable_id,3,array('tag'=>$tag,'genre'=>$genre));            
        
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
     
    public function deletetagAction(){      
        Tdxio_Log::info('entra in deletetagAction');
        $username = Tdxio_Auth::getUserName();  
        $request=$this->getRequest();
        $taggableId=$request->getParam('id');
        $tag=$request->getParam('tag');
        $genre=$request->getParam('genre');
        $model= $this->_getModel();
        $result = $model->deleteTag($username,$taggableId,$tag,$genre);
        Tdxio_Log::info($result,'esito');
        $histModel = new Model_History();
        $histModel->addHistory($taggableId,4,array('tag'=>$tag,'genre'=>$genre));   
        $this->_redirect($_SERVER['HTTP_REFERER']);
   
    }
    
    public function getRule($request){
        Tdxio_Log::info('Entra in tag-getRule');
        $action = $request->action;
        $resource_id = $request->getParam('id');
        
        if(!is_null($resource_id)){ 
            $taggableModel = new Model_Taggable();
            if(!($taggableModel->entryExists(array('id'=>$resource_id))))
            {throw new Zend_Exception(sprintf('Taggable Id "%d" does not exist.',$resource_id), 404);}
        }
        Tdxio_Log::info('supera controllo risorsa');
        
        $rule = 'noAction';
    
        switch($action){
            case 'tag': $rule = array('privilege'=> 'tag','work_id' => $resource_id);      
                        break; 
            case 'deletetag':
                        $rule = array('privilege'=> 'read','work_id' => $resource_id);       
                        break; 
        }               
        return $rule;        
    }
    
        
    
}
