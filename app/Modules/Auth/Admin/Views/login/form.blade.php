@extends('admin.layouts.quest')

@section('title',  __('auth::login_form.title'))

@section('content')
<form action="{{ r('admin.auth.login.submit') }}" method="post">
    
    @csrf
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="input-group mb-3">
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ __('auth::login_form.fields.email') }}">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="{{ __('auth::login_form.fields.password') }}">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">{{ __('auth::login_form.submit_btn') }}</button>
        </div>
    </div>
</form>
@stop
