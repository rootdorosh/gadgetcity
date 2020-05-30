<?= FormBuilder::create([
       'method' => $role->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $role,
       'id'  => 'form-role',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($role) {
        
        $form->addTab('main', [
            'title' => __('user::role.title.singular'),
        ]);

        $form->text('name');
        
        $form->text('slug');
        
        $form->select('permissions', \App\Modules\User\Services\Fetch\PermissionFetchService::getList(), [
            'empty' => false,
            'multiple' => 'multiple',
        ]);

        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
});?>
    