<?php

/**
 * Description of Category
 *
 * @author dan
 */
class Category {
    
    private $id = 0;
    private $name = '';
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

}
