<?php

/**
 * Description of Role
 *
 * @author dan
 */
class Role {
    
    const ROLE_ADMIN = 'ADMIN';
    const ROLE_AUTHOR = 'AUTHOR';
    const ROLE_COMMENTOR = 'COMMENTOR';
    
    
    private $id = 0;
    private $roleName = '';
    
    public function getId() {
        return $this->id;
    }

    public function getRoleName() {
        return $this->roleName;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setRoleName($roleName) {
        $this->roleName = $roleName;
    }

}
