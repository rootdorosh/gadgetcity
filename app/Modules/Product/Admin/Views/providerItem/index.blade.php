@extends('admin.layouts.main')

@section('title', __('product::provider_item.title.index'))
@section('module', 'product')

@section('content')
    <div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('product::provider_item.title.index') }}</h3>
        @if (allow('product.provideritem.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right" href="{{ r('admin.product.provider-items.create') }}">{{ __('app.add') }}</a>
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="provider-items-grid"></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#provider-items-grid').aGrid({
        url: '{{ r("admin.product.provider-items.index") }}',
        permissions: {
            update: {{ allow('product.provideritem.update') ? true : false }},
            destroy: {{ allow('product.provideritem.destroy') ? true : false }},
        },
        selectable: true,
        columns: [
			{
				name: 'provider_id',
				label: "{{ __('product::provider_item.fields.provider_id') }}",
                filter: {
                    type: 'select',
                    options: {!!  json_encode(App\Modules\Product\Models\Provider::orderBy('pid')->get()->pluck('title', 'id')->toArray()) !!},
                },
                render: function (row) {
				    return row.provider_title;
                },
            },
			{
				name: 'title',
				label: "{{ __('product::provider_item.fields.title') }}"
			},
			{
				name: 'price',
				label: "{{ __('product::provider_item.fields.price') }}"
			},
			{
				name: 'price_time',
				label: "{{ __('product::provider_item.fields.date') }}",
                filter: false,
			},
			{
				name: 'product_title',
				label: "{{ __('product::provider_item.fields.product_id') }}",
                filter: false,
                sortable: false,
			},
			{
				name: 'status',
				label: "{{ __('product::provider_item.fields.status') }}",
                filter: {
				    type: 'select',
                    options: {!!  json_encode(App\Modules\Product\Models\ProviderItem::STATUSES) !!},
                },
			},
        ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        bulkActions: [
            {
                permission: 'update',
                type: 'button',
                class: 'btn-danger btn-sm',
                label: '{{ __('product::provider_item.status.cancel') }}',
                action: 'bulkToggle',
                attribute: 'status',
                value: {{ \App\Modules\Product\Models\ProviderItem::STATUS_CANCEL  }},
                url: '/admin/product/provider-items/bulk-toggle',
            },
            {
                permission: 'update',
                type: 'button',
                class: 'btn-secondary btn-sm',
                label: '{{ __('product::provider_item.status.await') }}',
                action: 'bulkToggle',
                attribute: 'status',
                value: {{ \App\Modules\Product\Models\ProviderItem::STATUS_AWAIT  }},
                url: '/admin/product/provider-items/bulk-toggle',
            },
            /*
            {
                permission: 'destroy',
                type: 'button',
                class: 'btn-danger btn-sm',
                confirm: 'Вы действительно хотите удалить?',
                label: 'Remove',
                action: 'bulkDestroy',
                url: '/admin/product/provider-items/bulk-destroy'
            }
             */

        ],
        rowActions: aGridExt.defaultRowActions({
            update: false,
            baseUrl: '{{ r("admin.product.provider-items.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });

    var activeSelectionSetProduct;

    $('body').on('click', '.js-link-provider-item-set-product', function (e)  {
        e.preventDefault();
        var self = $(this);
        activeSelectionSetProduct = self;
        var input = self.next();
        input.removeClass('hidden');
        self.addClass('hidden');

        input.autocomplete({
            minLength: 2,
            source: function (request, response) {

                $.ajax({
                    data: {q: input.val()},
                    dataType: "json",
                    type: 'GET',
                    url: "{{ route('admin.product.products.autocomplete')  }}",
                    headers: {
                        'X-CSRF-TOKEN': window._token
                    },
                    success: function (data) {
                        response($.map(data, function (obj) {
                            return obj;
                        }));
                    }
                });
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                .append('<a href="#" class="js-set-product" data-id="'+self.data('id')+'" data-product_id="'+item.id+'">' + item.title + '</a>')
                .appendTo(ul);
        };
    })

    $('body').on('click', '.js-set-product', function (e) {
        e.preventDefault();
        var self = $(this);
        var data = {
            id: self.data('id'),
            product_id: self.data('product_id'),
        }

        $.ajax({
            data,
            dataType: "json",
            type: 'PUT',
            url: "{{ route('admin.product.provider-items.set-product') }}",
            headers: {
                'X-CSRF-TOKEN': window._token
            },
            success: function (data) {
                var tr = activeSelectionSetProduct.closest('tr');
                var tpl = `<p>${data.product_title} <a href="#" data-id="${data.price_id}" class="badge badge-danger js-remove-price">x</a></p>`;

                $('.js-product_title', tr).prepend(tpl);
                $('.js-status span', tr).html(data.status_title).attr('class', 'badge badge-success');
            }
        });
    })

    $('body').on('click', '.js-remove-price', function (e) {
        e.preventDefault();
        if (!confirm('Удалить')) {
            return;
        }

        var self = $(this);

        $.ajax({
            dataType: "json",
            type: 'DELETE',
            url: "/admin/product/provider-items/remove-price/" + self.data('id'),
            headers: {
                'X-CSRF-TOKEN': window._token
            },
            success: function (data) {
                var tr = self.closest('tr');
                self.closest('p').remove();
                $('.js-status span', tr).html(data.status_title).attr('class', 'badge badge-' + data.status_style);
            }
        });
    })
});
</script>
@endpush
