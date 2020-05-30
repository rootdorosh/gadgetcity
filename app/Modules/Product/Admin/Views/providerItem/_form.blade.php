<?= FormBuilder::create([
       'method' => $providerItem->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $providerItem,
       'id'  => 'form-provider-item',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($providerItem) {
         
		$form->addTab('main', [
			'title' => __('product::provider_item.title.singular'),
		]);    
    
        
		$form->text('provider_id');

		$form->text('title');

		$form->text('product_id');

		$form->text('status');

		$form->text('price');        
    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>    