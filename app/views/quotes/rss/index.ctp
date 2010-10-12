<?
$this->set('documentData', array(
    'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
);

$this->set('channelData', array(
    'title' => __("Most Recent Posts", true),
    'link' => $this->Html->url('/', true),
    'description' => __("Most recent quotes.", true),
    'language' => 'en-us')
);
    
foreach ($quotes as $quote) {

/*
    [Quote] => Array
        (
            [id] => 4730
            [quote] => dskfl;lsdfj dsflkfj
            [prf_name] => foo
            [active] => 0
            [url_key] => 
            [title] => 
            [time_added] => 2010-10-10 15:56:01
        )
*/


        $time = strtotime($quote['Quote']['time_added']);
        
        $title = $quote['Quote']['title'] or '#'.$quote['Quote']['id'];
        
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
                'dc:creator' => $quote['Quote']['prf_name'],
                'pubDate' => $time
            )
        );
    }