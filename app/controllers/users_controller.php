<?

class UsersController extends AppController {
    
    var $name = 'Users';
    var $uses = array('User', 'Quote', 'Prf');
    
    function home() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to go home.', '/');
        }
        
        $this->set( 'upvotes', $this->User->count_upvotes($this->sessuser['User']['id']) );
        $this->set( 'downvotes', $this->User->count_downvotes($this->sessuser['User']['id']) );
        $this->set( 'modcount', $this->User->get_moderation_count($this->sessuser['User']['id']) );
        
	}
    
    function my_files() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to view this.', '/');
        }
        
        $res = $this->Prf->findByUserId($this->sessuser['User']['id']);
        
        $this->set( 'prfs', $res );
	}
    
    function mass_upload() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to upload a quotefile.', '/');
        }
        
        if( !empty($_POST['data']['Users']['name']) && !empty($_POST['data']['Users']['quotefile']) ) {
            $name = stripslashes($_POST['data']['Users']['name']);
            $quotefile = stripslashes($_POST['data']['Users']['quotefile']);
            
            $res = array();
            $ar = split("\n", $quotefile);
            foreach( $ar as $line ) {
                $line = trim($line);
                
                if( $line ) {
                    $res[] = $line;
                }
            }
            
            if( count($res) ) {
                
                $user_id = $this->sessuser['User']['id'];
                $prf_id = $this->Quote->get_prf($name, $user_id);
                
                foreach($res as $line) {
                    $this->Quote->save_quote( $prf_id, $user_id, $line );
                }
                
                $this->flashAndGo( 'Mass-uploaded '.count($res).' quotes to quotefile: '.$name.' .', '/users/moderation_queue/' );
                
            } else {
                $this->flashAndGo( 'Invalid mass-upload.', '/users/mass_upload/' );
            }
        }
	}

    function moderation_queue() {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be logged in to manage your quotes.', '/');
        }
        
        $uid = $this->sessuser['User']['id'];
        
        $this->set( 'count', $this->User->get_moderation_count($uid) );
        
        $this->set( 'item', $this->User->get_top_moderation_item($uid) );
	}
    
    function logout() {
        Authsome::logout();
        $this->flashAndGo('You are now logged out.', '/');
    }

	function beforeFilter() {
		parent::beforeFilter();
    }
}