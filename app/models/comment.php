<?

class Comment extends AppModel { 

    var $name = 'Comment';  
    var $belongsTo = array(
        'User'=>array('className'=>'User'),
        'Quote'=>array('className'=>'Quote'),
    );
    
    var $validate = array(
        'body' => array(
            'rule' => array('minLength', 1),
            'required' => true,
        ),
        'quote_id' => array(
            'rule' => 'numeric',
            'required' => true,
        ),
        'user_id' => array(
            'rule' => 'numeric',
            'required' => true,
        )
    );
}