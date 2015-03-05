<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tinyissue\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Role is model class for roles
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;

    /**
     * Drop down of all roles
     *
     * @return array
     */
    public static function dropdown()
    {
        return static::lists('name', 'id');
    }

    /**
     * Role has many users (One-many relationship of User::role).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('Tinyissue\Model\User', 'role_id', 'id')->where('deleted', '=', User::NOT_DELETED_USERS)->orderBy('firstname', 'asc');
    }

    /**
     * Role has many users in a project_users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectUsers()
    {
        return $this->hasMany('Tinyissue\Model\Project\User');
    }

    /**
     * Role has many role permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('\Tinyissue\Model\Permission', 'roles_permissions', 'role_id', 'permission_id', 'role_id');
    }
}