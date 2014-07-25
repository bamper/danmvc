<?php

use danperron\danmvc\Core\MvcException;

/**
 * Description of UserDAO
 *
 * @author dan
 */
class UserDAO extends AbstractDAO {

    private static $TABLE_NAME = 'User';

    public function create($value) {

        /* @var $value User */


        $sql = 'INSERT INTO ' . self::$TABLE_NAME . '(firstName, lastName, emailAddress, password) VALUES (:firstName, :lastName, :emailAddress, :password);';

        $parameters = array('firstName' => $value->getFirstName(),
            'lastName' => $value->getLastName(),
            'emailAddress' => $value->getEmailAddress(),
            'password' => $value->getPassword());

        try {
            $statement = $this->pdo->prepare($sql);
            $this->pdo->beginTransaction();
            $statement->execute($parameters);
            $newUserId = $this->pdo->lastInsertId();
            $this->updateRoles($value);
            $this->pdo->commit();
            $value->setId($newUserId);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Error inserting user', 0, $e);
        }
    }

    /**
     * 
     * @param type $id
     * @return \User|null
     * @throws MvcException
     */
    public function findById($id) {

        try {
            $sql = 'SELECT userId AS id, firstName, lastName, emailAddress, password FROM ' . self::$TABLE_NAME . ' WHERE userId = :userId;';
            $statement = $this->pdo->prepare($sql);

            $statement->execute(array('userId' => $id));

            if ($statement->rowCount() > 0) {
                
                $user = $statement->fetchObject('User');
                
                $roles = $this->fetchUserRoles($user);

                return $user;
            } else {
                return null;
            }
        } catch (PDOException $exc) {
            throw new MvcException('Error fetching user', 0, $exc);
        }
    }

    /**
     * 
     * @param User $value
     * @throws MvcException
     */
    public function remove($value) {
        /* @var $value User */
        $sql = 'DELETE FROM ' . self::$TABLE_NAME . ' WHERE userId = :userId;';


        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('userId' => $value->getId()));

            $value = null;
        } catch (PDOException $e) {
            throw new MvcException('Error deleting user.', 0, $e);
        }
    }

    /**
     * 
     * @param User $value
     * @throws MvcException
     */
    public function update($value) {
        /* @var $value User */

        try {
            $sql = 'UPDATE ' . self::$TABLE_NAME . ' SET firstName = :firstName, '
                    . 'lastName = :lastName, '
                    . 'emailAddress = :emailAddress, '
                    . 'password = :password '
                    . 'WHERE userId = :userId;';

            $values = array();
            $values['userId'] = $value->getId();
            $values['firstName'] = $value->getFirstName();
            $values['lastName'] = $value->getLastName();
            $values['emailAddress'] = $value->getEmailAddress();
            $values['password'] = $value->getPassword();

            $statement = $this->pdo->prepare($sql);

            $this->pdo->beginTransaction();
            $statement->execute();
            $this->updateRoles($value);
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Error updating user', 0, $e);
        }
    }

    /**
     * 
     * @param User $user
     * @return \Role
     * @throws MvcException
     */
    private function fetchUserRoles(User $user) {

        try {
            $sql = 'SELECT r.roleId, r.name '
                    . 'FROM Role r '
                    . 'LEFT JOIN User_Role ur ON ur.roleId = r.roleId '
                    . 'WHERE ur.userId = :userId;';
            $statement = $this->pdo->prepare($sql);

            $statement->execute(array('userId' => $user->getId()));

            if ($statement->rowCount() > 0) {
                while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
                    $role = new Role();
                    $role->setId($row->roleId);
                    $role->setRoleName($row->name);
                    $user->addRole($role);
                }
            }
        } catch (PDOException $e) {
            throw new MvcException('Error fetching user roles.', 0, $e);
        }
    }

    /**
     * 
     * @param string $email
     * @return \User|null
     * @throws MvcException
     */
    public function findByEmail($email) {
        try {
            $sql = 'SELECT userId As id, firstName, lastName, emailAddress, password '
                    . 'FROM ' . self::$TABLE_NAME . ' WHERE emailAddress = :emailAddress;';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('emailAddress' => $email));
            if ($statement->rowCount() > 0) {
                $user = $statement->fetchObject('User');
                $this->fetchUserRoles($user);
                return $user;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new MvcException("Error finding user with email address $email", 0, $e);
        }
    }

    private function updateRoles(User $user) {
        $sql = 'DELETE FROM User_Role WHERE userId = :userId';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(array('userId' => $user->getId()));

        foreach ($user->getRoles() as $role) {
            $this->insertUserRole($user, $role);
        }
    }

    private function insertUserRole(User $user, Role $role) {
        $sql = 'INSERT INTO User_Role VALUES (:userId, :roleId);';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(array('userId' => $user->getId(), 'roleId' => $role->getId()));
    }

}
