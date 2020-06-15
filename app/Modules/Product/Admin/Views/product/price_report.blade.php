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
            @foreach(\App\Modules\Product\Models\Provider::where('is_active', 1)->get() as $provider)
            {
                name: 'provider_{{ $provider->id }}',
                label: "{{ $provider->title }}",
                filter: false,
                sortable: false,
                render: function(row) {
                    let items = row['provider_{{ $provider->id }}'];
                    let html = '<table>';
                    $.each(items, function(k, row) {
                        html+= '<tr>';
                        html+= `<td>${row.title}</td>`;
                        html+= `<td>${row.date}</td>`;
                        html+= `<td>${row.price}</td>`;
                        html+= '</tr>';
                    })
                    html += '</table>';
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
