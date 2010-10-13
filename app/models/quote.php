<?
 
class Quote extends AppModel {

    var $name = 'Quote';
    var $useTable = 'quotes';
    
    var $hasMany = array('Comment'=>array('className'=>'Comment'));
    var $belongsTo = 'User';
    
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
    
    function easy_save( $name, $quote, $user_id ) {
        
        $_SESSION['quick_prf'] = $name;
        
        $name = mysql_real_escape_string(stripslashes($name));
        $quote = mysql_real_escape_string(stripslashes($quote));
        
        $this->query( "
            INSERT IGNORE INTO `prfs`(user_id, name, url_key) VALUES (889031, '$name', '$name');
        " );
        
        $this->query( "
            INSERT INTO `quotes`( `prf_name`, `quote`, `active`, `time_added`, `user_id` )
                        VALUES( '$name', '$quote', 0, NOW(), $user_id );
        " );
        
        $res = $this->query( "SELECT LAST_INSERT_ID() as taco" );
        
        return $res[0][0]['taco'];
    }
}