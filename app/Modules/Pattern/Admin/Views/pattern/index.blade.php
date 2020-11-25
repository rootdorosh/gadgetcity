@extends('admin.layouts.main')

@section('title', __('pattern::pattern.title.index'))
@section('module', 'pattern')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title float-sm-left">{{ __('pattern::pattern.title.index') }}</h3>
        @if (allow('pattern.pattern.store'))
        <a class="btn btn-success btn-xs card-title float-sm-right ml-3" href="{{ r('admin.pattern.patterns.create') }}">{{ __('app.add') }}</a>
        <a class="btn btn-primary btn-xs card-title float-sm-right" href="{{ r('admin.pattern.patterns.apply') }}">Перепарсить</a>
        @endif
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover agrid" id="patterns-grid"></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    var tableAgrid = $('#patterns-grid').aGrid({
        url: '{{ r("admin.pattern.patterns.index") }}',
        permissions: {
            update: {{ allow('pattern.pattern.update') ? true : false }},
            destroy: {{ allow('pattern.pattern.destroy') ? true : false }},
        },
        columns: [
			{
				name: 'example',
				label: "{{ __('pattern::pattern.fields.example') }}"
			},
			{
				name: 'value',
				label: "{{ __('pattern::pattern.fields.value') }}"
			},
			{
				name: 'rank',
				label: "{{ __('pattern::pattern.fields.rank') }}"
			},
			{
				name: 'result',
				label: "{{ __('pattern::pattern.fields.result') }}",
                sortable: false,
                filter: false,
			},
          ],
        sort: {
            attr: 'id',
            dir: 'asc'
        },
        rowActions: aGridExt.defaultRowActions({
            baseUrl: '{{ r("admin.pattern.patterns.index") }}'
        }),
        theadPanelCols: {
            pager: 'col-sm-4',
            actions: 'col-sm-8'
        }
    });
});
</script>
@endpush
