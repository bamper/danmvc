<?php
namespace danperron\danmvc\Core\Config;

use danperron\danmvc\Core\MvcException;
use Exception;

/**
 * Description of JSONConfiguration
 *
 * @author dan
 */
class JSONConfiguration implements IConfiguration {
    
    /**
     *
     * @var stdObject
     */
    private $attributes = null;
    
    function __construct($jsonFile) {
        
        if(!file_exists($jsonFile)){
            throw new Exception("$jsonFile does not exist.");
        }
        
        $fileContents = file_get_contents($jsonFile);
        
        $jsonArray = json_decode($fileContents,TRUE);
        
        if(!$jsonArray){
            throw new MvcException("$jsonFile is not readable or not valid json");
        }
        
        $this->attributes = $jsonArray;
    }

    
    public function getAttributes() {
        return $this->attributes;
    }
}
