<?

class UsersController extends AppController {
    
    var $name = 'Users';
    var $uses = array('User');
    
    function home() {
        
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to go home.', '/');
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