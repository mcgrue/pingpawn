<?

class UsersController extends AppController {
    
    var $name = 'Users';
    var $uses = array('User');
    
    function home() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to go home.', '/');
        }
        
        $this->set( 'upvotes', $this->User->count_upvotes($this->sessuser['User']['id']) );
        $this->set( 'downvotes', $this->User->count_downvotes($this->sessuser['User']['id']) );
        
	}
    
    function mass_upload() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to upload a quotefile.', '/');
        }
        
        if( isset($_POST) ) {
            pr2($_POST);
        }
	}

    
    function logout() {
        Authsome::logout();
        $this->flashAndGo('You are now logged out.', '/');
    }

	function beforeFilter() {
		parent::beforeFilter();
    }
}