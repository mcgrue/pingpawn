<?

class QuotesController extends AppController {

	var $name = 'Quotes';
    
    function index( $id=null ) {
        //SELECT count(*) as total, prf_name FROM quotes GROUP BY prf_name ORDER BY total DESC
        if( $id ) {
            $res = $this->Quote->findById($id);
            
            if(isset($res['Quote'])) {
                $this->set('quote', $res['Quote']);
            }
        }
    }
}