@extends('layouts.wrapper')

@section('nav/settings/class')
active
@stop

@section('headingTitle')
    @lang('tinyissue.my_settings')
@stop

@section('headingSubTitle')
    @lang('tinyissue.my_settings_description')
@stop

@section('headingLink')
    <a href="{!! url('user/settings/messages') !!}" class="list-issues-btn">
        @lang('tinyissue.messages')
    </a>
@stop

@section('content')

  {!! Form::form($form, ['action'=>'user/settings','secure'=>null]) !!}

@stop
