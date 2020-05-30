<?php 
$fields = [];
foreach ($model['fields'] as $key => $item) {
    $field['type'] = !empty($item['field']) 
        ? $item['field']['type'] 
        : 'text';
    
    if ($field['type'] === 'text') {
        $fields[] = "\t\t" . '$form->text(\''.$key.'\');';
    } elseif ($field['type'] === 'datetime') {
        $fields[] = "\t\t" . '$form->datetime(\''.$key.'\');';
    } elseif ($field['type'] === 'image') {
        $fields[] = "\t\t" . '$form->image(\''.$key.'\');';
    } elseif ($field['type'] === 'number') {
        $fields[] = "\t\t" . '$form->number(\''.$key.'\');';
    } elseif ($field['type'] === 'toggle') {
        $fields[] = "\t\t" . '$form->toggle(\''.$key.'\');';
    } elseif ($field['type'] === 'select') {
         $fields[] = "\t\t" . '$form->select(\''.$key.'\', '.$field['options'].');';
    }   
}   

$tab = "\t\t\$form->addTab('main', [";
$tab .= "\n\t\t\t'title' => __('".Str::snake($moduleData['name'])."::".Str::snake($model['name']).".title.singular'),";
$tab .= "\n\t\t]);";

$tabsLocale = '';
$fieldsLocale = '';
if (!empty($model['translatable'])) {
    $tabsLocale .= "\n\t\t".'foreach (config(\'translatable.locales\') as $locale) {'; 
    $tabsLocale .= "\n\t\t\t\$form->addTab('locale-' . \$locale, [";
    $tabsLocale .= "\n\t\t\t\t'title' => \$locale,";
    $tabsLocale .= "\n\t\t\t]);";
    $tabsLocale .= "\n\t\t".'}'; 
    
    $fieldsLocale .= "\n\t\t".'foreach (config(\'translatable.locales\') as $locale) {'; 
        foreach ($model['translatable']['fields'] as $key => $localeField) {
            $fieldsLocale .= "\n\t\t\t" . '$form->text($locale . "['.$key.']", [';
            $fieldsLocale .= "\n\t\t\t\t'tab' => 'locale-' . \$locale,";
            $fieldsLocale .= "\n\t\t\t]);";
        } 
    $fieldsLocale .= "\n\t\t".'}'; 
    
}
?>

<?= "<?= FormBuilder::create([\n"?>
       'method' => $<?= Str::camel($model['name'])?>->exists ? 'PUT' : 'POST',
       'action' => $action,
       'model'  => $<?= Str::camel($model['name'])?>,
       'id'  => 'form-<?= Str::kebab($model['name'])?>',
       'groupClass' => 'form-group col-sm-4',
       'tab' => 'main',
    ], function (App\Services\Form\Form $form) use ($<?= Str::camel($model['name'])?>) {
         
<?= $tab?>    
<?= $tabsLocale?>    
        
<?= implode("\n\n", $fields)?>        
<?= $fieldsLocale?>    
    
        $form->button('submit', 'btn-success btn-sm', __('app.submit'));
<?= '});?>'?>
    