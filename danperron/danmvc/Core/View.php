<?php

namespace danperron\danmvc\Core;

/**
 * Description of View
 *
 * @author dan
 */
class View {

    /**
     *
     * @var type 
     */
    private $app = null;
    private $vars = array();

    private $masterView = '';
    private $useMaster = false;
    
    function __construct(Application $app) {
        $this->app = $app;
        if($app->getConfig()->useMasterView){
            $this->useMaster = true;
            $this->masterView = $app->getConfig()->masterView;
        }
    }

    public function render($viewName) {
        if($this->useMaster){
            $this->renderMaster($viewName);
            return;
        }
        
        $fileName = $this->app->getConfig()->viewDir . DIRECTORY_SEPARATOR . $viewName . '.php';
        if(!file_exists($fileName)){
            throw new MvcException("View not found: $viewName");
        }
        extract($this->vars);
        include $fileName;
    }
    
    private function renderMaster($viewName){
        $masterViewFile = $this->app->getConfig()->viewDir . DIRECTORY_SEPARATOR . $this->masterView . '.php';
        if(!file_exists($masterViewFile)){
            throw new MvcException("Master View file not found: $this->masterView");
        }
        extract($this->vars);
        $outlet = $this->fetch($viewName);
        include $masterViewFile;
    }

    public function fetch($viewName) {
        $fileName = $this->app->getConfig()->viewDir . DIRECTORY_SEPARATOR . $viewName . '.php';
        if(!file_exists($fileName)){
            throw new MvcException("View not found: $viewName");
        }
        
        ob_start();
        extract($this->vars);
        include $fileName;
        $viewContents = ob_get_contents();
        ob_end_clean();
        return $viewContents;
    }

    public function assign($key, $value) {
        $this->vars[$key] = $value;
    }

    public function assignArray($array) {
        foreach ($array as $key => $value) {
            $this->vars[$key] = $value;
        }
    }

}
