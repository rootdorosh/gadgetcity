@extends('admin.layouts.main')

@section('title', __('product::provider_log.title.index'))
@section('module', 'product')

@section('content')
    <div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('product::provider_log.title.index') }}</h3>
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="provider-logs-grid"></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#provider-logs-grid').aGrid({
        url: '{{ r("admin.product.provider-logs.index") }}',
        permissions: {
            destroy: {{ allow('product.providerlog.destroy') ? true : false }},
        },
        selectable: true,
        columns: [
			{
				name: 'provider_id',
				label: "{{ __('product::provider_log.fields.provider_id') }}",
                filter: {
                    type: 'select',
                    options: {!!  json_encode(App\Modules\Product\Models\Provider::orderBy('pid')->get()->pluck('title', 'id')->toArray()) !!},
                },
                render: function (row) {
				    return row.provider_id;
                },
            },
			{
				name: 'content',
				label: "{{ __('product::provider_log.fields.content') }}"
			},
			{
				name: 'message_time',
				label: "{{ __('product::provider_log.fields.message_time') }}",
                filter: false,
			},
			{
				name: 'create_time',
				label: "{{ __('product::provider_log.fields.create_time') }}",
                filter: false,
         	},
        ],
        sort: {
            attr: 'id',
            dir: 'desc'
        },
        rowActions: aGridExt.defaultRowActions({
            update: false,
            baseUrl: '{{ r("admin.product.provider-logs.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
