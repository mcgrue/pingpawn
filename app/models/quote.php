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
                ) 
            ); 

    
    function easy_save( $name, $quote, $user_id ) {
        
        $_SESSION['quick_prf'] = $name;
        
        $name = mysql_real_escape_string(stripslashes($name));
        $quote = mysql_real_escape_string(stripslashes($quote));
        
        $this->query( "
            INSERT INTO `quotes`( `prf_name`, `quote`, `active`, `time_added`, `user_id` )
                        VALUES( '$name', '$quote', 0, NOW(), $user_id );
        " );
        
        $res = $this->query( "SELECT LAST_INSERT_ID() as taco" );
        
        return $res[0][0]['taco'];
    }
}