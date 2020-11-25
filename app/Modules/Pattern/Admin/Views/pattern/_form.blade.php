<?= FormBuilder::create([
       'method' => $pattern->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $pattern,
       'id'  => 'form-pattern',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($pattern) {
         
		$form->addTab('main', [
			'title' => __('pattern::pattern.title.singular'),
		]);    
    
        
		$form->text('example');

		$form->text('value');

		$form->text('rank');        
    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>    