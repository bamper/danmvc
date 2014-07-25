<?php

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\Log\Logger;

/**
 * Description of LoginController
 *
 * @author dan
 */
class LoginController extends AbstractBlogController {

    /**
     *
     * @var UserDAO
     */
    private $userDAO;

    public function __construct(Application $app) {
        parent::__construct($app);
        $this->userDAO = new UserDAO($app->getProperty('PDO'));
    }

    public function showLoginForm() {
        
        if($this->session->has('user')){
            $this->app->redirect($this->app->getBaseUri());
        }
        
        
        $this->view->render('login');
    }

    public function doLogin() {
        //Do login.

        try {
            $emailAddress = $this->input->getPostField('email');
            $password = $this->input->getPostField('password');
            
            $user = $this->userDAO->findByEmail(trim($emailAddress));
            
            if($user != null && $user->getPassword() === md5(trim($password))){
                $this->session->set('user', $user);
                $this->app->redirect($this->app->getBaseUri());
            } else {
                $this->view->assign('errorMessage', 'Invalid Login');
            }
            
        } catch (Exception $e) {
            Logger::getInstance()->logException($e);
            $this->view->assign('errorMessage', 'Login Error: ' . $e->getMessage());
        }
        $this->view->render('login');
    }
    
    public function doLogout(){
        $this->session->remove('user');
        $this->app->redirect($this->app->getBaseUri() . '/login');
    }

}
