<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Traits\Project\Issue;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations;
use Tinyissue\Model;

/**
 * RelationTrait is trait class containing the relationship methods for the Project\Issue model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property static $this
 */
trait RelationTrait
{
    /**
     * An issue has one user assigned to (inverse relationship of User::issues).
     *
     * @return Relations\BelongsTo
     */
    public function assigned()
    {
        return $this->belongsTo('Tinyissue\Model\User', 'assigned_to');
    }

    /**
     * An issue has one user updated by (inverse relationship of User::issuesUpdatedBy).
     *
     * @return Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('Tinyissue\Model\User', 'updated_by');
    }

    /**
     * An issue has one user closed it (inverse relationship of User::issuesClosedBy).
     *
     * @return Relations\BelongsTo
     */
    public function closer()
    {
        return $this->belongsTo('Tinyissue\Model\User', 'closed_by');
    }

    /**
     * An issue has one user created it (inverse relationship of User::issuesCreatedBy).
     *
     * @return Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Tinyissue\Model\User', 'created_by');
    }

    /**
     * Issue belong to a project.
     *
     * @return Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Tinyissue\Model\Project');
    }

    /**
     * Issue can have many attachments.
     *
     * @return Relations\HasMany
     */
    public function attachments()
    {
        return $this
            ->hasMany('Tinyissue\Model\Project\Issue\Attachment', 'issue_id')
            ->where(function (Builder $query) {
                $query->where('comment_id', '=', 0);
                $query->orWhere('comment_id', '=', null);
            });
    }

    /**
     * Issue have many users activities.
     *
     * @return Relations\HasMany
     */
    public function activities()
    {
        return $this
            ->hasMany('Tinyissue\Model\User\Activity', 'item_id')
            ->orderBy('created_at', 'ASC');
    }

    /**
     * Issue have many users activities (all except comments).
     *
     * @return mixed
     */
    public function generalActivities()
    {
        return $this
            ->hasMany('Tinyissue\Model\User\Activity', 'item_id')
            ->whereNotIn('type_id', [Model\Activity::TYPE_COMMENT])
            ->orderBy('created_at', 'ASC');
    }

    /**
     * Issue have many users activities (comments).
     *
     * @return mixed
     */
    public function commentActivities()
    {
        return $this
            ->hasMany('Tinyissue\Model\User\Activity', 'item_id')
            ->whereIn('type_id', [Model\Activity::TYPE_COMMENT])
            ->orderBy('created_at', 'ASC');
    }

    /**
     * Issue have many tags.
     *
     * @return Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany('Tinyissue\Model\Tag', 'projects_issues_tags', 'issue_id', 'tag_id')
            ->join('tags as p', 'p.id', '=', 'tags.parent_id')
            ->orderBy('parent_id');
    }

    /**
     * Issue have many comments.
     *
     * @return Relations\HasMany
     */
    public function comments()
    {
        return $this
            ->hasMany('Tinyissue\Model\Project\Issue\Comment', 'issue_id')
            ->orderBy('created_at', 'ASC');
    }

    /**
     * Issue can have many messages queue.
     *
     * @return Relations\HasMany
     */
    public function messagesQueue()
    {
        return $this->morphMany('Tinyissue\Model\Message\Queue', 'model');
    }

    abstract public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);
    abstract public function hasOne($related, $foreignKey = null, $localKey = null);
    abstract public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null);
}
