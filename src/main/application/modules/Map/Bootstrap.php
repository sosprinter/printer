<?php 
	
	/*
	* 
	* In a multi-module application, all the module bootstraps will run. 
	* Therefore, the last one that runs ends up overriding all the others.
	*/
	class Map_Bootstrap extends Zend_Application_Module_Bootstrap
	{


	}

?>