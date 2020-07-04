@extends('admin.layouts.main')

@section('title', __('product::product.price_report'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('product::product.price_report') }}</h3>
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
        url: '{{ r("admin.product.products.price-report") }}',
        permissions: {
            update: false,
            destroy: false,
        },
        columns: [
			{
				name: 'title',
				label: "{{ __('product::product.fields.title') }}"
        	},

            {
                name: 'is_availability',
                label: "{{ __('product::product.fields.is_availability') }}",
                render: function(row) {
                    return aGridExt.renderYesNo(row, 'is_availability');
                },
                filter: {type: 'select'},
                sortable: false,
            },
            @foreach(\App\Modules\Product\Models\Provider::where('is_active', 1)->get() as $provider)
            {
                name: 'provider_{{ $provider->id }}',
                label: "{{ $provider->title }}",
                filter: false,
                sortable: false,
                render: function(row) {
                    let items = row['provider_{{ $provider->id }}'];
                    let html = '';
                    $.each(items, function(k, row) {
                        html+= `<span><b>\$${row.price}</b></span><br/>`;
                        html+= `<span>${row.title}</span><br/>`;
                        html+= `<span>${row.date}</span><br/>`;
                        html+= '<br/>';
                    })
                    html += '';
                    return html;
                }
            },
            @endforeach
          ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
