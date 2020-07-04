@extends('admin.layouts.main')

@section('title', __('product::product.title.index'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('product::product.title.index') }}</h3>
        @if (allow('product.product.store'))
            <a class="btn btn-success btn-xs card-title float-sm-left ml-3" href="{{ r('admin.product.products.create') }}">{{ __('app.add') }}</a>
        @endif
        <a class="btn btn-primary btn-xs card-title float-sm-right js-btn-show-import-availability" href="{{ route('admin.product.products.import-availability') }}">{{ __('product::product.import_availability.title') }}</a>
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="products-grid"></table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#products-grid').aGrid({
        url: '{{ r("admin.product.products.index") }}',
        permissions: {
            update: {{ allow('product.product.update') ? true : false }},
            destroy: {{ allow('product.product.destroy') ? true : false }},
        },
        columns: [
			{
				name: 'id',
				label: "id"
			},
			{
				name: 'title',
				label: "{{ __('product::product.fields.title') }}"
			},
            {
                name: 'price',
                label: "{{ __('product::product.fields.price') }}",
                filter: false,
                sortable: false,
            },
            {
                name: 'availability',
                label: "{{ __('product::product.fields.availability') }}",
            },
			{
				name: 'is_active',
				label: "{{ __('product::product.fields.is_active') }}",
				render: function(value) {
					return aGridExt.renderYesNo(value);
				},
				filter: {type: 'select'}
			},
          ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.product.products.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
