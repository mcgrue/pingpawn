<?

class Tag extends AppModel { 
    var $name = 'Tag';
    var $belongsTo = 'User';
    
    function simple_save( $tag, $post_id, $user_id ) {
        
        $post_id = (int)$post_id;
        $res = $this->query( "SELECT * FROM `quotes` WHERE id = $post_id" );
        if(!$res) {
            return false;
        }
        
        $tag = mysql_real_escape_string(strtolower($tag));
        
        $sql = "INSERT IGNORE INTO `tags`(`tag`) VALUES ('$tag');";
        
        $this->query($sql);
        
        $sql2 = "SELECT * FROM `tags` WHERE tag = '$tag'";
        $res = $this->query( $sql2 );

        if( !isset($res[0]['tags']['id']) ) {
            return false;
        } else {
            $tag_id = $res[0]['tags']['id'];
        }
        
        $sql3 = "INSERT IGNORE INTO `quotes_tags`(`tag_id`, `quote_id`, `user_id`) VALUES ($tag_id, $post_id, $user_id)";
        $this->query( $sql3 );
        
        return true;
    }
} 