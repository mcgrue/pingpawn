<?

class AppController extends Controller {
    public $components = array(
        'Session',
        'Authsome.Authsome' => array(
            'model' => 'User'
        )
    );
    
    function beforeFilter() {
        if( !is_sandbox() ) {
            if( $_SERVER['HTTP_HOST'] != 'pingpawn.com' ) {
                
                Header( "HTTP/1.1 301 Moved Permanently" );
                header( 'Location: http://pingpawn.com'.$_SERVER['REQUEST_URI'] );
                die();
            }
        }
        
        parent::beforeFilter();
        $this->sessuser = Authsome::get();
        $this->set('sessuser', $this->sessuser);
    }
    
    function flashAndGo( $flash, $go, $response=302 ) {
        $this->Session->setFlash($flash);
        $this->redirect($go, $response);            
    }
}
