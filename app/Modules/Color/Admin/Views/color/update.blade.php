@extends('admin.layouts.main')

@section('title', __('color::color.title.update'))
@section('module', 'color')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('color::color.title.update') }}</h3>
    </div>    
    <div class="card-body">    
        @include('Color.admin::color._form', [
            'action' => r('admin.color.colors.update', [$color->id]),
        ])
    </div>    
</div>    
@endsection