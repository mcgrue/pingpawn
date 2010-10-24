<?
class QuotefileController extends AppController {

	var $name = 'Quotefile';
    var $uses = 'Prf';
    
    function view($pretty_url=NULL, $action=NULL) {
        $res = $this->Prf->findByUrlKey($pretty_url);
        
        if( !empty($res['Prf']) ) {
            
            $stats = $this->Prf->getStats($res['Prf']['id']);
            
            $this->set('prf', $res);
            $this->set('stats', $stats);
        } else {
            $this->cakeError('error404', array());
        }
        
        if( $action != NULL ) {
            switch($action) {
                case 'listing':
                    $res = $this->Prf->getQuotefileListing($res['Prf']['id']);
                    pr2($res, 'listing');
                    break;
                default:
                    $this->cakeError('error404', array());
            }
        }
    }
    
    function lookup($id=NULL) {
        $res = $this->Prf->findById($id);
        
        if( !empty($res['Prf']['url_key']) ) {
            $this->redirect( '/quotefile/'.$res['Prf']['url_key'], 301 );
        } else {
            $this->cakeError('error404', array());
        }
    }
}