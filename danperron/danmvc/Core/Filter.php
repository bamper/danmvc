<?php

namespace danperron\danmvc\Core;

/**
 * Description of Filter
 *
 * @author dan
 */
abstract class Filter {

    protected $app;
    protected $session;
    protected $input;

    public function __construct(Application $app) {
        $this->app = $app;
        $this->session = Session::getInstance();
        $this->input = Input::getInstance();
    }

    abstract function preFilter();

    abstract function postFilter();
}
