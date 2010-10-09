<?
	Router::connect('/', array('controller' => 'quotes', 'action' => 'recent'));
	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about'));
    Router::connect('/about/', array('controller' => 'pages', 'action' => 'display', 'about'));
    
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
    
    Router::connect('/quotes/recent/*', array('controller' => 'quotes', 'action' => 'recent'));
    Router::connect('/quotes/manage/*', array('controller' => 'quotes', 'action' => 'manage'));
    Router::connect('/quotes/add/*', array('controller' => 'quotes', 'action' => 'add'));
    Router::connect('/quotes/*', array('controller' => 'quotes', 'action' => 'index'));

    /// plugin for spark_plug; keep on last line
    include_once(ROOT.'/app/plugins/spark_plug/config/routes.php');