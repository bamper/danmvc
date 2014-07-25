<?php

use danperron\danmvc\Core\MvcException;

/**
 * Description of CategoryDAO
 *
 * @author dan
 */
class CategoryDAO extends AbstractDAO {

    public function create($value) {
        /* @var $value Category */
        try {
            $sql = 'INSERT INTO Category (name) VALUES (:name);';
            $statement = $this->pdo->prepare($sql);

            $statement->execute(array('name' => $value->getName()));
            $value->setId($this->pdo->lastInsertId());
        } catch (PDOException $e) {
            throw new MvcException('Unable to create Category', 0, $e);
        }
    }

    /**
     * 
     * @param int $id
     * @return \Category|null
     * @throws MvcException
     */
    public function findById($id) {
        try {
            $sql = 'SELECT categoryId, name FROM Category WHERE categoryId = :id';
            $statement = $this->pdo->prepare($sql);

            $statement->execute(array('id' => $id));

            if ($statement->rowCount() > 0) {
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                $category = new Category();
                $category->setId($row['categoryId']);
                $category->setName($row['name']);
                return $category;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new MvcException('Unable to find category by id', 0, $e);
        }
    }

    /**
     * 
     * @param Category $value
     * @throws MvcException
     */
    public function remove($value) {
        /* @var $value Category */
        try {
            $sql = 'REMOVE FROM Category WHERE categoryId = :id';
            $statement = $this->pdo->prepare($sql);
            $this->pdo->beginTransaction();
            $statement->execute(array('id' => $value->getId()));
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Unable to remove category', 0, $e);
        }
    }

    /**
     * 
     * @param Category $value
     * @throws MvcException
     */
    public function update($value) {
        /* @var $value Category */
        try {
            $sql = 'UPDATE Category SET name=:name WHERE categoryId=:id';
            $statement = $this->pdo->prepare($sql);
            $this->pdo->beginTransaction();
            $statement->execute(array('id' => $value->getId(), 'name' => $value->getName()));
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Unable to update category', 0, $e);
        }
    }

    /**
     * 
     * @param string $name
     * @return \Category
     * @throws MvcException
     */
    public function findOrCreate($name) {
        try {
            
            $sql = 'SELECT categoryId, name FROM Category WHERE name=:name;';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('name' => $name));
            
            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                $category = new Category();
                $category->setId($row['categoryId']);
                $category->setName($row['name']);
                return $category;
            } else {
                $category = new Category();
                $category->setName($name);
                $this->create($category);
                return $category;
            }
            
        } catch (PDOException $e) {
            throw new MvcException('Unable to create category by name', 0, $e);
        }
    }

}
