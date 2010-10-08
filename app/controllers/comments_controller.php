<?
class CommentsController extends AppController {

	var $name = 'Comments';
    
    function add() {
        
        $this->Comment->set( $this->data );
        
        if( $this->Comment->validates() ) {
            $this->Comment->save();
            $this->redirect('/quotes/'.$_POST['data']['Comment']['post_id']);
        } else if( isset($_POST['data']['Comment']['post_id']) && is_numeric($_POST['data']['Comment']['post_id']) ) {
            $this->Session->setFlash('required fields missing from comment.');
            $this->redirect('/quotes/'.$_POST['data']['Comment']['post_id']);
            $errors = $this->Comment->invalidFields();
            pr2($errors);
        }
        
        $this->Session->setFlash('Unexpected error while saving comment.');
        $this->redirect('/', 500);
	}
}