<?

// SELECT * FROM quotes WHERE length(quote) <= 118

App::import('Vendor', 'Twitter');
App::import('Vendor', 'HttpSocketOauth');

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

        if( ! class_exists ('Twitter') ) {
            require_once( "vendors/Twitter/Twitter.php" );
        }

        $twitter = new Twitter(null, $this->Session);
        $twitter->setupApp(get_oauth_consumer_key(), get_oauth_consumer_secret()); 
        $twitter->connectApp(Router::url(array('action' => 'authorization'), true));        

/*
        //$requestToken = $this->OauthConsumer->getRequestToken('Twitter', 'http://twitter.com/oauth/request_token', $response_url );

        if( !$requestToken ) {
            die( "Didn't get a request token from twitter.  That's bad." );
        }

        $this->Cookie->write('twitter_request_token_2', $requestToken);
        
        if( $return && !empty($_SERVER['HTTP_REFERER']) ) {
            $this->Cookie->write('after_login',$_SERVER['HTTP_REFERER']);
        } else {
            $this->Cookie->write('after_login', false);
        }
        
        $this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
*/        
    }


/*
http://localhost/pingpawn/twitter/authorization?oauth_token=1VW04LimUGcpWA9kRijdGo6Yet1X8BCNIAEMmPr1E&oauth_verifier=Reo1HJz3HrQrrFhYjx7HQrO6Xyqrkzh3Qbt8Z5qRN4
*/
    public function authorization() {


        if( ! class_exists ('Twitter') ) {
            require_once( "vendors/Twitter/Twitter.php" );
        }

        $twitter = new Twitter(null, $this->Session);
        $twitter->setupApp(get_oauth_consumer_key(), get_oauth_consumer_secret()); 

        if (!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])) {
            $twitter->authorizeTwitterUser($_GET['oauth_token'], $_GET['oauth_verifier']);
            # connect the user to the application
            try {
                $twitterUserObject = $twitter->getTwitterUser(true);

            $user = array(
                'id' => $twitterUserObject['profile']['id'],
                'real_name' => $twitterUserObject['profile']['name'],
                'twitter_name' => $twitterUserObject['profile']['screen_name'],
                'description' => $twitterUserObject['profile']['description'],
                'url' => $twitterUserObject['profile']['url'],
                'profile_image_url' => $twitterUserObject['profile']['profile_image_url'],
                'display_name' => $twitterUserObject['profile']['screen_name'],
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
            
            //get_achievements('36db707fd0c7995bff4cf4bcc6b19dc6', $user);
            
            $return = $this->Cookie->read('after_login');
            if($return) {
                $return = substr($return, stripos($return, 'pingpawn.com')+12);
                error_log('$return: ('.$return.')');
            } else {
                $return = '/users/home';
            }














                $this->flashAndGo( 'You are now logged in, '.$user['User']['display_name'], $return );   

            } catch (Exception $e) {
                $this->flashAndGo( 'There was a weird problem: '.$e->getMessage(), '/' );   
            }
        } else {
            $this->flashAndGo( 'Invalid authorization request.', '/' );   
        }
    }
    
	function beforeFilter() {
		parent::beforeFilter();
    }
}
