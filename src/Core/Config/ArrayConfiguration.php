<?php


namespace danperron\danmvc\Core\Config;

/**
 * Description of ArrayConfiguration
 *
 * @author dan
 */
class ArrayConfiguration implements IConfiguration {
    
    private $attributes = null;
    
    /**
     * 
     * @param array $configArray
     */
    function __construct($configArray) {
        $this->attributes = $configArray;
    }

    public function getAttributes() {
        return (object) $this->attributes;
    }

}
