@extends('admin.layouts.main')

@section('title', __('user::role.title.create'))
@section('module', 'user')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('user::role.title.create') }}</h3>
    </div>    
    <div class="card-body">    
        @include('User.admin::role._form', [
            'action' => r('admin.user.roles.store'),
        ])
    </div>    
</div>    
@endsection