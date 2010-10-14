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
        
        $help = '<a href=/random>Go to Random Quote?</a>';
        
        if($vote > 0) {
            $this->flashAndGo('Successfully voted UP.<br /><br />'.$help, '/quotes/'.$id);
        } else {
            $this->flashAndGo('Successfully voted DOWN.<br /><br />'.$help, '/quotes/'.$id);
        }   
    }
    
    function up($id) {
        $this->_vote($id, +1);
    }
    
    function down($id) {
        $this->_vote($id, -1);
    }
}