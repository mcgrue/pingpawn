<?

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));

class OauthController extends AppController {
        
    public $uses = array();
    
    public function response() {
        $_POST;
    }

    public function twitter() {
        $consumer = $this->createConsumer();
        $requestToken = $consumer->getRequestToken('http://twitter.com/oauth/request_token', 'http://www.pingpawn.com/oath/response');
        $this->Session->write('twitter_request_token', $requestToken);
        $this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    }

    public function twitter_callback() {
        $requestToken = $this->Session->read('twitter_request_token');
        $consumer = $this->createConsumer();
        $accessToken = $consumer->getAccessToken('http://twitter.com/oauth/access_token', $requestToken);

        $consumer->post($accessToken->key, $accessToken->secret, 'http://twitter.com/statuses/update.json', array('status' => 'hello world!'));
    }

    private function createConsumer() {
        return new OAuth_Consumer(get_oauth_consumer_key(), get_oauth_consumer_secret());
    }
}