@extends(getKeyTemplateForErrorPage())

@section('title', __($exception->getMessage() ?: 'Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden'))
