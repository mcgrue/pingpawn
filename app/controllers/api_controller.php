<?
class ApiController extends AppController {

	var $name = 'Api';
    
    var $uses = array('Quote');

    function _output($res=NULL) {
        if(empty($res[0])) {
            $res = array(array('Error'=>'Sorry, no.'));
        }
        echo( json_encode($res[0]) );
        die();
    }

    function rand($from=NULL) {
        $res = NULL;
        if($from===NULL) {
            $res = $this->Quote->query( "SELECT * FROM `quotes` WHERE is_public = 1 ORDER BY RAND() LIMIT 1" );
        } else {
            $from = mysql_real_escape_string($from);
            $res = $this->Quote->query( "SELECT q.* FROM `quotes` q, `prfs` p WHERE p.name = '$from' AND p.id = q.prf_id AND q.is_public = 1 ORDER BY RAND() LIMIT 1" );
        }
        $this->_output($res);
    }

    function search($from=NULL, $idx=NULL) {
        $res = NULL;
        if(!empty($_GET['q'])) {
            $q = mysql_real_escape_string($_GET['q']);
            $q = str_replace( '*', '%', $q );
            
            if($from===NULL) {
                $res = $this->Quote->query( "SELECT * FROM `quotes` WHERE is_public = 1 AND original_quote LIKE '%$q%' ORDER BY RAND() LIMIT 1" );
            } else {
                $from = mysql_real_escape_string($from);
                $res = $this->Quote->query( "SELECT q.* FROM `quotes` q, `prfs` p WHERE p.name = '$from' AND q.original_quote LIKE '%$q%' AND p.id = q.prf_id AND q.is_public = 1 ORDER BY RAND() LIMIT 1" );
            }
        }
        $this->_output($res);
    }
}