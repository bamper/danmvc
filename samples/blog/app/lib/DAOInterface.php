<?php

/**
 *
 * @author dan
 */
interface DAOInterface {
    public function findById($id);
    public function create($value);
    public function update($value);
    public function remove($value);
}
