<?

class QuotesController extends AppController {

	var $name = 'Quotes';
    
    function index( $id=null ) {
        //SELECT count(*) as total, prf_name FROM quotes GROUP BY prf_name ORDER BY total DESC
        if( $id ) {
            $res = $this->Quote->findById($id);
            
            if(isset($res['Quote'])) {
                $this->set('quote', $res['Quote']);
            } else {
                $this->cakeError('error404');
            }
        }
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
    
    function beforeFilter() {
        parent::beforeFilter();
    }
}