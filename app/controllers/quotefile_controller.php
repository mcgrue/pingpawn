<?
class QuotefileController extends AppController {

	var $name = 'Quotefile';
    var $uses = array('Prf','Quote','Vote');
    
    function view($pretty_url=NULL, $action=NULL) {
        $res = $this->Prf->findByUrlKey($pretty_url);
        
        if( !empty($res['Prf']) ) {
            
            $stats = $this->Prf->getStats($res['Prf']['id']);
            
            $this->set('prf', $res);
            $this->set('stats', $stats);
            
            $this->paginate = array(
                'limit' => 10,
                'conditions' => array(
                    'Quote.is_public' => 1,
                    'Quote.prf_id' => $res['Prf']['id']
                ), 
                'order' => array(
                    'Quote.tally' => 'desc'
                )
            );
            
            $data = $this->paginate('Quote');
            $this->set( 'data', $data );
                        
            if( !empty($this->sessuser) ) {
                $ar = array();
                foreach($data as $caca) {                   
                    $ar[] = $caca['Quote']['id'];
                }
                $res = $this->Vote->get($ar, $this->sessuser['User']['id']);
                
                $ar = array();
                foreach($res as $v) {
                    $ar[$v['votes']['quote_id']] = $v;
                }
                
                $this->set( 'vote', $ar );
            }
            
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