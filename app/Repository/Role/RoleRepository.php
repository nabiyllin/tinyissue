<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Repository\Role;

use Tinyissue\Contracts\Repository\RoleRepository as RoleContract;
use Tinyissue\Model\Role as RoleModel;
use Tinyissue\Repository\Repository;

class RoleRepository extends Repository implements RoleContract
{
    protected $updaterClass = UpdaterRepository::class;

    /**
     * Drop down of all roles.
     *
     * @return Eloquent\Collection
     */
    public static function getDropdown()
    {
        return RoleModel::lists('name', 'id');
    }

    /**
     * Returns all roles with users of each role.
     *
     * @return Eloquent\Collection
     */
    public function getRolesWithUsers()
    {
        return $this->model->with('users')->orderBy('id', 'DESC')->get();
    }
}
