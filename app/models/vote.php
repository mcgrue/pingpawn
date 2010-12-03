<?
class Vote extends AppModel {
    var $name = 'Vote';
    
    function get( $unk, $cid ) {
        if( is_array($unk) ) {
            return $this->_get_many($unk, $cid);
        } else {
            return $this->_get_one($unk, $cid);
        }
    }
    
    function _get_one( $qid, $uid ) {
        return $this->query( "SELECT * FROM `votes` WHERE `quote_id`= $qid AND `user_id` = $uid;" );
    }
    
    function _get_many( $qids, $uid ) {
        
        $csv = join(',',$qids);
        
        return $this->query( "SELECT * FROM `votes` WHERE `quote_id` IN ($csv) AND `user_id` = $uid;" );
    }

    function reverse_decision($qid, $uid, $vote) {
        $this->query( "UPDATE `votes` SET `time_voted` = NOW(), `vote` =  $vote WHERE `quote_id` = $qid AND `user_id` = $uid;" );
        $vote = $vote + $vote;
        $this->query( "UPDATE `quotes` SET `tally` = `tally` + $vote WHERE id = $qid; " );
    }

    function perform_civic_duty( $qid, $uid, $vote) {
        $this->query( "INSERT INTO `votes`(`quote_id`, `user_id`, `time_voted`, `vote` ) VALUES ($qid, $uid, NOW(), $vote);" );
        $this->query( "UPDATE `quotes` SET `tally` = `tally` + $vote, total_votes = total_votes + 1 WHERE id = $qid; " );
    }
    
    function get_voter_counts($limit) {
        return $this->query("
            SELECT count(v.user_id) as votecount, sum(v.vote) as tally, v.user_id as uid, u.display_name as uname
              FROM votes v, users u
             WHERE v.user_id = u.id
             GROUP BY v.user_id
             ORDER BY votecount DESC
              LIMIT $limit;
        ");
    }

    function _vote_schema_update_dec2010() {
        $res = $this->query( "SELECT quote_id, COUNT(*) FROM votes GROUP BY quote_id;" );
        foreach( $res as $r ) {
            
            $qid = $r['votes']['quote_id'];
            $cnt = $r[0]['COUNT(*)'];
            
            $this->query( "UPDATE quotes SET total_votes = $cnt WHERE id = $qid; " );
        }
        $this->query( "UPDATE quotes SET total_votes = 0 WHERE total_votes IS NULL; " );
        pr2('JEGUS DOEN');
    }
}