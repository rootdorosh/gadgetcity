<?= FormBuilder::create([
       'method' => $product->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $product,
       'id'  => 'form-product',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($product) {

    $form->input('file', 'file_import');
    $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>
