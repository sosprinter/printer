<?php

defined('APPLICATION_PATH')|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_ENV')|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));
defined('APPLICATION_LOG_FOLDER') || define('APPLICATION_LOG_FOLDER', getenv('APPLICATION_LOG_ENV_FOLDER'));

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../application/classes'),
    realpath(APPLICATION_PATH . '/../application/modules'),
    realpath(APPLICATION_PATH . '/../application/models'),
)));

require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV , array('config' => array(APPLICATION_PATH.'/configs/application.ini')));
Zend_Controller_Action_HelperBroker::addPath(realpath(APPLICATION_PATH) .'/../application/views/helpers','Zend_View_Helper');
$application->bootstrap()->run();
