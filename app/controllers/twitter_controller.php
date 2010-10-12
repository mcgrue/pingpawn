<?

// SELECT * FROM quotes WHERE length(quote) <= 118

class TwitterController extends AppController {
    
	var $name = 'Twitter';
    
    var $components = array('RequestHandler', 'Cookie'); 
}