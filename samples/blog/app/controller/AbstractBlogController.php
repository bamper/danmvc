<?php

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\Controller;
use danperron\danmvc\Core\MvcException;

/**
 * Description of AbstractBlogController
 *
 * @author dan
 */
abstract class AbstractBlogController extends Controller {

    /**
     *
     * @var User
     */
    private $user;

    public function __construct(Application $app) {
        parent::__construct($app);
        $this->user = $this->session->get('user');
        $this->view->assign('isLoggedIn', $this->session->has('user'));
        $this->view->assign('user', $this->user);
    }

    protected function authorizeUser() {

        /* @var $user User */
        if (!$this->user) {
            $this->app->redirect($this->app->getBaseUri() . '/login');
            return;
        } else if (!$this->user->isInRole(Role::ROLE_AUTHOR)) {
            $this->view->render('unauthorized.');
            return;
        }
    }

    protected function requireRole($roleName) {
        if (!$this->user) {
            $this->app->redirect($this->app->getBaseUri() . '/login');
            return;
        } else if (!$this->user->isInRole($roleName)) {
            throw new MvcException('Unauthorized user', MvcException::ERROR_CODE_UNAUTHORIZED);
        }
    }

}
