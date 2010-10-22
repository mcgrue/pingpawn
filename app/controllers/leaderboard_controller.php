<?

class LeaderboardController extends AppController {

	var $name = 'Leaderboard';
    var $uses = array('Vote');
    
    function index() {
        $this->set( 'voters', $this->Vote->get_voter_counts(20) );
	}
}