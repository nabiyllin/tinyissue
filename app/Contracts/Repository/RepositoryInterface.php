<?php
/*
 * This file is part of the site package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Contracts\Repository;

interface RepositoryInterface {
    public function load($id);

    /**
     * @return Model
     */
    public function getModel();

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model);

    public function updater();


    public function getById($id);

    public function requireById($id);

//    public function getAll()
//    {
//        return $this->model->all();
//    }
//
//    public function getPaginated($count)
//    {
//        return $this->model->paginate($count);
//    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all();

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'));

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'));
}

