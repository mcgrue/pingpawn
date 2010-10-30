<?
 
class Prf extends AppModel {
    var $name = 'Prf';
    var $useTable = 'prfs';
    
    var $belongsTo = array('User');
    
    
    function getStats($id) {
        
        $ret = array(
            'quote_count' => 0,
            'vote_count' => 0,
            'min' => 0,
            'max' => 0,
            'sum' => 0
        );
        
        $sql = "
            SELECT COUNT(votes.vote) as vote_count
              FROM votes, quotes
             WHERE quotes.prf_id = $id
               AND quotes.id = votes.quote_id
               AND quotes.is_public = 1
        ";
        
        $res = $this->query($sql);
        
        if(!empty($res[0][0]['vote_count'])) {
            $ret['vote_count'] = $res[0][0]['vote_count'];
        }
        
        $sql = "
            SELECT count(quotes.id) as total_quotes, SUM(quotes.tally) as my_sum, MAX(quotes.tally) as my_max, MIN(quotes.tally) as my_min 
              FROM quotes
             WHERE quotes.prf_id = $id
               AND quotes.is_public = 1
        ";
        
        $res = $this->query($sql);
        
        if(!empty($res[0][0]['my_sum'])) {
            $ret['sum'] = $res[0][0]['my_sum'];
        }
        if(!empty($res[0][0]['my_max'])) {
            $ret['max'] = $res[0][0]['my_max'];
        }
        if(!empty($res[0][0]['my_min'])) {
            $ret['min'] = $res[0][0]['my_min'];
        }
        if(!empty($res[0][0]['total_quotes'])) {
            $ret['quote_count'] = $res[0][0]['total_quotes'];
        }
        
        return $ret;
    }
    
    function getQuotefileListing( $id ) {
        $sql = "SELECT id, title, tally FROM quotes WHERE prf_id = $id AND is_public = 1;";
        return mysql_query_assoc($sql);
        //$this->query($sql);
    }
    
    function getLeaderboard() {
        $sql = " SELECT COUNT(*) as quotecount, SUM(quotes.tally) as tallysum, prf_id FROM quotes WHERE is_public = 1 GROUP BY prf_id ORDER BY tallysum; ";
        return $this->query($sql);
    }
}