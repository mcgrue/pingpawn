<?
class IrcLog extends AppModel {

    var $name = 'IrcLog';
    var $useTable = 'irc_logs';

    function get_daily_log($room, $day) {
        $room = mysql_real_escape_string(stripslashes($room));
        $day = mysql_real_escape_string(stripslashes($day));

        $query = "SELECT * FROM irc_logs WHERE room = '$room' AND `time` >= '$day 00:00:00' AND `time` <= '$day 23:59:59'  ORDER BY id DESC;";
pr2($query, "TACO");
        $res = $this->QUERY($query);

        return $res;
    }
