<?= FormBuilder::create([
       'method' => $provider->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $provider,
       'id'  => 'form-provider',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($provider) {
         
		$form->addTab('main', [
			'title' => __('product::provider.title.singular'),
		]);    
    
        
		$form->text('pid');

		$form->text('title');

		$form->toggle('is_active');

		$form->text('last_guid');        
    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>    