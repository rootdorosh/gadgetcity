@extends('admin.layouts.main')

@section('title', __('user::user.title.create'))
@section('module', 'user')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('user::user.title.create') }}</h3>
    </div>    
    <div class="card-body">    
        @include('User.admin::user._form', [
            'action' => r('admin.user.users.store'),
        ])
    </div>    
</div>    
@endsection