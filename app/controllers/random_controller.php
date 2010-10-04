<?
class RandomController extends AppController {

	var $name = 'Random';
    var $uses = array('Quote');
    
	function index() {
        $res = $this->Quote->findAllByActive(1,null,'rand()',1,null,null);
        
        if( isset($res[0]['Quote']['id']) ) {
            $this->redirect(array('controller'=>'quotes', 'action'=>'index', $res[0]['Quote']['id']) );
        }
        
        $this->cakeError('error500');
	}
}