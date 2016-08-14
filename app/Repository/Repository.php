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

abstract class Repository
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    protected $updater;
    protected $updaterClass;

    public function updater()
    {
        if (is_null($this->updater)) {
            $this->updater = new $this->updaterClass($this->getModel());
        }
        return $this->updater;
    }

    public function load($id)
    {

//        $project->load()->updater()->delete();
//        $project->load()->updater()->update([]);
//        $project->load()->query()->getSomethign();
//        $project->load()->query()->all();
        return $this->setModel($this->getById($id));
    }

    public function hasModel()
    {
        return $this->getModel() && $this->getModel()->id > 0;
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function requireById($id)
    {
        $model = $this->getById($id);

        if (!$model) {
            throw new \DomainException('Un');
        }

        return $model;
    }

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
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        // TODO: Implement paginate() method.
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        // TODO: Implement find() method.
    }
}
