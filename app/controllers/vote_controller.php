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
            
            $oldvote = $res[0]['votes']['vote'];
            
            if( $oldvote == $vote ) {
                $this->flashAndGo('You already voted that way on this quote.', '/quotes/'.$id);
            } else {
                $this->Vote->reverse_decision($id, $this->sessuser['User']['id'], $vote);
                $this->flashAndGo('Vote successfully switched.', '/quotes/'.$id);
            }
        } else {
            $this->Vote->perform_civic_duty($id, $this->sessuser['User']['id'], $vote);
            
            $help = '<a href="/random/unvoted">Go to Random Quote?</a><br><br>';
            
            if($vote > 0) {
                $this->flashAndGo($help.'Successfully voted UP.', '/quotes/'.$id);
            } else {
                $this->flashAndGo($help.'Successfully voted DOWN.', '/quotes/'.$id);
            }            
        }
    }
    
    function up($id) {
        $this->_vote($id, +1);
    }
    
    function down($id) {
        $this->_vote($id, -1);
    }

    function xyzzy_plugh() {
        if(empty($this->sessuser) || $this->sessuser['User']['id'] != 889031 ) {
            $this->flashAndGo('You aren\'t a god, Ray.', '/');
        }

        $this->Vote->_vote_schema_update_dec2010();
    }
}