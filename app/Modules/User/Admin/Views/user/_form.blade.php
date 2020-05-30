<?= FormBuilder::create([
       'method' => 'POST',
       'action' => $action,
       'model'  => $user,
       'id'  => 'form-user',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($user) {
        
        $form->addTab('main', [
            'title' => __('user::user.title.singular'),
        ]);

        $form->text('name', [
            'groupClass' => 'col-sm-4',
        ]);
        
        $form->text('email');
        
        $form->password('password');
        
        $form->toggle('is_active');
        
        $form->select('roles', \App\Modules\User\Services\Fetch\RoleFetchService::getList(), [
            'empty' => false,
            'multiple' => 'multiple',
        ]);

        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>
    