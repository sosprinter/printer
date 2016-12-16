<?php

require_once 'Users.php';

class LoginController extends Zend_Controller_Action
{

    public function init()
    {$this->_logger = Zend_Registry::get("log");

    }

    public function indexAction(){

        $request = $this->getRequest();
        $loginForm = $this->getLoginForm();
        if($request->isPost()) {

            if($loginForm->isValid($request->getPost())) {
                $res = $this->checkAuth($loginForm->getValue('email'),$loginForm->getValue('password'));
                if($res !== "SUCCESS"){
                    $this->view->errorMessage = $res;
                }else {
                    $this->_redirect('/Map');
                }
            }
        }
        $this->view->loginForm = $loginForm;

    }

    protected function getLoginForm(){

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
              ->setAttrib("class","form-control input-lg")
              ->addErrorMessage('Email obbligatoria')
              ->setDecorators(array(
                    'Viewhelper',
                    array(
                        array('div' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'form-group')
                    )
                ));

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                 ->setAttrib("class","form-control input-lg")
                 ->addErrorMessage('Password obbligatoria.')
                 ->setDecorators(array(
                    'Viewhelper',
                    array(
                        array('div' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'form-group')
                    )
                 ));


        $remember = new Zend_Form_Element_Submit('remember');

        $remember->setAttrib("class","btn")
            ->setLabel('Ricordami')
            ->setDecorators(array(
               'PrepareElements',
               array('ViewScript', array('viewScript' => 'login/remember.phtml')),
        ));

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setAttrib("class","btn btn-primary btn-block btn-lg")
                ->setLabel('Login')
                ->setDecorators(array(
                    'PrepareElements',
                    array('ViewScript', array('viewScript' => 'login/login.phtml')),
                ));

        $loginForm = new Zend_Form();
        $loginForm->setAttrib("class","register-form");
        $loginForm->setAttrib("role" , "form");

        $loginForm->setAction($this->_request->getBaseUrl())
            ->setMethod('post')
            ->addElement($submit)
            ->addElement($email)
            ->addElement($password)
            ->addElement($remember);


        $loginForm->addDisplayGroup(array('email', 'password',"remember", "login"), 'myDisplayGroup');

        return $loginForm;
    }


    private function checkPassword($username,$password){

        if(empty($username)||empty($password))
            return false;

        require_once 'Users.php';
        $Users = new Users();
        $r = $Users->getPass($username);

        return ( md5($password)==$r[0]['password']) ? true : false ;
    }

    protected function checkAuth($email,$password){
        $log = Zend_Registry::get("log");
        $log->info("check for ".$email);

        if(empty($email) || empty($password))
            throw new Exception("error");

        $authAdapter = $this->getAuthAdapter();
        $authAdapter->setIdentity($email)->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        switch ($result->getCode()) {
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                return "FAILURE_IDENTITY_NOT_FOUND";
                break;
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                return "FAILURE_CREDENTIAL_INVALID";
                break;
            case Zend_Auth_Result::SUCCESS:
                $userInfo = $authAdapter->getResultRowObject(null, 'password');
                require_once 'Printer.php';
                require_once 'Feedback.php';
                require_once 'Location.php';
                require_once 'Resource.php';
                $printer = new Printer();
                $feedback = new Feedback();
                $location = new Location();
                $resource = new Resource();
                $printer = $printer->getPrinter($userInfo->id);
                $feedback_media = $feedback->getMediaFeedback($userInfo->id);
                $location = $location->getLocation($userInfo->id);
                $resource = $resource->getResource($printer[0]['id']);
                $userInfo->printer = $printer[0]['id'];
                $userInfo->feedback = $feedback_media;
                $userInfo->location = $location[0]['id'];
                $userInfo->resource = $resource[0]['id'];
                $authStorage = $auth->getStorage();
                $authStorage->write($userInfo);
                echo "SUCCESS";
                return "SUCCESS";
                break;
            default:
                echo "default";
                return "default";
                break;
        }

    }

    protected function getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)');

        return $authAdapter;
    }

    public function logoutAction(){
        # clear everything - session is cleared also!
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('index');
    }


}