@extends('layouts.wrapper')

@section('scripts')
    {!! Html::script(elixir('js/tiny_project.js')) !!}
@stop

@section('nav/projects/class')
    active
@stop

@section('headingTitle')
    {!! link_to($project->to(), $project->name) !!}
@stop

@section('headingSubTitle')
    @lang('tinyissue.project_overview')
@stop

@section('headingLink')
    {!! link_to($project->to('issue/new'), trans('tinyissue.new_issue')) !!}
@stop

@section('content')

{!! Html::tab([
    [
        'url' => $project->to(),
        'page' => 'activity'
    ],
    [
        'url' => $project->to('issues'),
        'page' => 'open_issue',
        'prefix' => $open_issues_count,
    ],
    [
        'url' => $project->to('issues') . '/0',
        'page' => 'closed_issue',
        'prefix' => $closed_issues_count,
    ],
    [
        'url' => $project->to('assigned'),
        'page' => 'issue_assigned_to_you',
        'prefix' => $assigned_issues_count,
    ],
    [
        'url' => $project->to('notes'),
        'page' => 'notes',
        'prefix' => $notes_count,
    ],
], $active) !!}

    <div class="inside-tabs {{ $active }}">

        @if (isset($filterForm))
            {!! Html::startBox('blue-box gray-box toolbar') !!}
            {!! Form::form($filterForm, ['action'=>'', 'method'=>'GET']) !!}
            {!! Html::endBox() !!}
        @endif

        @if(isset($notes))
            @include('project/index/notes')
        @else
            {!! Html::startBox() !!}
            @if (isset($issues))
                @include('project/index/issues')
            @else
                @include('project/index/activity')
            @endif
            {!! Html::endBox() !!}
        @endif

    </div>

@stop
