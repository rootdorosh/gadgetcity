@extends('admin.layouts.main')

@section('title', __('product::product.title.update'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('product::product.title.update') }}</h3>
    </div>    
    <div class="card-body">    
        @include('Product.admin::product._form', [
            'action' => r('admin.product.products.update', [$product->id]),
        ])
    </div>    
</div>    
@endsection