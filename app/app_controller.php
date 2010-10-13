<?
	class AppController extends Controller {
		public $components = array(
            'Session',
			'Authsome.Authsome' => array(
				'model' => 'User'
			)
		);
        
        function beforeFilter() {
            parent::beforeFilter();
            $this->sessuser = Authsome::get();
            $this->set('sessuser', $this->sessuser);
        }
        
        function flashAndGo( $flash, $go, $response=302 ) {
            $this->Session->setFlash($flash);
            $this->redirect($go, $response);            
        }
    }