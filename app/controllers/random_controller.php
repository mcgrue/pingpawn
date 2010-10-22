<?
class RandomController extends AppController {

	var $name = 'Random';
    var $uses = array('Quote');
    
	function index() {
        $res = $this->Quote->findAllByIsPublic(1,null,'rand()',1,null,null);
        
        if( isset($res[0]['Quote']['id']) ) {
            $this->redirect(array('controller'=>'quotes', 'action'=>'index', $res[0]['Quote']['id']) );
        }
        
        $this->cakeError('error500');
	}
    
    function unvoted() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to see random quotes that you haven\'t met yet.', '/');
        }
        
        $id = $this->Quote->get_random_unvoted($this->sessuser['User']['id']);
                
        if( $id ) {
            $this->redirect(array('controller'=>'quotes', 'action'=>'index', $id) );
        }
        
        $this->cakeError('error500');        
    }
}