@extends('layouts.app')

@section('content')
<div class="container-full">
    <chat-app :auth="{{$user}}"></chat-app>
</div>
@endsection
