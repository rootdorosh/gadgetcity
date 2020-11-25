@extends('admin.layouts.main')

@section('title', __('pattern::pattern.title.create'))
@section('module', 'pattern')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('pattern::pattern.title.create') }}</h3>
    </div>    
    <div class="card-body">    
        @include('Pattern.admin::pattern._form', [
            'action' => r('admin.pattern.patterns.store'),
        ])
    </div>    
</div>    
@endsection