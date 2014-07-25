<?php

/**
 * Description of AbstractDAO
 *
 * @author dan
 */
abstract class AbstractDAO implements DAOInterface {
    
    /**
     *
     * @var PDO
     */
    protected $pdo = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
}
