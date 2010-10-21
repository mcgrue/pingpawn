<?
	Router::connect('/', array('controller' => 'quotes', 'action' => 'recent'));
	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about'));
    Router::connect('/about/', array('controller' => 'pages', 'action' => 'display', 'about'));
    
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

    Router::connect('/quotes/delete/*', array('controller' => 'quotes', 'action' => 'delete')); 
    Router::connect('/quotes/update/*', array('controller' => 'quotes', 'action' => 'update'));    
    Router::connect('/quotes/rss/*', array('controller' => 'quotes', 'action' => 'rss'));
    Router::connect('/quotes/recent/*', array('controller' => 'quotes', 'action' => 'recent'));
    Router::connect('/quotes/manage/*', array('controller' => 'quotes', 'action' => 'manage'));
    Router::connect('/quotes/add/*', array('controller' => 'quotes', 'action' => 'add'));
    Router::connect('/quotes/*', array('controller' => 'quotes', 'action' => 'index'));
    Router::connect('/q/*', array('controller' => 'quotes', 'action' => 'index'));

    Router::parseExtensions('rss');
