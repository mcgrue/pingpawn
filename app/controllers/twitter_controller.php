<?

// SELECT * FROM quotes WHERE length(quote) <= 118

class TwitterController extends AppController {
    
	var $name = 'Twitter';
    var $uses = array('User');
    var $components = array('Cookie', 'OauthConsumer');
    
    public function login($return=false) {
        
        if( isset($sessuser) ) {
            $this->flashAndGo('You need to be logged out to log in, '.$sessuser['User']['display_name']);    
        }
        
        if( is_sandbox() ) {
            $response_url = 'http://localhost/pingpawn/twitter/twitter_callback';    
        } else {
            $response_url = 'http://www.pingpawn.com/twitter/twitter_callback';    
        }
        
        $requestToken = $this->OauthConsumer->getRequestToken('Twitter', 'http://twitter.com/oauth/request_token', $response_url );

        $this->Cookie->write('twitter_request_token_2', $requestToken);
        
        if( $return && !empty($_SERVER['HTTP_REFERER']) ) {
            $this->Cookie->write('after_login',$_SERVER['HTTP_REFERER']);
        } else {
            $this->Cookie->write('after_login', false);
        }
        
        $this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    }

    public function twitter_callback() {
        
        $requestToken = $this->Cookie->read('twitter_request_token_2');
        
        if( !$requestToken ) {
            $this->flashAndGo( 'Invalid request token.', '/' );
        }
        
        $accessToken = $this->OauthConsumer->getAccessToken('Twitter', 'http://twitter.com/oauth/access_token', $requestToken);
        
        if( !$accessToken ) {
            $this->flashAndGo( 'Invalid access token.', '/' );
        }
        
        $response = $this->OauthConsumer->get('Twitter', $accessToken->key, $accessToken->secret, 'http://api.twitter.com/1/account/verify_credentials.json', array());
        
        $res = json_decode($response);
        
        if( !$res ) {
            $this->flashAndGo( 'Invalid login response.', '/' );
        }
        
        if( is_object($res) ) {
            if( isset($res->error) ) {
                $this->flashAndGo( 'Error: '.$res->error, '/' );
            }
            
            $user = array(
                'id' => $res->id,
                'real_name' => $res->name,
                'twitter_name' => $res->screen_name,
                'description' => $res->description,
                'url' => $res->url,
                'profile_image_url' => $res->profile_image_url,
                'display_name' => $res->screen_name,
            );
            
            $res = $this->User->findById($user['id']);
            if( !$res ) {
                $this->User->set($user);
                $this->User->save();
                $res = $this->User->findById($user['id']);
            }
            
            $user = Authsome::login($res['User']);
            
			if (!$user) {
				$this->flashAndGo('Unknown user or wrong password', '/');
				return;
			}
            
            Authsome::persist('2 weeks');
            
            $return = $this->Cookie->read('after_login');
            if($return) {
                $this->Cookie->write('after_login', 'false');
            } else {
                $return = '/users/home';
            }
            
            $this->flashAndGo( 'You are now logged in, '.$user['User']['display_name'], $return );   
            
        } else {
            $this->flashAndGo( 'There was a weird problem: '.$res, '/' );   
        }
    }
    
	function beforeFilter() {
		parent::beforeFilter();
    }
}