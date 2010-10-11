<?

class Comment extends AppModel { 

    var $name = 'Comment';  
    var $belongsTo = array('Quote'=>array('className'=>'Quote'));
    
    var $validate = array(
        'name' => array(
            'rule' => array('minLength', 1),  
            'required' => true,
        ),
        'body' => array(
            'rule' => array('minLength', 1),
            'required' => true,
        ),
        'email' => array(
            'rule' => 'email',
            'required' => true,
        ),
        'quote_id' => array(
            'rule' => 'numeric',
            'required' => true,
        )
    );

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