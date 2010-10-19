<?

class QuotesController extends AppController {
    
	var $name = 'Quotes';
    var $uses = array( 'Quote', 'Vote' );
    var $components = array('RequestHandler', 'Cookie');
    var $helpers = array();
    
    function _rss($limit=5) {
        
        $quotes = $this->recent($limit);
            
        $this->set(compact('quotes'));
    }
    
    function _index( $id=null ) {
        if( $id !== null ) {
            $res = $this->Quote->findById($id);
            
            if(!empty($res)) {
                
                if( !isset($res['Prf']) && !isset($res['Quote']) ) {
                    pr2($res, 'res');
                }
                
                $this->set('res', $res);
                
                if(!$res['Quote']['active'] && can_edit($this->sessuser, $res) ) {
                    $this->set('inactive', 1);
                }
                
                if($this->sessuser) {
                    $v = $this->Vote->get($id, $this->sessuser['User']['id']);
                    if(!empty($v[0]['votes']['vote'])) {
                        $this->set('vote', $v[0]['votes']['vote']);
                    }
                }
                
                $this->set('ads_on', 1);
                
            } else {
                $this->cakeError('error404', array());
            }
        } else {
            $this->recent(20);
            $this->render('recent');
            return;
        }
    }
    
    function index( $id=null ) {
        
        if( $this->RequestHandler->isRss() ) {
            $this->_rss();
        } else {
            $this->_index($id);
        }
    }
    
    function manage() {
        $userID = $this->Session->read('User.id');
        if(!$userID) {
            $this->flashAndGo('You must be logged in to perform that action.', '/');
        }
    }
    
    function add() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to quote.', '/');
        }
        
        if(
            isset($_POST['data']['Quote']['prf']) &&
            isset($_POST['data']['Quote']['quote'])   
        ) {
            $res_id = $this->Quote->easy_save($_POST['data']['Quote']['prf'], $_POST['data']['Quote']['quote'], $this->sessuser['User']['id']);
            
            $this->Vote->perform_civic_duty($res_id, $this->sessuser['User']['id'], 1);
            
            $this->redirect('/quotes/'.$res_id);
        }
        
        $userID = $this->Session->read('User.id');
        if(!$userID) {
            $this->flashAndGo('You must be logged in to perform that action.', '/');
        }
    }
       
	public function beforeFiler() {
		parent::beforeFiler();
        
        //$this->helpers[] = array('Edit' => array('sessUser'=>$this->sessuser) );
	}
    
    public function recent($limit=5) {
        $limit = (int)$limit;
        if(!$limit) {
            $limit = 5;
        }
        
        if( $limit > 20 ) {
            $this->Session->setFlash('Too many quotes requested, limiting to 20.');
            $limit = 20;
        }
        
        $res = $this->Quote->find( 'all', array('order' => 'Quote.id DESC', 'limit' => $limit) );
        $this->set('res', $res);
        
        $this->set('rssurl', '/quotes/index.rss');
        $this->set('rssname', 'Ping Pawn RSS Feed');
        
        return $res;
    }
    
    public function update( $id, $field ) {
        $this->layout = 'ajax';
        $this->set('error_text', '');
        //$this->set('html', json_encode(print_r($_POST, true)) );
        $this->set('html', 'This is not a true ending.');

/*
[url] => 'http://localhost/pingpawn/quotes/6399'
[id] => 'quote_body'
[form_type] => 'textarea'
[orig_value] => 'blah blah blah'
[new_value] => 'blah blah blah'
[data] => false
*/
        
    }
}