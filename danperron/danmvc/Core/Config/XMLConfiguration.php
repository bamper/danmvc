<?php
namespace danperron\danmvc\Core\Config;


/**
 * XMLConfiguration is not yet implemented.  It is intended to parse an XML 
 * configuration to be sent to Application.
 *
 * @author dan
 */
class XMLConfiguration implements IConfiguration{
    
    
    public function getAttributes() {
        throw new Exception("Not yet implemented");
    }

}
