<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Repository\User;

use Hash;
use Tinyissue\Contracts\Repository\RepositoryUpdater as UpdaterInterface;
use Tinyissue\Model\Project\User as UserProject;
use Tinyissue\Repository\RepositoryUpdater;
use Tinyissue\Model\User;

/**
 * Class UpdaterRepository
 * @package Tinyissue\Repository\User
 */
class UpdaterRepository extends RepositoryUpdater implements UpdaterInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Add a new user.
     *
     * @param array $info
     *
     * @return bool
     */
    public function create(array $info)
    {
        $insert = [
            'email'     => $info['email'],
            'firstname' => $info['firstname'],
            'lastname'  => $info['lastname'],
            'role_id'   => $info['role_id'],
            'private'   => (boolean)$info['private'],
            'password'  => Hash::make($info['password']),
            'status'    => $info['status'],
            'language'  => app('tinyissue.settings')->getLanguage(),
        ];

        return $this->model->fill($insert)->save();
    }

    /**
     * Soft deletes a user and empties the email.
     *
     * @return bool
     */
    public function delete()
    {
        $this->model->update([
            'email'   => $this->email . '_deleted',
            'deleted' => User::DELETED_USERS,
        ]);
        $this->model->projects()->delete();
//        Project\User::where('user_id', '=', $this->id)->delete();

        return true;
    }

    /**
     * Updates the users settings, validates the fields.
     *
     * @param array $info
     *
     * @return Eloquent\Model
     */
    public function updateSetting(array $info)
    {
        $update = array_intersect_key($info, array_flip([
            'email',
            'firstname',
            'lastname',
            'language',
            'password',
            'private',
            'status',
        ]));

        return $this->update($update);
    }

    /**
     * Update the user.
     *
     * @param array $info
     *
     * @return Eloquent\Model
     */
    public function update(array $info = [])
    {
        if ($info['password']) {
            $info['password'] = Hash::make($info['password']);
        } elseif (empty($info['password'])) {
            unset($info['password']);
        }

        return $this->model->update($info);
    }

    /**
     * Update user messages setting.
     *
     * @param array $input
     */
    public function updateMessagesSettings(array $input)
    {
        $this->model
            ->projects()
            ->whereIn('project_id', array_keys($input))
            ->get()
            ->each(function (UserProject $project) use ($input) {
                $project->message_id = $input[$project->project_id];
                $project->save();
            });
//        return (new Project\User())
//            ->where('user_id', '=', $this->id)
//            ->whereIn('project_id', array_keys($input))
//            ->get()
//            ->each(function (Project\User $project) use ($input) {
//                $project->message_id = $input[$project->project_id];
//                $project->save();
//            });
    }

}
