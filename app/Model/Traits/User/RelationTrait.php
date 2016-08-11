<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Traits\User;

use Illuminate\Database\Eloquent\Relations;
use Tinyissue\Model\Project;

/**
 * RelationTrait is trait class containing the relationship methods for the User model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property static $this
 */
trait RelationTrait
{
    /**
     * A user has one role (inverse relationship of Role::users).
     *
     * @return Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('Tinyissue\Model\Role', 'role_id');
    }

    /**
     * User has many comments (One-many relationship of Comment::user).
     *
     * @return Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue\Comment', 'created_by', 'id');
    }

    /**
     * Returns issues created by the user.
     *
     * @return Relations\HasMany
     */
    public function issuesCreatedBy()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'created_by');
    }

    /**
     * Returns issues closed by the user.
     *
     * @return Relations\HasMany
     */
    public function issuesClosedBy()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'closed_by');
    }

    /**
     * Returns issues updated by the user.
     *
     * @return Relations\HasMany
     */
    public function issuesUpdatedBy()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'updated_by');
    }

    /**
     * User has many attachments (One-many relationship of Attachment::user).
     *
     * @return Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue\Attachment', 'uploaded_by');
    }

    /**
     * Returns all projects the user can access.
     *
     * @param int $status
     *
     * @return Relations\HasMany
     */
    public function projects($status = Project::STATUS_OPEN)
    {
        return $this
            ->belongsToMany('Tinyissue\Model\Project', 'projects_users')
            ->where('status', '=', $status)
            ->orderBy('name');
    }

    /**
     * User has many issues assigned to (One-many relationship of Issue::assigned).
     *
     * @return Relations\HasMany
     */
    public function issues()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'assigned_to');
    }

    /**
     * Returns all permission for the user.
     *
     * @return Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany('\Tinyissue\Model\Role\Permission', 'role_id', 'role_id');
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    abstract public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null);

    /**
     * Define a one-to-many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * Define a many-to-many relationship.
     *
     * @param string $related
     * @param string $table
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);
}
