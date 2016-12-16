<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error..';
                break;
        }
        
        $exception = $errors->exception;

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($exception->getMessage(), $exception->getTraceAsString());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog($logName = 'log')
    {  
        if(!Zend_Registry::isRegistered($logName))
            return FALSE;
        return Zend_Registry::get($logName);
    }


}

