<?
 
class Quote extends AppModel {

    var $name = 'Quote';
    var $useTable = 'quotes';
    
    var $hasMany = array('Comment'=>array('className'=>'Comment'));
    var $belongsTo = array('User', 'Prf');
    
    var $conditions = array('is_public'=>1);
    
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
            INSERT INTO `quotes`( `prf_id`, `quote`, `original_quote`, `active`, `time_added`, `user_id` )
                        VALUES( $prf_id, '$quote', '$quote', 0, NOW(), $user_id );
        " );
        
        $res = $this->query( "SELECT LAST_INSERT_ID() as taco" );
        
        return $res[0][0]['taco'];        
    }
    
    function deactivate($id) {
        $id = (int)$id;
        $this->query( "
            UPDATE `quotes` SET `is_public` = 0 WHERE id = $id;
        " );
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
    
    function update_title( $qid, $title, $uid ) {
        $safetitle = mysql_escape_string($title);
        
        $sql = "UPDATE `quotes` SET title = '$safetitle' WHERE id = $qid; ";
        
        $this->query($sql);
        $this->_updatePrettyUrl($qid, $title, $uid);
    }
    
    function update_body( $qid, $body, $uid ) {
        $safebody = mysql_escape_string($body);
        
        $sql = "UPDATE `quotes` SET quote = '$safebody', last_edited_by = $uid, is_formatted = 1 WHERE id = $qid; ";
        
        $this->query($sql);
    }
    
    function find_info_for_prettyurl( $pretty ) {
        
        $pretty = mysql_real_escape_string($pretty);
        
        $sql = "SELECT * FROM `quotes_permalinks` WHERE pretty_url = '$pretty';";
        $res = $this->query($sql);
        if( !empty($res[0]) ) {
            return $res[0];
        }
        
        return false; 
    }
    
    function find_permalink_for_id($qid) {
        $sql = "SELECT * FROM `quotes_permalinks` WHERE quote_id = $qid AND is_current = 1;";
        $res = $this->query($sql);
        
        if(isset($res[0]['quotes_permalinks']['pretty_url'])) {
            return $res[0]['quotes_permalinks']['pretty_url'];
        }
        
        return false;
    }
    
	function _updatePrettyUrl( $qid, $title, $uid ) {
        
        $pretty = url_token::tokenize($title);
		
        $sql = "UPDATE `quotes_permalinks` SET is_current = 0 WHERE quote_id = $qid;";
        
        $this->query( $sql );
        		
        $i = 0;
        do {
            $url = $pretty;
            
            if( $i ) {
                $url .= '-'.$i;
            }
            
            $sql2 = "
                INSERT INTO `quotes_permalinks`(`quote_id`, `pretty_url`, `is_current`, `user_id`)
                                        VALUES ($qid, '$url', 1, $uid);
            ";
            
            mysql_query( $sql2 );
            
            $i++;
            
        } while( mysql_errno() > 0 );
	}   
}