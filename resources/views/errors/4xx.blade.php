@extends(getKeyTemplateForErrorPage())

@section('title', $exception->getStatusCode() . ' ' .$exception->getMessage())
@section('code', $exception->getStatusCode())
@section('message', $exception->getMessage())
