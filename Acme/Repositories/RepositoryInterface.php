<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/29/2016
 * Time: 10:53 AM
 */

namespace Acme\Repositories;


interface RepositoryInterface
{
    public function all($columns = array('*'));
/**/
    public function paginate($perPage = 15, $columns = array('*'));

    public function create();

    public function store(array $request);

    public function edit($id);

    public function update(array $request, $id);

    public function delete($id);

    public function find($id, $columns = array('*'));

    public function findBy($field, $value, $columns = array('*'));

}