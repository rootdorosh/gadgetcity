@extends('admin.layouts.main')

@section('title', __('user::user.title.index'))
@section('module', 'user')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('user::user.title.index') }}</h3>
        @if (allow('user.user.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right" href="{{ r("admin.user.users.create") }}">{{ __('app.add') }}</a>      
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="users-grid"></table>
    </div>    
</div>    
@endsection

@push('scripts')
<script>    
$(function () {

    var tableAgrid = $('#users-grid').aGrid({
        url: '{{ r("admin.user.users.index") }}',
        permissions: {
            update: {{ allow('user.user.update') ? true : false }},
            destroy: {{ allow('user.user.destroy') ? true : false }},
        },
        selectable: true,
        columns: [
            {
                name: 'id', 
                label: '<?= __('user::user.fields.id') ?>'
            },
            {
                name: 'email', 
                label: '<?= __('user::user.fields.email') ?>'
            },
            {
                name: 'name', 
                label: '<?= __('user::user.fields.name') ?>'
            },
            {
                name: 'is_active', 
                label: '<?= __('user::user.fields.is_active') ?>',
                render: function(value) {
                    return aGridExt.renderYesNo(value);
                },
                filter: {type: 'select'}
            },
            {
                name: 'roles', 
                label: '<?= __('user::user.fields.roles') ?>', 
                sortable: false,
                filter: false
            },
        ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.user.users.index") }}'
        }),    
        bulkActions: aGridExt.defaultBulkActions({
            baseUrl: '{{ r("admin.user.users.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }                    
    });
});
</script>
@endpush