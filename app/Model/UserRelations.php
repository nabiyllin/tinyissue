<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Relations\User;

use Illuminate\Database\Eloquent\Relations as EloquentRelations;

/**
 * RelationTrait is trait class containing the relationship methods for the User model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property static $this
 */
trait UserRelations
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
     * @return Relations\HasMany
     */
    public function projects()
    {
        return $this
            ->belongsToMany('Tinyissue\Model\Project', 'projects_users')
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

    abstract public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null);
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);
    abstract public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);
}
