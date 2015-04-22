@extends('layouts.wrapper')

@section('nav/projects/class')
active
@stop

@section('headingTitle')
    @lang('tinyissue.projects')
@stop

@section('headingSubTitle')
    @lang('tinyissue.projects_description')
@stop

@section('content')

{!! Html::tab([
    [
        'url' => URL::to('projects'),
        'page' => 'active',
        'prefix' => $active_count,
    ],
    [
        'url' => URL::to('projects') . '/0',
        'page' => 'archived',
        'prefix' => $archived_count
    ],
], $active) !!}

<div class="inside-tabs">
{!! Html::startBox() !!}

<ul class="projects">
    @foreach($content_projects as $project)
    <li>
        <a href="{{ $project->to() }}">{{ $project->name }}</a><br />

        {{ $project->openIssuesCount }} @lang('tinyissue.open_issue' . ($project->openIssuesCount <= 1? '' : 's'))
    </li>
    @endforeach

    @permission('project-create')
    @if(count($content_projects) == 0)
    <li>@lang('tinyissue.you_do_not_have_any_projects') <a href="{{ URL::to('projects/new') }}">@lang('tinyissue.create_project')</a></li>
    @endif
    @endpermission
</ul>
{!! Html::endBox() !!}
</div>

@stop
