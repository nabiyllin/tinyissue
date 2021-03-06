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

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tinyissue\Contracts\Model\AccessControl;
use URL;

/**
 * Project is model class for projects.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $default_assignee
 * @property int $private
 * @property int $openIssuesCount
 * @property int $closedIssuesCount
 * @property int $closedIssuesCount
 * @property Collection $issues
 * @property Collection $issuesByUser
 * @property Collection $users
 * @property Collection $projectUsers
 * @property Collection $activities
 * @property Collection $notes
 * @property Collection $kanbanTags
 */
class Project extends Model implements AccessControl
{
    use Traits\CountAttributeTrait,
        Traits\Project\CountTrait,
        Traits\Project\FilterTrait,
        Traits\Project\SortTrait,
        Traits\Project\RelationTrait,
        Traits\Project\CrudTrait,
        Traits\Project\QueryTrait;

    /**
     * Project private & user role can see their own issues only.
     *
     * @var int
     */
    const INTERNAL_YES = 2;

    /**
     * Project not public to view and create issue.
     *
     * @var int
     */
    const PRIVATE_YES = 1;

    /**
     * Project public to view and create issue.
     *
     * @var int
     */
    const PRIVATE_NO = 0;

    /**
     * All projects.
     *
     * @var int
     */
    const PRIVATE_ALL = -1;

    /**
     * Project status Open.
     *
     * @var int
     */
    const STATUS_OPEN = 1;

    /**
     * Project status Archived.
     *
     * @var int
     */
    const STATUS_ARCHIVED = 0;

    /**
     * Timestamp enabled.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Name of database table.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * List of allowed columns to be used in $this->fill().
     *
     * @var array
     */
    protected $fillable = ['name', 'default_assignee', 'status', 'private'];

    /**
     * List of HTML classes for each status.
     *
     * @var array
     */
    protected $attrClassNames = [
        self::PRIVATE_NO   => 'note',
        self::PRIVATE_YES  => 'info',
        self::INTERNAL_YES => 'primary',
    ];

    /**
     * List of statuses names.
     *
     * @var array
     */
    protected $statusesNames = [
        self::PRIVATE_NO   => 'public',
        self::PRIVATE_YES  => 'private',
        self::INTERNAL_YES => 'internal',
    ];

    /**
     * Generate a URL for the active project.
     *
     * @param string $url
     *
     * @return string
     */
    public function to($url = '')
    {
        return URL::to('project/' . $this->id . (($url) ? '/' . $url : ''));
    }

    /**
     * Returns the aggregate value of number of open issues in the project.
     *
     * @return int
     */
    public function getOpenIssuesCountAttribute()
    {
        return $this->getCountAttribute('openIssuesCount');
    }

    /**
     * Returns the aggregate value of number of closed issues in the project.
     *
     * @return int
     */
    public function getClosedIssuesCountAttribute()
    {
        return $this->getCountAttribute('closedIssuesCount');
    }

    /**
     * Set default assignee attribute.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setDefaultAssigneeAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['default_assignee'] = (int) $value;
        }

        return $this;
    }

    /**
     * Returns the aggregate value of number of issues in the project.
     *
     * @return int
     */
    public function getIssuesCountAttribute()
    {
        return $this->getCountAttribute('issuesCount');
    }

    /**
     * Get total issues total quote time.
     *
     * @return int
     */
    public function getTotalQuote()
    {
        $total = 0;
        foreach ($this->issues as $issue) {
            $total += $issue->time_quote;
        }

        return $total;
    }

    /**
     * Calculate the progress (open & closed issues).
     *
     * @return float|int
     */
    public function getProgress()
    {
        $total    = $this->openIssuesCount + $this->closedIssuesCount;
        $progress = 100;
        if ($total > 0) {
            $progress = (float) ($this->closedIssuesCount / $total) * 100;
        }
        $progressInt = (int) $progress;
        if ($progressInt > 0) {
            $progress = number_format($progress, 2);
            $fraction = $progress - $progressInt;
            if ($fraction === 0.0) {
                $progress = $progressInt;
            }
        }

        return $progress;
    }

    /**
     * Whether or not a user is member of the project.
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isMember($userId)
    {
        return $this->user($userId)->count() > 0;
    }

    /**
     * Whether or not the project is private.
     *
     * @return bool
     */
    public function isPrivate()
    {
        return (int) $this->private === self::PRIVATE_YES;
    }

    /**
     * Whether or not the project is public.
     *
     * @return bool
     */
    public function isPublic()
    {
        return (int) $this->private === self::PRIVATE_NO;
    }

    /**
     * Whether or not the project is private internal.
     *
     * @return bool
     */
    public function isPrivateInternal()
    {
        return (int) $this->private === self::INTERNAL_YES;
    }

    /**
     * Returns project status as string name.
     *
     * @return string
     */
    public function getStatusAsName()
    {
        if (array_key_exists((int) $this->private, $this->statusesNames)) {
            return $this->statusesNames[(int) $this->private];
        }

        return '';
    }

    /**
     * Returns the class name to be used for project status.
     *
     * @return string
     */
    public function getStatusClass()
    {
        if (array_key_exists((int) $this->private, $this->attrClassNames)) {
            return $this->attrClassNames[(int) $this->private];
        }

        return '';
    }

    /**
     * Whether or not a user can access the project.
     *
     * @param User $user
     *
     * @return bool
     */
    public function canView(User $user)
    {
        // Is member of the project
        if (
            ($this->isPublic() && app('tinyissue.settings')->isPublicProjectsEnabled()) ||
            $this->isMember($user->id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Whether a user can edit the project.
     *
     * @param User $user
     *
     * @return bool
     */
    public function canEdit(User $user)
    {
        return $user->permission(Permission::PERM_PROJECT_MODIFY) || $user->permission(Permission::PERM_PROJECT_ALL);
    }

    /**
     * @param string $permission
     * @param User   $user
     *
     * @return bool
     */
    public function can($permission, User $user)
    {
        $editPermissions = [
            Permission::PERM_PROJECT_CREATE,
            Permission::PERM_PROJECT_MODIFY,
        ];

        if (in_array($permission, $editPermissions)) {
            return $this->canEdit($user);
        }

        return $this->canView($user);
    }
}
