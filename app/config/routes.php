<?
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));



    Router::connect('/quotes/*', array('controller' => 'quotes', 'action' => 'index'));










    /// plugin for spark_plug; keep on last line
    include_once(ROOT.'/app/plugins/spark_plug/config/routes.php');