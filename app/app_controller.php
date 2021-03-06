<?

class AppController extends Controller {
    public $components = array(
        'Session',
        'Authsome.Authsome' => array(
            'model' => 'User'
        )
    );
    
    function beforeFilter() {
        if( !is_sandbox() ) {
            if( $_SERVER['HTTP_HOST'] != 'pingpawn.com' ) {
                
                Header( "HTTP/1.1 301 Moved Permanently" );
                header( 'Location: http://pingpawn.com'.$_SERVER['REQUEST_URI'] );
                die();
            }
        }
        
        parent::beforeFilter();
        $this->sessuser = Authsome::get();
        $this->set('sessuser', $this->sessuser);
    }
    
    function flashAndGo( $flash, $go, $response=302 ) {
        $this->Session->setFlash($flash);
        $this->redirect($go, $response);            
    }
}

function can_edit($sessuser, $quote) {
    if(empty($sessuser)) {
        return false;
    }
    
    if( $sessuser['User']['is_admin'] ) {
        return true;
    }
    
    $uid = $sessuser['User']['id'];
    
    if(!isset($quote['Quote']['user_id']) && !isset($quote['Prf']['user_id'])) {
        return false;
    }
        
    return ( $uid == $quote['Quote']['user_id'] || $uid == $quote['Prf']['user_id'] );    
}

function is_alphanum($char) {
    
    if( strlen($char) != 1 ) {
        return false;
    }

    $ascii = ord($char);

    if( !(($ascii >= 48 && $ascii <= 57) || ($ascii >= 65 && $ascii <= 90) || ($ascii >= 97 && $ascii <= 122)) ) {
        return false;
    }
    
    return true;
}

class url_token {

    function tokenize($input) {
        
        $input = str_replace( "'", '', $input );
        $input = str_replace( "'", '', $input );
        $input = strtolower($input);
        
        $output = '';
        $last = '';
        
        for( $i=0; $i<strlen($input); $i++ ) {
            $c = substr( $input, $i, 1 );
            
            if( !is_alphanum($c) ) {
                $c = '-';
            }
            
            if( $c != '-' ) {
                $output .= $c;
            } else if( $last != '-' ) {
                $output .= $c;
            }
            
            $last = $c;
        }
        
        if( substr($output, strlen($output)-1, 1) == '-' ) {
            $output = substr($output, 0, strlen($output)-1);
        }
        
        if( substr($output, 0, 1) == '-' ) {
            $output = substr($output, 1);
        }
        
        if(!$output) {
        	$output = 'unk';
        }
        
        return $output;
    }
}