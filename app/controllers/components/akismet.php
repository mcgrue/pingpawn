<?php
/**
 * This is a component for CakePHP that utilizes the Akismet API
 *
 * See http://akismet.com/development/api/ for more information.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Seth Cardoza <www.sethcardoza.com>
 * @category akismet
 * @package component
 **/

class AkismetComponent extends Object {

    /**
     * @var string
     */
    private $http;

    const API_KEY = Configure::read('Akismet.key');
    const BASE_URL = 'rest.akismet.com';
    const API_VERSION = '1.1';
    const VERIFY_KEY_ACTION = 'verify-key';
    const COMMENT_CHECK_ACTION = 'comment-check';
    const SUBMIT_SPAM_ACTION = 'submit-spam';
    const SUBMIT_HAM_ACTION = 'submit-ham';

    const APP_USER_AGENT = 'CakePHP/1.2 | Akismet Model 1.0';

    public function __construct() {
        App::Import('Core', 'HttpSocket');
        $this->http = new HttpSocket();
    }

    public function verifyKey($data) {
        $data = array();
       
        if (!isset($data['blog'])) {
            $data['blog'] = FULL_BASE_URL;
        }
       
        if (!isset($data['key'])) {
            $data['key'] = self::API_KEY;
        }
       
        $uri = 'http://' . self::BASE_URL . '/' . self::API_VERSION . '/' . self::VERIFY_KEY_ACTION;
       
        $request = array('header' => array('User-Agent: ' . self::APP_USER_AGENT));
       
        return $this->http->post($uri, $data, $request);
    }
   
    /**
     * This is just a wrapper function for Akismet::commentCheck(). the return result makes more sense calling this function.
     * The two functions can be used interchangeably
     * @param array $comment
     * @return string
     */
    public function isSpam($comment) {
        return $this->commentCheck($comment);
    }
   
    /**
     * returns true if comment is spam, false otherwise
     *
     * From API Documentation: If you are having trouble triggering you can send "viagra-test-123"
     * as the author and it will trigger a true response, always.
     *
     * @param array $comment
     * @return string
     */
    public function commentCheck($comment) {
        return $this->__makeRequest($comment, self::COMMENT_CHECK_ACTION);
    }
   
    /**
     * @param array $comment
     * @return string
     */
    public function submitSpam($comment) {
        return $this->__makeRequest($comment, self::SUBMIT_SPAM_ACTION);
    }
   
    /**
     * @param array $comment
     * @return string
     */   
    public function submitHam($comment) {
        return $this->__makeRequest($comment, self::SUBMIT_HAM_ACTION);
    }
   
    /**
     * this is where the magic happens. this makes the call to get the default info if not set, and
     * makes the request, passing the necessary data
     */
    private function __makeRequest($comment, $action) {
        $comment = $this->__getDefaultData($comment);
       
        $request = array('header' => array('User-Agent: ' . self::APP_USER_AGENT));
       
        $uri = 'http://' . self::API_KEY . '.' . self::BASE_URL . '/' . self::API_VERSION . '/' . $action;
       
        $return = $this->http->post($uri, $comment, $request);
       
        return $return;
    }
   
    private function __getDefaultData($comment) {
        App::import('Component', 'RequestHandler');
        if (!isset($comment['blog'])) {
            $comment['blog'] = FULL_BASE_URL;
        }

        if (!isset($comment['user_ip'])) {
            $comment['user_ip'] = RequestHandlerComponent::getClientIP();
        }
       
        if (!isset($comment['referrer'])) {
            $comment['referrer'] = RequestHandlerComponent::getReferrer();
        }

        if (!isset($comment['user_agent'])) {
            $vars['user_agent'] = env('HTTP_USER_AGENT');
        }
       
        return $comment;
    }
}
