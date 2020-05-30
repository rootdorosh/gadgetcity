@extends('admin.layouts.main')

@section('title', __('user::role.title.index'))
@section('module', 'user')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('user::role.title.index') }}</h3>
        @if (allow('user.role.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right" href="{{ r("admin.user.roles.create") }}">{{ __('app.add') }}</a>      
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="roles-grid"></table>
    </div>    
</div>    
@endsection

@push('scripts')
<script>    
$(function () {

    var tableAgrid = $('#roles-grid').aGrid({
        url: '{{ r("admin.user.roles.index") }}',
        permissions: {
            update: {{ allow('user.role.update') ? true : false }},
            destroy: {{ allow('user.role.destroy') ? true : false }},
        },
        columns: [
            {
                name: 'id', 
                label: '<?= __('user::role.fields.id') ?>'
            },
            {
                name: 'name', 
                label: '<?= __('user::role.fields.name') ?>'
            },
            {
                name: 'slug', 
                label: '<?= __('user::role.fields.slug') ?>'
            },
            {
                name: 'permissions', 
                label: '<?= __('user::role.fields.permissions') ?>', 
                sortable: false,
                filter: false
            },
        ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.user.roles.index") }}'
        }),    
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }                    
    });
});
</script>
@endpush