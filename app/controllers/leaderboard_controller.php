<?

class LeaderboardController extends AppController {

	var $name = 'Leaderboard';
    var $uses = array('Vote', 'Quote');
    
    var $paginate = array(
        'limit' => 10,
        'conditions' => array(
            'Quote.is_public' => 1
        ), 
        'order' => array(
            'Quote.tally' => 'desc'
        )
    );
    
    function index() {
        $this->set( 'voters', $this->Vote->get_voter_counts(20) );
	}
    
    function voters() {
        $this->set( 'voters', $this->Vote->get_voter_counts(20) );
	}
    
    function quotes() {
        $data = $this->paginate('Quote');
        
        if( !empty($this->sessuser) ) {
            $ar = array();
            foreach($data as $caca) {
                $ar[] = $caca['Quote']['id'];
            }
            $res = $this->Vote->get($ar, $this->sessuser['User']['id']);
            
            $ar = array();
            foreach($res as $v) {
                $ar[$v['votes']['quote_id']] = $v;
            }
            
            $this->set( 'vote', $ar );
        }
        
        $this->set( 'data', $data );
    }
    
    function quotefiles() {
        
    }
}