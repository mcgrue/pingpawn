<?php

Configure::write('Project',array('name'=>'Spark Plug Cakephp Plugin'));


Configure::write(
    'UserPermissions',
    array(
        'controllers/Posts/index',
        'controllers/Posts/edit',
        'controllers/Websites'
    )
);

Configure::write('rootURL','pingpawn.com/spark');
Configure::write('httpRootURL','http://pingpawn.com/spark');
Configure::write('projectName','Spark Plug Cakephp Plugin');
Configure::write('logged-in-menu','logged_in_menu');
Configure::write('front_end_layout','default');
Configure::write('dashboard_layout','default');

Configure::write('SparkPlug.open_registration', true);
Configure::write('SparkPlug.default_groupid_for_registration', 2); // id of the group used to register new users via users/register
Configure::write('SparkPlug.register_defaults.optin', false);
Configure::write('SparkPlug.register_defaults.agreement', false);

// if the user tries to login with his facebook account and he has not any associated spark user, a new user will be created and linked with his facebook email
Configure::write('SparkPlug.auto_register_facebook_accounts', true);
Configure::write('SparkPlug.default_group_for_new_facebook_accounts', 2);

Configure::write('SparkPlug.redirectOriginAfterLogin', false); // used to redirect after login to the page that triggered the login action
Configure::write('SparkPlug.loginRedirect', '/users/dashboard'); // default url to login to (used also by the facebook autologin feature)
Configure::write('SparkPlug.registerRedirect', false);
Configure::write('SparkPlug.registerAutoLogin', false);
Configure::write('SparkPlug.administrator.from_name', 'PingPawn Mail Robot');
Configure::write('SparkPlug.administrator.email', 'quotemaster@pingpawn.com');
Configure::write('SparkPlug.hash.method', 'md5');	// use sha1 to be compatible with passwords generated by Cake's Auth Component
Configure::write('SparkPlug.hash.salt', false);		// use true to be compatible with passwords generated by Cake's Auth Component
Configure::write('SparkPlug.allow.login_as_user', false);

// creating a specific cache config for rules
Cache::config('SparkPlug', array(  
    'engine' => 'File',  
    'duration'=> '+3 months',  
    'path' => CACHE,  
    'prefix' => 'SparkPlug_'
));


/**
 * Main entry point to the plugin. This should be called from the app_controller beforeFilter OR 
 * from the beforeFilter of the controllers you wish to protect
 * @param $controller  
 * @return unknown_type
 */
function SparkPlugIt(&$controller)
{
    $pageRedirect = $controller->Session->read('permission_error_redirect');
    $controller->Session->delete('permission_error_redirect');

    //TODO: Check why is this model used
    $controller->company_id = $controller->Session->read('Company.id');
    
    // check if the controller $uses = null to bind the required models before continue. This corrects issue 
    if (!is_array($controller->uses) || !in_array('SparkPlug.UserGroup', $controller->uses)){
    	//debug ('binding SparkPlug.UserGroup model');
    	//code from http://www.pseudocoder.com/archives/one-more-tip-for-speeding-up-cakephp-apps
    	//the loadModel way
		$controller->loadModel('SparkPlug.UserGroup');
		//end of code from http://www.pseudocoder.com/archives/one-more-tip-for-speeding-up-cakephp-apps
    }

    if (empty($pageRedirect))
    {
        $actionUrl = $controller->params['url']['url'];
        if ($actionUrl != 'users/login'){
        	$controller->Session->write('SparkPlug.OriginAfterLogin', '/'.$actionUrl);
        }

//        if (isset($controller->params['slug']))
//            $website = $controller->Website->find('Website.subdomain = "'.$controller->params['slug'].'"');
//        else
//            $website = null;
//
//        if (!$website)
//        {
            $user = $controller->Authsome->get();
            if (!$user)
            {
                //anonymous?
                if (!$controller->UserGroup->isGuestAccess($actionUrl))
                {
                    $controller->Session->write('permission_error_redirect','/users/login');
                    $controller->Session->setFlash('Please login to view this page.');

                    $controller->redirect('/users/login');
                }
            }
            else
            {
                if (!$controller->UserGroup->isUserGroupAccess($user['User']['user_group_id'],$actionUrl))
                {
                    $controller->Session->write('permission_error_redirect','/users/login');
                    $controller->Session->setFlash('Sorry, You don\'t have permission to view this page. '.$user['User']['user_group_id'].':('.$actionUrl.')');

                    //$controller->redirect(Configure::read('SparkPlug.loginRedirect'));
                    $controller->redirect('/errors/unauthorized');
                }
            }
//        }
    }
}
?>