<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Model\Traits\Project\Note;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Relations;
use Tinyissue\Model;

/**
 * CrudTrait is trait class containing the methods for adding/editing/deleting the Project\Note model.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property int                  $project_id
 * @property int                  $created_by
 * @property string               $body
 * @property Model\Project        $project
 * @property Model\User           $createdBy
 *
 * @method   Relations\BelongsTo  belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
 * @method   Eloquent\Model       save()
 * @method   Relations\HasOne     activity()
 */
trait CrudTrait
{
    /**
     * Create a new note.
     *
     * @param array $input
     *
     * @return $this
     */
    public function createNote(array $input)
    {
        $this->body       = $input['note_body'];
        $this->project_id = $this->project->id;
        $this->created_by = $this->createdBy->id;

        // Add event on successful save
        static::saved(function (Model\Project\Note $note) {
            $this->queueAdd($note, $note->createdBy);
        });

        $this->save();

        // Add to user's activity log
        $this->activity()->save(new Model\User\Activity([
            'type_id'   => Model\Activity::TYPE_NOTE,
            'parent_id' => $this->project->id,
            'user_id'   => $this->createdBy->id,
        ]));

        return $this;
    }

    /**
     * Update the note body.
     *
     * @param string     $body
     * @param Model\User $user
     *
     * @return Eloquent\Model
     */
    public function updateBody($body, Model\User $user)
    {
        $this->body = $body;

        // Add event on successful save
        static::saved(function (Model\Project\Note $note) use ($user) {
            $this->queueUpdate($note, $user);
        });

        return $this->save();
    }

    /**
     * Delete a note.
     *
     * @param Model\User $user
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteNote(Model\User $user)
    {
        $this->activity()->delete();

        // Add event on successful delete
        static::deleted(function (Model\Project\Note $note) use ($user) {
            $this->queueDelete($note, $user);
        });

        return parent::delete();
    }
}
