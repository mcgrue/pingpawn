<?

class VoteController extends AppController {

	var $name = 'Vote';
    
    var $uses = array('Quote', 'Vote');
    
    function _vote($id, $vote) {
        if(!$this->sessuser) {
            $this->flashAndGo('You must be <a href="/twitter/login/1/">logged in</a> to vote.', '/quotes/'.$id);
        }
        
        $id = (int)$id;
        
        if($id <= 0) {
            $this->flashAndGo('Invalid quote id.', '/quotes/');
        }
        
        $res = $this->Quote->findById($id);
        
        if(empty($res)) {
            $this->flashAndGo('Invalid quote.', '/quotes/');
        }
        
        if( !($vote === -1 || $vote === 1) ) {
            $this->flashAndGo('Invalid vote value.', '/quotes/'.$id);
        }
        
        $res = $this->Vote->get($id, $this->sessuser['User']['id']);
        if( $res ) {
            $this->flashAndGo('You already voted on this quote.', '/quotes/'.$id);
        }
        
        $this->Vote->perform_civic_duty($id, $this->sessuser['User']['id'], $vote);
        
        $help = '<a href="/random/unvoted">Go to Random Quote?</a><br><br>';
        
        if($vote > 0) {
            $this->flashAndGo($help.'Successfully voted UP.', '/quotes/'.$id);
        } else {
            $this->flashAndGo($help.'Successfully voted DOWN.', '/quotes/'.$id);
        }   
    }
    
    function up($id) {
        $this->_vote($id, +1);
    }
    
    function down($id) {
        $this->_vote($id, -1);
    }
}