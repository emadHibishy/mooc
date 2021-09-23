<?php


define('PATH', dirname( __FILE__ )  )    ;
define('DS', DIRECTORY_SEPARATOR);
define('APP', PATH . DS . 'app');
define('PLUGINS', APP . DS . 'plugins');
define('MODELS', APP . DS . 'models');
define('CONTROLLERS', APP . DS . 'controllers');
define('VIEWS', PATH . DS . 'views');
define('ASSETS', PATH. DS . 'assets');
define('TEMPLATES', PATH. DS . 'templates');


require_once APP . DS . 'config.php';
require_once APP . DS . 'redirect.php';
require_once APP . DS . 'general.functions.php';
require_once MODELS . DS . 'model.php';
require_once APP . DS . 'factory.php';
require_once CONTROLLERS . DS . 'controller.php';
require_once APP . DS . 'pluggable.php';

foreach(glob(PLUGINS . DS . '*.php') as $plugin)
{
    require_once $plugin;
}