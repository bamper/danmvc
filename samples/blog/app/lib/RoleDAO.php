<?php

use danperron\danmvc\Core\MvcException;

/**
 * Description of RoleDAO
 *
 * @author dan
 */
class RoleDAO {

    private $pdo = null;

    /**
     * 
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 
     * @return Role
     * @throws MvcException
     */
    public function fetchAll() {
        try {
            $sql = 'SELECT roleId, name FROM Role;';
            $statement = $this->pdo->query($sql);
            $roles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $role = new Role();
                $role->setId($row['roleId']);
                $role->setRoleName($row['name']);
                $roles[] = $role;
            }
            return $roles;
        } catch (PDOException $e) {
            throw new MvcException('Unable to fetch Roles', 0, $e);
        }
    }

    /**
     * 
     * @param int $id
     * @return \Role
     * @throws MvcException
     */
    public function findById($id) {
        try {
            $sql = 'SELECT roleId, name FROM Role WHERE roleId = :roldId;';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('roleId' => $id));

            if ($statement->rowCount() > 0) {
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                $role = new Role();
                $role->setId($row['roleId']);
                $role->setRoleName($row['name']);
                return $role;
            }
        } catch (PDOException $e) {
            throw new MvcException('Error fetching role', 0, $e);
        }
    }

    
    /**
     * 
     * @param string $roleName
     * @return \Role
     * @throws MvcException
     */
    public function findByName($roleName) {
        try {
            $sql = 'SELECT roleId, name FROM Role WHERE name = :name;';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('name' => $roleName));
            
            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                $role = new Role();
                $role->setId($row['roleId']);
                $role->setRoleName($row['name']);
                return $role;
            }
        } catch (PDOException $e) {
            throw new MvcException('Error fetching role', 0, $e);
        }
    }

}
