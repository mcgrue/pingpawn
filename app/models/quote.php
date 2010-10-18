<?
 
class Quote extends AppModel {

    var $name = 'Quote';
    var $useTable = 'quotes';
    
    var $hasMany = array('Comment'=>array('className'=>'Comment'));
    var $belongsTo = array('User', 'Prf');
    
    var $hasAndBelongsToMany =
        array(
            'Tag' => 
                array(
                    'className'                 => 'Tag', 
                    'joinTable'                 => 'quotes_tags', 
                    'foreignKey'                => 'quote_id', 
                    'associationForeignKey'     => 'tag_id', 
                    'conditions'                => '', 
                    'order'                     => '', 
                    'limit'                     => '', 
                    'unique'                    => true, 
                    'finderQuery'               => '', 
                    'deleteQuery'               => '', 
                ),
            'Prf' => 
                array(
                    'className'                 => 'Prf', 
                    'joinTable'                 => 'prfs_quotes', 
                    'foreignKey'                => 'quote_id', 
                    'associationForeignKey'     => 'prf_id', 
                    'conditions'                => '', 
                    'order'                     => '', 
                    'limit'                     => '', 
                    'unique'                    => true, 
                    'finderQuery'               => '', 
                    'deleteQuery'               => '', 
                ) 
            );
        
    function afterFind($results, $primary) {        
        if( empty($results[0]['Comment']) ) return $results;
        
        $commenters = array();
        foreach( $results[0]['Comment'] as $c ) {
            $commenters[$c['user_id']] = (int)$c['user_id'];
        }
        
        $res = $this->User->find('all', array('conditions' => array('id' => array_values($commenters) )));
        
        $commenters = array();
        foreach( $res as $c ) {
            $commenters[$c['User']['id']] = $c['User'];
        }
        
        $results[0]['Commentors'] = $commenters;
        
        return $results;
    }
    
    function get_prf($name, $user_id) {
        $name = mysql_real_escape_string(stripslashes($name));
        
        $this->query( "
            INSERT IGNORE INTO `prfs`(user_id, name, url_key) VALUES ($user_id, '$name', '$name');
        " );
        
        $res = $this->QUERY( "
            SELECT * FROM `prfs` WHERE `name` = '$name';
        " );
        
        $prf_id = $res[0]['prfs']['id'];
        
        return $prf_id;
    }
    
    function save_quote( $prf_id, $user_id, $quote ) {
        $quote = mysql_real_escape_string(stripslashes($quote));
        
        $this->query( "
            INSERT INTO `quotes`( `prf_id`, `quote`, `active`, `time_added`, `user_id` )
                        VALUES( $prf_id, '$quote', 0, NOW(), $user_id );
        " );
        
        $res = $this->query( "SELECT LAST_INSERT_ID() as taco" );
        
        return $res[0][0]['taco'];        
    }
    
    function easy_save( $name, $quote, $user_id ) {
        
        $_SESSION['quick_prf'] = $name;
        
        $prf_id = $this->get_prf($name, $user_id);
        
        return $this->save_quote( $prf_id, $user_id, $quote );
    }
    
    function get_random_unvoted($uid) {
        $uid = (int)$uid;
        if( $uid <= 0 ) {
            return false;
        }
        
        $sql = "
            SELECT *
              FROM quotes
             WHERE id NOT IN ( SELECT quote_id FROM votes WHERE user_id = $uid )
             ORDER BY RAND()
             LIMIT 1
        ";
        
        $res = $this->query( $sql );
        
        if( !empty($res[0]['quotes']['id']) ) {
            return $res[0]['quotes']['id'];
        }
        
        return false;
    }
}