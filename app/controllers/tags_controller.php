<?
class TagsController extends AppController {

	var $name = 'Tags';

	function add() {
        
        if(
           isset($_POST['data']['Tag']['tag']) && trim($_POST['data']['Tag']['tag']) &&
           isset($_POST['data']['Tag']['post_id']) && (int)trim($_POST['data']['Tag']['post_id'])
        ) {
            
            $res = $this->Tag->simple_save( $_POST['data']['Tag']['tag'], $_POST['data']['Tag']['post_id'] );

            if($res) {
                $this->Session->setFlash('Tag added.');
                $this->redirect('/quotes/'.$_POST['data']['Tag']['post_id']);
            }
        }
        
        $this->Session->setFlash('Unexpected error while saving tag.');
        $this->redirect('/', 500);
    }
}