<?

class LeaderboardController extends AppController {

	var $name = 'Leaderboard';
    var $uses = array('Vote', 'Quote', 'Prf');
    
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
        $this->set( 'voters', $this->Vote->get_voter_counts(50) );
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
        $res = $this->Prf->getLeaderboard();
        
        function _cmp($a, $b) {            
            if( $a['score'] > $b['score'] ) {
                return -1;
            } else if( $a['score'] < $b['score'] ) {
                return 1;
            } else {
                return 0;
            }
        }
        
        $lb = array();
        foreach($res as $r) {
            $score = $r[0]['tallysum'] / $r[0]['quotecount'];
            
            $lb[] = array(
                'score' => $score,
                'tallysum' => $r[0]['tallysum'],
                'quotecount' => $r[0]['quotecount'],
                'prf_id' => $r['quotes']['prf_id']
            );
        }
        
        usort($lb, '_cmp');
        
        $res = $this->Prf->find('all');
        
        $prfs = array();
        foreach( $res as $p ) {
            $prfs[$p['Prf']['id']] = array(
                'name' => $p['Prf']['name'],
                'key' => $p['Prf']['url_key'],
            );
        }
        
        $this->set('prfs', $prfs);
        $this->set('lb', $lb);
    }
}