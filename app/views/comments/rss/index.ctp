<?
$this->set('documentData', array(
    'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
);

$this->set('channelData', array(
    'title' => __("PingPawn - Most Recent Comments", true),
    'link' => $this->Html->url('/', true),
    'description' => __("Most recent comments.", true),
    'language' => 'en-us')
);
    
foreach ($comments as $c) {
        $q = $c['Quote'];
        $c = $c['Comment'];

        $time = strtotime($c['created']);
        
        $title = $c['name'].' on ('.$q['prf_name']. ') #'.$q['id'];
        
        $link = array(
            'controller' => 'quotes',
            'action' => 'index',
            $q['id']
        );
        
        // You should import Sanitize
        App::import('Sanitize');

        // This is the part where we clean the body text for output as the description 
        // of the rss item, this needs to have only text to make sure the feed validates
        $bodyText = preg_replace('=\(.*?\)=is', '', $c['body']);
        $bodyText = Sanitize::stripAll($bodyText);

        echo  $this->Rss->item(
            array(),
            array(
                'title' => $title,
                'link' => $link,
                'guid' => array('url' => $link, 'isPermaLink' => 'true'),
                'description' =>  $bodyText,
                'dc:creator' => $c['name'],
                'pubDate' => $time
            )
        );
    }