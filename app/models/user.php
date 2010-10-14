<?
class User extends AppModel{

    public $hasMany = array('LoginToken', 'Prf');
    
    public function authsomeLogin($type, $credentials = array()) {
        switch ($type) {
            case 'guest':
                // You can return any non-null value here, if you don't
                // have a guest account, just return an empty array
                return array();
            case 'credentials':
                // This is the logic for validating the login
                $conditions = array(
                    'User.id' => $credentials['id']
                );
                break;
			case 'cookie':
				list($token, $userId) = split(':', $credentials['token']);
				$duration = $credentials['duration'];

				$loginToken = $this->LoginToken->find('first', array(
					'conditions' => array(
						'user_id' => $userId,
						'token' => $token,
						'duration' => $duration,
						'used' => false,
						'expires <=' => date('Y-m-d H:i:s', strtotime($duration)),
					),
					'contain' => false
				));

				if (!$loginToken) {
					return false;
				}

				$loginToken['LoginToken']['used'] = true;
				$this->LoginToken->save($loginToken);

				$conditions = array(
					'User.id' => $loginToken['LoginToken']['user_id']
				);
				break;

            default:
                return null;
        }

        return $this->find('first', compact('conditions'));
    }
    
    public function authsomePersist($user, $duration) {
        $token = md5(uniqid(mt_rand(), true));
        $userId = $user['User']['id'];
    
        $this->LoginToken->create(array(
            'user_id' => $userId,
            'token' => $token,
            'duration' => $duration,
            'expires' => date('Y-m-d H:i:s', strtotime($duration)),
        ));
        $this->LoginToken->save();
    
        return "${token}:${userId}";
    }
    
    public function beforeFind($foo) {
        $this->query( 'DELETE FROM `login_tokens` WHERE `expires` < NOW(); ' );
    }
    
    public function count_upvotes($uid) {
        $res = $this->query( 'SELECT COUNT(*) as up_cnt FROM votes WHERE vote > 0; ' );
        
        if(!empty($res[0][0]['up_cnt'])) {
            return $res[0][0]['up_cnt'];
        } else {
            return 0;
        }
    }

    public function count_downvotes($uid) {
        $res = $this->query( 'SELECT COUNT(*) as dwn_cnt FROM votes WHERE vote < 0; ' );

        if(!empty($res[0][0]['dwn_cnt'])) {
            return $res[0][0]['dwn_cnt'];
        } else {
            return 0;
        }
    }
    
    public function get_moderation_count($user_id) {
        $res = $this->query(
            "SELECT COUNT(*) as cnt
               FROM quotes
              WHERE
                    quotes.active != 1
                AND 
                    quotes.user_id = $user_id;
            "
        );
        
        if(!empty($res[0][0]['cnt'])) {
            return $res[0][0]['cnt'];
        }
        
        return 0;
    }
    
    public function get_top_moderation_item($user_id) {
        $res = $this->query(
            "SELECT * 
               FROM quotes
              WHERE
                    quotes.active != 1
                AND 
                    quotes.user_id = $user_id
              LIMIT 1;
            "
        );
        
        if(!empty($res[0]['quotes'])) {
            return $res[0]['quotes'];
        }
        
        return array();
    }
}