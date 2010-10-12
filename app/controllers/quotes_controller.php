<?

class QuotesController extends AppController {
    
	var $name = 'Quotes';
    
    var $components = array('RequestHandler', 'Cookie'); 
    
    function index( $id=null ) {
        //SELECT count(*) as total, prf_name FROM quotes GROUP BY prf_name ORDER BY total DESC
        if( $id ) {
            $res = $this->Quote->findById($id);
            
            if(isset($res)) {
                $this->set('res', $res);
            } else {
                $this->cakeError('error404');
            }
        }
        
        $pf_name = $this->Cookie->read('Comments.name');
        $pf_email = $this->Cookie->read('Comments.email');
        $pf_www = $this->Cookie->read('Comments.website');

        if( isset($sessuser['User']['username']) ) {
            $pf_name = $sessuser['User']['username'];
            pr2($sessuser, 'hi');
        }
        
        $this->set('pf_name', $pf_name);
        $this->set('pf_email', $pf_email);
        $this->set('pf_www', $pf_www);
    }
    
    function manage() {
        $userID = $this->Session->read('User.id');
        if(!$userID) {
            $this->cakeError('error404', 'You must be logged in to perform that action.');
        }
    }
    
    function add() {
        
        if( 
            isset($_POST['data']['Quote']['prf']) &&
            isset($_POST['data']['Quote']['quote'])   
        ) {
            $res_id = $this->Quote->easy_save($_POST['data']['Quote']['prf'], $_POST['data']['Quote']['quote']);
            $this->redirect('/quotes/'.$res_id);
        }
        
        $userID = $this->Session->read('User.id');
        if(!$userID) {
            $this->cakeError('error404', 'You must be logged in to perform that action.');
        }
    }
       
	public function beforeFiler() {
		parent::beforeFiler();
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
        
        $res = $this->Quote->find( 'all', array('order' => 'id DESC', 'limit' => $limit) );
        $this->set('res', $res);
    }
}