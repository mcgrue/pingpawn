<?

// SELECT * FROM quotes WHERE length(quote) <= 118

class TwitterController extends AppController {
    
	var $name = 'Twitter';
    var $uses = array();
    var $components = array('RequestHandler', 'Cookie', 'OauthConsumer');

    public function twitter() {
        
        
        if( is_localhost() ) {
            $response_url = 'http://localhost/pingpawn/twitter/twitter_callback';    
        } else {
            $response_url = 'http://www.pingpawn.com/twitter/twitter_callback';    
        }
        
        $requestToken = $this->OauthConsumer->getRequestToken('Twitter', 'http://twitter.com/oauth/request_token', $response_url );
        $this->Session->write('twitter_request_token', $requestToken);
        $this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    }

    public function twitter_callback() {
        $requestToken = $this->Session->read('twitter_request_token');
        $accessToken = $this->OauthConsumer->getAccessToken('Twitter', 'http://twitter.com/oauth/access_token', $requestToken);

        $this->OauthConsumer->post('Twitter', $accessToken->key, $accessToken->secret, 'http://twitter.com/statuses/update.json', array('status' => 'hello world!'));
    }
}