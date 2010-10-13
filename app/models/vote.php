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
}