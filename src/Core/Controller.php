<?php

namespace danperron\danmvc\Core;

/**
 * Description of Controller
 *
 * @author dan
 */
class Controller {

    /**
     *
     * @var Application
     */
    protected $app = null;

    /**
     *
     * @var Session 
     */
    protected $session = null;

    /**
     *
     * @var View 
     */
    protected $view = null;

    /**
     *
     * @var Input 
     */
    protected $input = null;

    function __construct(Application $app) {
        $this->app = $app;
        $this->view = new View($app);
        $this->session = Session::getInstance();
        $this->input = Input::getInstance();
        $this->view->assign('baseUrl', $app->getBaseUri());
        $this->view->assign('session', $this->session);
        $this->view->assign('input', $this->input);
    }

}
