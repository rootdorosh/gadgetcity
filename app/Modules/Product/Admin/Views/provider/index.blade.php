@extends('admin.layouts.main')

@section('title', __('product::provider.title.index'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('product::provider.title.index') }}</h3>
        @if (allow('product.provider.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right" href="{{ r('admin.product.providers.create') }}">{{ __('app.add') }}</a>
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="providers-grid"></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#providers-grid').aGrid({
        url: '{{ r("admin.product.providers.index") }}',
        permissions: {
            update: {{ allow('product.provider.update') ? true : false }},
            destroy: {{ allow('product.provider.destroy') ? true : false }},
        },
        columns: [
			{
				name: 'id',
				label: "id"
			},
			{
				name: 'pid',
				label: "{{ __('product::provider.fields.pid') }}"
			},
			{
				name: 'title',
				label: "{{ __('product::provider.fields.title') }}"
			},
			{
				name: 'is_active',
				label: "{{ __('product::provider.fields.is_active') }}",
				render: function(row) {
                 	return aGridExt.renderYesNo(row, 'is_active');
				},
				filter: {type: 'select'}
			},
          ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.product.providers.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
