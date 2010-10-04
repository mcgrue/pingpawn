<?

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));

class ExampleController extends AppController {
    public $uses = array();

    public function twitter() {
        $consumer = $this->createConsumer();
        $requestToken = $consumer->getRequestToken('http://twitter.com/oauth/request_token', 'http://test.localhost/example/twitter_callback');
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
        return new OAuth_Consumer('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');
    }
}