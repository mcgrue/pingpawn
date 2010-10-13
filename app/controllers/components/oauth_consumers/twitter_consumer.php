<?

require_once( 'config/database.php' );

class TwitterConsumer extends AbstractConsumer {
    public function __construct() {
        /// these functions hidden away in the gitignore'd database.php
        parent::__construct(get_oauth_consumer_key(), get_oauth_consumer_secret());
    }
}