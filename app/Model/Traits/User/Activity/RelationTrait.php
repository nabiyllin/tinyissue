<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Traits\User\Activity;

use Illuminate\Database\Eloquent;
use Tinyissue\Model\Project;

/**
 * RelationTrait is trait class containing the relationship method for the User\Activity model
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method Eloquent\Model hasMany($related, $foreignKey = null, $localKey = null)
 * @method Eloquent\Model belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
 * @method Eloquent\Model belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
 * @method Eloquent\Model hasOne($related, $foreignKey = null, $localKey = null)
 */
trait RelationTrait
{
    /**
     * Returns the project issue this activity is belongs to by the item_id, which can hold the issue id
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function issue()
    {
        return $this->belongsTo('Tinyissue\Model\Project\Issue', 'item_id');
    }

    /**
     * Returns the user this activity is belongs to
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\Tinyissue\Model\User', 'user_id');
    }

    /**
     * Returns the user that was assigned to the issue. Only for reassign activity
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function assignTo()
    {
        return $this->belongsTo('\Tinyissue\Model\User', 'action_id');
    }

    /**
     * User activity has one activity type
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function activity()
    {
        return $this->belongsTo('Tinyissue\Model\Activity', 'type_id');
    }

    /**
     * Returns the comment this activity belongs to if any
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo('Tinyissue\Model\Project\Issue\Comment', 'action_id');
    }

    /**
     * Returns the project his activity belongs to
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Tinyissue\Model\Project', 'parent_id');
    }

    /**
     * Returns the note this activity belongs to if any
     *
     * @return Eloquent\Relations\BelongsTo
     */
    public function note()
    {
        return $this->belongsTo('\Tinyissue\Model\Project\Note', 'action_id');
    }
}
