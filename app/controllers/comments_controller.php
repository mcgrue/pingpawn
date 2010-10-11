<?
class CommentsController extends AppController {

	var $name = 'Comments';
    
    var $components = array('Cookie'); 
    
    function add() {
        
        $this->Comment->set( $this->data );
        
        if( $this->Comment->validates() ) {
            
            $this->Cookie->write('Comments.name', $this->data['Comment']['name'] );
            $this->Cookie->write('Comments.email', $this->data['Comment']['email'] );
            $this->Cookie->write('Comments.website', $this->data['Comment']['website'] );
            
            $this->Comment->save();
            $this->redirect('/quotes/'.$_POST['data']['Comment']['quote_id']);
        } else {
            $this->Session->setFlash('required fields missing from comment.');
            
            $errors = $this->Comment->invalidFields();
            pr2($errors);
            
            $this->redirect('/quotes/'.$_POST['data']['Comment']['quote_id']);
        }
        
        $this->Session->setFlash('Unexpected error while saving comment.');
        $this->redirect('/', 500);
	}
}