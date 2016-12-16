<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


	protected function _initAutoload()
	{
	    $autoloader = Zend_Loader_Autoloader::getInstance();
	    $autoloader->registerNamespace('TwitterBootstrap_');
	}


	protected function _initConfig()
    {
    	Zend_Registry::set('config', new Zend_Config($this->getOptions()));
    }


	protected function _initMvc()
    {
    	Zend_Layout::startMvc();
    }

    protected function _initPlugins()
    {

    }

    protected function _initViewHelpers()
    {
    	$this->bootstrap('view');
    	$view = $this->getResource('view');
    	$view->addHelperPath('TwitterBootstrap/Helper/', 'TwitterBootstrap_Helper');
    }

	protected function _initCache()
	{

	}

    protected function _initDoctype()
    {
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->setEncoding('UTF-8');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    }

	protected function _initDate(){

    	date_default_timezone_set(Zend_Registry::get('config')->settings
    														  ->application
    														  ->datetime);
    }

     protected function _initLog(){

		 if($this->hasPluginResource("log")){
    		$r = $this->getPluginResource("log");
    		$log = $r->getLog();
    		Zend_Registry::set("log",$log);
    	}
    }

	protected function _initDatabases()
    {
    	$databases_config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/databases.ini', APPLICATION_ENV);
    	$sp_database = Zend_Db::factory($databases_config->sp->database);
    	Zend_Registry::set("sp_db", $sp_database );
		Zend_Db_Table::setDefaultAdapter($sp_database );

    }



	public function preDispatch () {
	 /* $this->_controller= $this->_front->getRequest()->getControllerName();
	  if (($this->_controller!='index') && ($this->_controller!='error')) {
	    if (Globals::getConfig()->authentication->active) {
	      $this->checkSession();
	    }
	  }*/
	}
	/**
	 * checkSession
	 */
	private function checkSession() {
	  //  if (empty(Globals::getSession()->username)) {
	  //  $this->_response->setRedirect('/index/login')->sendResponse();
	  // die();
	  // }
	}

}
