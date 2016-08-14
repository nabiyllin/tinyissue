<?php
/*
 * This file is part of the site package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Repository;

use Illuminate\Database\Eloquent\Model;

abstract class RepositoryUpdater
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function save(array $options = [])
    {
        return $this->model->save($options);
    }

    public function delete()
    {
        return $this->model->delete();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        // TODO: Implement update() method.
    }

}
