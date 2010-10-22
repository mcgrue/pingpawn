<?
class Vote extends AppModel {
    var $name = 'Vote';
    
    function get( $qid, $uid ) {
        return $this->query( "SELECT * FROM `votes` WHERE `quote_id`= $qid AND `user_id` = $uid;" );
    }

    function perform_civic_duty( $qid, $uid, $vote) {
        $this->query( "INSERT INTO `votes`(`quote_id`, `user_id`, `time_voted`, `vote` ) VALUES ($qid, $uid, NOW(), $vote);" );
        $this->query( "UPDATE `quotes` SET `tally` = `tally` + $vote WHERE id = $qid; " );
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
}