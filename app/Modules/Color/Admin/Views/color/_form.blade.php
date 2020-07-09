<?= FormBuilder::create([
       'method' => $color->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $color,
       'id'  => 'form-color',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($color) {
         
		$form->addTab('main', [
			'title' => __('color::color.title.singular'),
		]);    
    
        
		$form->text('title');

		$form->text('code');        
    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>    