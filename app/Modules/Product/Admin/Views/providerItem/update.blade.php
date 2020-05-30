@extends('admin.layouts.main')

@section('title', __('product::provider_item.title.update'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('product::provider_item.title.update') }}</h3>
    </div>    
    <div class="card-body">    
        @include('Product.admin::providerItem._form', [
            'action' => r('admin.product.provider-items.update', [$providerItem->id]),
        ])
    </div>    
</div>    
@endsection