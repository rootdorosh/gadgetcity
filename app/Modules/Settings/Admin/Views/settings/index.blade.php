@extends('admin.layouts.main')

@section('title', __('settings::settings.title.update'))
@section('module', 'settings')

@section('content')
    <div class="card card-info card-outline">
        <div class="card-header p-2 border-bottom-0">
            <h3 class="card-title float-sm-left">{{ __('settings::settings.title.update') }}</h3>
        </div>
        <div class="card-body">
            <?= FormBuilder::create([
                'method' => 'POST',
                'action' => route('admin.settings.update') . '/',
                'model'  => $settings,
                'id'  => 'form-settings',
                'groupClass' => 'form-group col-sm-4',
            ], function (App\Services\Form\Form $form) {

                $form->toggle('report_xml_date');

                $form->toggle('report_google_date');

                $form->button('submit', 'btn-success btn-sm', __('app.submit'));
            });?>
        </div>
    </div>

@endsection
