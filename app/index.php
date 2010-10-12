<?

/**
 *  Get Cake's root directory
 */
        define('APP_DIR', 'app');
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', dirname(__FILE__));
        define('WEBROOT_DIR', 'webroot');
        define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
/**
 * This only needs to be changed if the "cake" directory is located
 * outside of the distributed structure.
 * Full path to the directory containing "cake". Do not add trailing directory separator
 */
        if (!defined('CAKE_CORE_INCLUDE_PATH')) {
                define('CAKE_CORE_INCLUDE_PATH', ROOT);
        }

/**
 * Set the include path or define app and core path
 */
        if (function_exists('ini_set')) {
                ini_set('include_path',
                        ini_get('include_path') . PATH_SEPARATOR . CAKE_CORE_INCLUDE_PATH
                        . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS
                );
                define('APP_PATH', null);
                define('CORE_PATH', null);
        } else {
                define('APP_PATH', ROOT . DS . APP_DIR . DS);
                define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
        }
        require APP_DIR . DS . WEBROOT_DIR . DS . 'index.php';
