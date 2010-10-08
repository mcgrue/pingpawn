<?

class Comment extends AppModel { 

    var $name = 'Comment';  
    var $belongsTo = array('Quote'=>array('className'=>'Quote'));
    
    var $validate = array(
        'name' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
        ),
        'body' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
        ),
        'email' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
        ),
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