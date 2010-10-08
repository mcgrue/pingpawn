<?

class Comment extends AppModel { 

    var $name = 'Comment';  
    var $belongsTo = array('Quote'=>array('className'=>'Quote'));  

/*
    var $validate = array(
        'body'=>array(
            'notSpam'=>array(
                'rule'=>array(
                    'notSpam', true
                )
            )
        )
    ); 

    var $actsAs = array(
        'Akismet' => array( 
            'content'=>'body', 
            'author'=>'User.name', 
            'type'=>false, 
            'owner'=>'owner_id', 
            'is_spam'=>'spam' 
        )
    );   
*/
}