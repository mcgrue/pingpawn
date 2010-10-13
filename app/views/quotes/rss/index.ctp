<?
$this->set('documentData', array(
    'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
);

$this->set('channelData', array(
    'title' => __("Most Recent Quotes", true),
    'link' => $this->Html->url('/', true),
    'description' => __("Most recent quotes.", true),
    'language' => 'en-us')
);
    
foreach ($quotes as $quote) {

        $time = strtotime($quote['Quote']['time_added']);
        
        $title = '('.$quote['Prf']['name']. ') #'.$quote['Quote']['id'];
        
        $link = array(
            'controller' => 'quotes',
            'action' => 'index',
            $quote['Quote']['id']
        );
        
        // You should import Sanitize
        App::import('Sanitize');

        // This is the part where we clean the body text for output as the description 
        // of the rss item, this needs to have only text to make sure the feed validates
        $bodyText = preg_replace('=\(.*?\)=is', '', $quote['Quote']['quote']);
        $bodyText = nl2br(htmlentities($bodyText));
        $bodyText = Sanitize::stripAll($bodyText);
        $bodyText = substr($bodyText, 0, 400);
        
        if( strlen($bodyText) == 400 ) $bodyText .= '...';

        echo  $this->Rss->item(
            array(),
            array(
                'title' => $title,
                'link' => $link,
                'guid' => array('url' => $link, 'isPermaLink' => 'true'),
                'description' =>  $bodyText,
                'dc:creator' => $quote['Prf']['name'],
                'pubDate' => $time
            )
        );
    }