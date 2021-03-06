<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Http\Controllers;

use Tinyissue\Form\Settings as FormSettings;
use Tinyissue\Http\Requests\FormRequest;
use Tinyissue\Model\Project;
use Tinyissue\Model\Tag;
use Tinyissue\Model\User;
use Tinyissue\Services\SettingsManager;

/**
 * AdministrationController is the controller class for managing request related the application system admin.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class AdministrationController extends Controller
{
    /**
     * Show general application stats.
     *
     * @param Tag     $tag
     * @param Project $project
     * @param User    $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Tag $tag, Project $project, User $user)
    {
        return view('administration.index', [
            'users'             => $user->countUsers(),
            'active_projects'   => $project->countOpenProjects(),
            'archived_projects' => $project->countArchivedProjects(),
            'open_issues'       => $project->countOpenIssues(),
            'closed_issues'     => $project->countClosedIssues(),
            'projects'          => $this->getLoggedUser()->projects()->get(),
            'tags'              => $tag->count(),
        ]);
    }

    /**
     * Add new tag page.
     *
     * @param FormSettings $form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSettings(FormSettings $form)
    {
        return view('administration.settings', [
            'form'     => $form,
            'projects' => $this->getLoggedUser()->projects()->get(),
        ]);
    }

    /**
     * To create new tag.
     *
     * @param FormRequest\Settings $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSettings(FormRequest\Settings $request)
    {
        app('tinyissue.settings')->save($request->except('_token'));

        return redirect('administration/settings')->with('notice', trans('tinyissue.settings_saved'));
    }

    /**
     * Toggle site maintenance mode.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getChangeMaintenanceMode()
    {
        $action = 'down';
        if (app()->isDownForMaintenance()) {
            $action = 'up';
            app('session')->remove('notice-error');
        }

        \Artisan::call($action);

        return redirect('administration');
    }
}
