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

use Illuminate\Http\Request;
use Tinyissue\Form\FilterIssue as FilterForm;
use Tinyissue\Form\Note as NoteForm;
use Tinyissue\Form\Project as Form;
use Tinyissue\Http\Requests\FormRequest;
use Tinyissue\Model\Project;
use Tinyissue\Model\Project\Issue;
use Tinyissue\Model\Project\Note;
use Tinyissue\Services\Exporter;

/**
 * ProjectController is the controller class for managing request related to a project.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class ProjectController extends Controller
{
    /**
     * Display activity for a project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Project $project)
    {
        $activities = $project->activities()
            ->with('activity', 'issue', 'user', 'assignTo', 'comment', 'note')
            ->orderBy('users_activity.created_at', 'DESC')
            ->take(10);

        // Internal project and logged user can see created only
        if ($project->isPrivateInternal() && $this->getLoggedUser()->isUser()) {
            $activities->join('projects_issues', 'projects_issues.id', '=', 'item_id');
            $activities->where('created_by', '=', $this->getLoggedUser()->id);
        }

        return view('project.index', [
            'tabs'       => $this->projectMainViewTabs($project, 'index'),
            'project'    => $project,
            'active'     => 'activity',
            'activities' => $activities->get(),
            'sidebar'    => 'project',
        ]);
    }

    /**
     * Display issues for a project.
     *
     * @param FilterForm $filterForm
     * @param Request    $request
     * @param Project    $project
     * @param int        $status
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIssues(FilterForm $filterForm, Request $request, Project $project, $status = Issue::STATUS_OPEN)
    {
        if ($project->isPrivateInternal() && $this->getLoggedUser()->isUser()) {
            $request['created_by'] = $this->getLoggedUser()->id;
        }
        $active                = $status == Issue::STATUS_OPEN ? 'open_issue' : 'closed_issue';
        $issues                = $project->listIssues($status, $request->all());

        return view('project.index', [
            'tabs'       => $this->projectMainViewTabs($project, 'issues', $issues, $status),
            'project'    => $project,
            'active'     => $active,
            'issues'     => $issues,
            'sidebar'    => 'project',
            'filterForm' => $filterForm,
        ]);
    }

    /**
     * Display issues assigned to current user for a project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAssigned(Project $project)
    {
        $issues = $project->listAssignedOrCreatedIssues($this->getLoggedUser());

        return view('project.index', [
            'tabs'    => $this->projectMainViewTabs($project, 'assigned', $issues),
            'project' => $project,
            'active'  => 'issue_assigned_to_you',
            'issues'  => $issues,
            'sidebar' => 'project',
        ]);
    }

    /**
     * Display issues created to current user for a project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreated(Project $project)
    {
        $issues = $project->listAssignedOrCreatedIssues($this->getLoggedUser());

        return view('project.index', [
            'tabs'    => $this->projectMainViewTabs($project, 'created', $issues),
            'project' => $project,
            'active'  => 'issue_created_by_you',
            'issues'  => $issues,
            'sidebar' => 'project',
        ]);
    }

    /**
     * Display notes for a project.
     *
     * @param Project  $project
     * @param NoteForm $form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getNotes(Project $project, NoteForm $form)
    {
        $notes = $project->notes()->with('createdBy')->get();

        return view('project.index', [
            'tabs'     => $this->projectMainViewTabs($project, 'notes', $notes),
            'project'  => $project,
            'active'   => 'notes',
            'notes'    => $notes,
            'sidebar'  => 'project',
            'noteForm' => $form,
        ]);
    }

    /**
     * @param Project $project
     * @param string  $view
     * @param null    $data
     * @param bool    $status
     *
     * @return array
     */
    protected function projectMainViewTabs(Project $project, $view, $data = null, $status = false)
    {
        $user              = $this->getLoggedUser();
        $isLoggedIn        = !$this->auth->guest();
        $isUser            = $isLoggedIn && $user->isUser();
        $isInternalProject = $project->isPrivateInternal();

        if ($view === 'note') {
            $notesCount = !is_null($data) ? $data->count() : 0;
        } else {
            $notesCount = $project->notes()->count();
        }

        if ($view === 'issues') {
            if ($status == Issue::STATUS_OPEN) {
                $closedIssuesCount = $project->closedIssuesCount($user)->count();
                $openIssuesCount   = !is_null($data) ? $data->count() : 0;
            } else {
                $closedIssuesCount = !is_null($data) ? $data->count() : 0;
                $openIssuesCount   = $project->openIssuesCount($user)->count();
            }
        } else {
            $openIssuesCount   = $project->openIssuesCount($user)->count();
            $closedIssuesCount = $project->closedIssuesCount($user)->count();
        }

        $tabs   = [];
        $tabs[] = [
            'url'  => $project->to(),
            'page' => 'activity',
        ];
        $tabs[] = [
            'url'    => $project->to('issues'),
            'page'   => 'open_issue',
            'prefix' => $openIssuesCount,
        ];
        $tabs[] = [
            'url'    => $project->to('issues') . '/0',
            'page'   => 'closed_issue',
            'prefix' => $closedIssuesCount,
        ];
        if ($isLoggedIn && (!$isInternalProject || (!$isUser && $isInternalProject))) {
            if ($view !== 'assigned') {
                $method              = $isUser ? 'createdIssuesCount' : 'assignedIssuesCount';
                $assignedIssuesCount = $this->getLoggedUser()->$method($project->id);
            } else {
                $assignedIssuesCount = !is_null($data) ? $data->count() : 0;
            }

            $tabs[] = [
                'url'    => $project->to($isUser ? 'created' : 'assigned'),
                'page'   => ($isUser ? 'issue_created_by_you' : 'issue_assigned_to_you'),
                'prefix' => $assignedIssuesCount,
            ];
        }
        $tabs[] = [
            'url'    => $project->to('notes'),
            'page'   => 'notes',
            'prefix' => $notesCount,
        ];

        return $tabs;
    }

    /**
     * Edit the project.
     *
     * @param Project $project
     * @param Form    $form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit(Project $project, Form $form)
    {
        return view('project.edit', [
            'form'    => $form,
            'project' => $project,
            'sidebar' => 'project',
        ]);
    }

    /**
     * To update project details.
     *
     * @param Project             $project
     * @param FormRequest\Project $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Project $project, FormRequest\Project $request)
    {
        // Delete the project
        if ($request->has('delete-project')) {
            $project->delete();

            return redirect('projects')
                ->with('notice', trans('tinyissue.project_has_been_deleted'));
        }

        $project->update($request->all());

        return redirect($project->to())
            ->with('notice', trans('tinyissue.project_has_been_updated'));
    }

    /**
     * Ajax: returns list of users that are not in the project.
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInactiveUsers(Project $project)
    {
        $users = $project->usersNotIn();

        return response()->json($users);
    }

    /**
     * Ajax: add user to the project.
     *
     * @param Project $project
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAssign(Project $project, Request $request)
    {
        $status = $project->assignUser((int) $request->input('user_id'));

        return response()->json(['status' => (bool) $status]);
    }

    /**
     * Ajax: remove user from the project.
     *
     * @param Project $project
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUnassign(Project $project, Request $request)
    {
        $status = $project->unassignUser((int) $request->input('user_id'));

        return response()->json(['status' => (bool) $status]);
    }

    /**
     * To add a new note to the project.
     *
     * @param Project          $project
     * @param Note             $note
     * @param FormRequest\Note $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddNote(Project $project, Note $note, FormRequest\Note $request)
    {
        $note->setRelation('project', $project);
        $note->setRelation('createdBy', $this->getLoggedUser());
        $note->createNote($request->all());

        return redirect($note->to())->with('notice', trans('tinyissue.your_note_added'));
    }

    /**
     * Ajax: To update project note.
     *
     * @param Project $project
     * @param Note    $note
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postEditNote(Project $project, Project\Note $note, Request $request)
    {
        $body = '';
        if ($request->has('body')) {
            $note->setRelation('project', $project);
            $note->updateBody($request->input('body'), $this->getLoggedUser());

            $body = \Html::format($note->body);
        }

        return response()->json(['status' => true, 'text' => $body]);
    }

    /**
     * Ajax: to delete a project note.
     *
     * @param Project $project
     * @param Note    $note
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDeleteNote(Project $project, Project\Note $note)
    {
        $note->deleteNote($this->getLoggedUser());

        return response()->json(['status' => true]);
    }

    /**
     * Ajax: generate the issues export file.
     *
     * @param Project  $project
     * @param Exporter $exporter
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postExportIssues(Project $project, Exporter $exporter, Request $request)
    {
        // Generate export file
        $info = $exporter->exportFile(
            'Project\Issue',
            $request->input('format', Exporter::TYPE_CSV),
            $request->all()
        );

        // Download link
        $link = link_to(
            $project->to('download_export/' . $info['file']),
            trans('tinyissue.download_export'),
            ['class' => 'btn btn-link']
        );

        return response()->json([
            'link'  => $link,
            'title' => $info['title'],
            'file'  => $info['file'],
            'ext'   => $info['ext'],
        ]);
    }

    /**
     * Download and then delete an export file.
     *
     * @param Project $project
     * @param string  $file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getDownloadExport(Project $project, $file)
    {
        // Filter out any characters that are not in pattern
        $file = preg_replace('/[^a-z0-9\_\.]/mi', '', $file);

        // Download export
        return response()->download(storage_path('exports/' . $file), $file)->deleteFileAfterSend(true);
    }
}
