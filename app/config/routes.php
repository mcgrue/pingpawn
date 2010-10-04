<?
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));



    Router::connect('/quotes/*', array('controller' => 'quotes', 'action' => 'index'));
