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

use Illuminate\Database\Eloquent\Relations;

/**
 * RelationTrait is trait class containing the relationship methods for the Project model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property static $this
 */
trait ProjectRelations
{
    /**
     * Returns all issues related to project.
     *
     * @return Relations\HasMany
     */
    public function issues()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'project_id');
    }

    /**
     * Returns all issues related to project.
     *
     * @return Relations\HasMany
     */
    public function _openIssues()
    {
        return $this->issues()->where('status', '=', Project\Issue::STATUS_OPEN);
    }

    /**
     * Returns issues in the project with user details eager loaded.
     *
     * @return Relations\HasMany
     */
    public function issuesByUser()
    {
        return $this->hasMany('Tinyissue\Model\Project\Issue', 'project_id')->with('user')->get();
    }

    /**
     * Returns all users assigned in the current project.
     *
     * @return Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('\Tinyissue\Model\User', 'projects_users', 'project_id', 'user_id');
    }

    /**
     * Return a user that is member of a project.
     *
     * @param int $userId
     *
     * @return Relations\BelongsToMany
     */
    public function user($userId)
    {
        return $this->users()->where('user_id', '=', (int) $userId);
    }

    /**
     * Project has many project users.
     *
     * @return Relations\HasMany
     */
    public function projectUsers()
    {
        return $this->hasMany('Tinyissue\Model\Project\User', 'project_id');
    }

    /**
     * Returns project activities.
     *
     * @return Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany('Tinyissue\Model\User\Activity', 'parent_id');
    }

    /**
     * Returns notes in the project.
     *
     * @return Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany('Tinyissue\Model\Project\Note', 'project_id');
    }

    /**
     * Project have many kanban tags.
     *
     * @return Relations\BelongsToMany
     */
    public function kanbanTags()
    {
        return $this->belongsToMany('Tinyissue\Model\Tag', 'projects_kanban_tags', 'project_id', 'tag_id')
            ->orderBy('position');
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRoleLimitForUser($query, User $user)
    {
        return $query->where(function (Eloquent\Builder $query) use ($user) {
            $query->where('role_limit', '<=', $user->role_id);
            $query->orWhere('role_limit', '=', null);
        });
    }

    abstract public function hasMany($related, $foreignKey = null, $localKey = null);
    abstract public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);
}
