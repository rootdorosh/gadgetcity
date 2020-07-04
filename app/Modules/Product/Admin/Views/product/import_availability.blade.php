<?php
$product = new \App\Modules\Product\Models\Product;
?>

@extends('admin.layouts.main')

@section('title', __('product::product.import_availability.title'))
@section('module', 'product')

@section('content')
<div class="card card-info card-outline">
    <div class="card-header p-2 border-bottom-0">
        <h3 class="card-title float-sm-left">{{ __('product::product.import_availability.title') }}</h3>
    </div>
    <div class="card-body">

        @include('Product.admin::product._form_import', [
            'action' => r('admin.product.products.import-availability.post'),
        ])

    </div>
</div>
@endsection
