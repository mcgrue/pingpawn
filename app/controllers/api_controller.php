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
   	$size_limit = $_GET['size_limit'];

        $res = NULL;

	if( $size_limit == NULL || (int)$size_limit <= 0 ) {
		$size_limit = "";
	} else {
		$size_limit = (int)$size_limit;
		$size_limit = " AND LENGTH(q.quote) < $size_limit ";
	}

        if($from===NULL) {
            $res = $this->Quote->query( "SELECT * FROM `quotes` q WHERE is_public = 1 $size_limit ORDER BY RAND() LIMIT 1" );
        } else {
            $from = mysql_real_escape_string($from);
            $res = $this->Quote->query( "SELECT q.* FROM `quotes` q, `prfs` p WHERE p.name = '$from' $size_limit AND p.id = q.prf_id AND q.is_public = 1 ORDER BY RAND() LIMIT 1" );
        }

        $this->_output($res);
    }

    function search($from=NULL, $idx=NULL) {
        $size_limit = $_GET['size_limit'];

        $res = NULL;

        if( $size_limit == NULL || (int)$size_limit <= 0 ) {
                $size_limit = "";
        } else {
                $size_limit = (int)$size_limit;
                $size_limit = " AND LENGTH(q.quote) < $size_limit ";
        }
 

	if(!empty($_GET['q'])) {
            $q = mysql_real_escape_string($_GET['q']);
            $q = str_replace( '*', '%', $q );
            
            if($from===NULL) {
                $sql = "SELECT q.* FROM `quotes` q WHERE q.is_public = 1 $size_limit AND q.original_quote LIKE '%$q%' ";
            } else {
                $from = mysql_real_escape_string($from);
                $sql = "SELECT q.* FROM `quotes` q, `prfs` p WHERE p.name = '$from' $size_limit AND q.original_quote LIKE '%$q%' AND p.id = q.prf_id AND q.is_public = 1 ";
            }

            if( is_numeric($idx) ) {
                $idx = (int)$idx;
                $idx--;
                if( $idx < 0 ) {
                    $this->_output($res);
                }
                $sql .= " ORDER by q.id LIMIT $idx,1";
            } else {
                $sql .= " ORDER BY RAND() LIMIT 1 ";
            }

            $res = $this->Quote->query($sql);
        }
        $this->_output($res);
    }

    function count($from=NULL) {
        $res = NULL;
        if( !empty($_GET['q']) ) {
            $q = mysql_real_escape_string($_GET['q']);
            $q = str_replace( '*', '%', $q );

            if( $from === NULL ) {
                $sql = "SELECT COUNT(*) as cnt FROM `quotes` q WHERE q.is_public = 1 AND q.original_quote LIKE '%$q%' ";
            } else {
                $from = mysql_real_escape_string($from);
                $sql = "SELECT COUNT(*) as cnt FROM `quotes` q, `prfs` p WHERE p.name = '$from' AND q.original_quote LIKE '%$q%' AND p.id = q.prf_id AND q.is_public = 1 ";
            }
            
            $res = $this->Quote->query($sql);
        }
        $this->_output($res);
    }
}
