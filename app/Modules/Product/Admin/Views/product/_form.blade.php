<?= FormBuilder::create([
       'method' => $product->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $product,
       'id'  => 'form-product',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($product) {
         
		$form->addTab('main', [
			'title' => __('product::product.title.singular'),
		]);    
    
        
		$form->text('title');

		$form->toggle('is_active');        
    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>    