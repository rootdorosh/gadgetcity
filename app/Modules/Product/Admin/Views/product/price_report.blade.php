<?php
$labels = __('product::product.fields');
?>

@extends('admin.layouts.main')

@section('title', __('product::product.price_report'))
@section('module', 'product')

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="float-sm-left text-uppercase">Фильтр</h5>
    </div>

    <div class="card-body">
        <form id="form-price-report-filter">
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="js-group form-group form-group">
                            <label for="title" class="">{{ $labels['title'] }}</label>
                            <input type="text" name="title" id="title" value="" class="form-control">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="js-group form-group form-group">
                            <label for="title" class="">{{ $labels['is_availability'] }}</label>
                            <select id="is_availability" name="is_availability" class="form-control">
                                <option value="">-</option>
                                <option value="1">да</option>
                                <option value="0">нет</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="js-group form-group form-group">
                            <label for="title" class="">{{ $labels['is_show_provider_item_title'] }}</label>
                            <select id="is_show_provider_item_title" name="is_show_provider_item_title" class="form-control">
                                <option value="1">да</option>
                                <option value="0" selected>нет</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="js-group form-group form-group">
                            <label class="">{{ $labels['date_range'] }}</label>
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            <input type="hidden" name="date_from" id="date_from">
                            <input type="hidden" name="date_to" id="date_to">
                        </div>
                    </div>
                    <div class="col-xl-1">
                        <div class="js-group form-group form-group">
                            <label class="" style="color: #fff;">Отправить</label>
                            <input type="submit" value="Отправить" class="btn-primary btn">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>


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

    var start = moment().subtract(6, 'days');
    var end = moment();

    function cb(from, to) {
        $('#reportrange span').html(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
        $('#date_from').val(from.format('YYYY-MM-DD'));
        $('#date_to').val(to.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);


    var tableAgrid = $('#products-grid').aGrid({
        filterForm: '#form-price-report-filter',
        url: '{{ r("admin.product.products.price-report") }}?' + $('#realty-object-filter').serialize(),
        permissions: {
            update: false,
            destroy: false,
        },
        columns: [
			{
				name: 'title',
				label: "{{ __('product::product.fields.title') }}",
                filter: false,
        	},
            {
                name: 'is_availability',
                label: "{{ __('product::product.fields.is_availability') }}",
                render: function(row) {
                    return aGridExt.renderYesNo(row, 'is_availability');
                },
                filter: false,
                sortable: false,
            },
            @foreach(\App\Modules\Product\Models\Provider::where('is_active', 1)->get() as $provider)
            {
                name: 'provider_{{ $provider->id }}',
                label: "{{ $provider->title }}",
                filter: false,
                sortable: false,
                /*
                render: function(row) {
                    let items = row['provider_{{ $provider->id }}'];
                    let html = '';

                    if (row.is_show_provider_item_title) {
                        $.each(items, function (k, row) {
                            html += `<span><b>\$${row.price}</b></span><br/>`;
                            html += `<span>${row.title}</span><br/>`;
                            html += `<span>${row.date}</span><br/>`;
                            html += '<br/>';
                        })
                    } else if (items.length) {
                        let sum = 0;
                        $.each(items, function (k, row) {
                            sum += row.price;
                        })
                        let avg = Math.ceil(sum / items.length);
                        html = `<span><b>\$${avg}</b></span><br/>`;
                    }

                    return html;
                }
                */
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
