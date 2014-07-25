<?php

/**
 * Description of User
 *
 * @author dan
 */
class User {

    private $id = 0;
    private $password = '';
    private $roles = array();
    private $firstName = '';
    private $lastName = '';
    private $emailAddress = '';

    public function getId() {
        return $this->id;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmailAddress() {
        return $this->emailAddress;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function addRole(Role $role) {
        if (!$this->isInRole($role->getRoleName())) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(Role $role) {
        foreach ($this->roles as $key => $value) {
            if ($role->getId() == $value->getId()) {
                unset($this->roles[$key]);
                //reindex array
                $this->roles = array_values($this->roles);
                break;
            }
        }
    }

    /**
     * 
     * @param type $roleName
     * @return boolean
     */
    public function isInRole($roleName) {
        foreach ($this->roles as $i) {
            /* @var $i Role */
            if ($i->getRoleName() == $roleName) {
                return true;
            }
        }
        return false;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
    }

}
