@extends('admin.layouts.main')

@section('title', __('color::color.title.index'))
@section('module', 'color')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('color::color.title.index') }}</h3>
        @if (allow('color.color.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right" href="{{ r('admin.color.colors.create') }}">{{ __('app.add') }}</a>
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="colors-grid"></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#colors-grid').aGrid({
        url: '{{ r("admin.color.colors.index") }}',
        permissions: {
            update: {{ allow('color.color.update') ? true : false }},
            destroy: {{ allow('color.color.destroy') ? true : false }},
        },
        columns: [
			{
				name: 'id',
				label: "id"
			},
			{
				name: 'title',
				label: "{{ __('color::color.fields.title') }}"
			},
			{
				name: 'code',
				label: "{{ __('color::color.fields.code') }}"
			},
          ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.color.colors.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
