<?php

namespace App\Services\Form;

use Illuminate\Support\Str;

class Html
{
    const FIELD_ATTRIBUTES = [
        'type',
        'name',
        'id',
        'class',
        'placeholder',
        'value',
        'step',
        'multiple',
        'data-value',
        'row',
        'readonly',
//        'style',
    ];

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @param  Form  $form
     *
     * @return void
     */
    public function __construct(Form  $form)
    {
        $this->form = $form;
    }
    /**
     *
     * @return string
     */
    public function render() : string
    {
        $errors = $this->form->getErrors();
        
        list($method, $subMethod) = $this->form->resolveMethod();

        $id = $this->form->getId();
        
        $form = '';
        $html = '';
        
        if (!$this->form->hideForm) {
            $form = '<form id="' . $id . '" action="' . $this->form->action . '" method="' . $method . '" class="' .
                $this->form->formClass . '"';

            foreach ($this->form->attributes as $k => $v) {
                $form .= ' ' . $k . '="' . $v . '"';
            }

            $form .= ' enctype="multipart/form-data">';

            $html .= csrf_field();
            if ($subMethod !== null) {
                $html .= '<input type="hidden" name="_method" value="' . $subMethod . '">';
            }
        }
        
        if (!empty($this->form->tabs)) {
            $html .= '<div class="card card-tabs">';
            $html .=    '<div class="card-header p-2 border-bottom-0">';
            $html .=        '<ul class="nav nav-tabs" id="custom-tabs" role="tablist">';
            
            $tabIndex = 0;
            foreach ($this->form->tabs as $tabId => $tabParam) {
                $active = !$tabIndex ? 'active' : '';
                $html .= '<li class="nav-item">';
                $html .=    '<a class="nav-link ' . $active . '" id="nav-link-' . $tabId . '" data-toggle="pill" href="#' . 
                            $tabId . '" role="tab" aria-controls="' . $tabId . '" aria-selected="true">' .
                            $tabParam['title'] . '</a>';
                $html .= '</li>';
                $tabIndex++;
            }
            
            $html .=        '<ul>';
            $html .=    '</div>';
            $html .=    '<div class="card-body">';
            $html .=        '<div class="tab-content" id="custom-tabs-tabContent">';
        }
        
        $formBlocks = [];
        
        foreach ($this->form->fields as $field) {
            $itemHtml = '';
            
            if ($field['tag'] === 'input' && isset($field['type']) && $field['type'] === 'hidden') {
                $html .=    $this->{$field['tag'] . ''}($field);
                continue;
            }
            
            $nameDot = Form::nameToDot($field['name']);
            
            $errorClass = !empty($errors[$nameDot]) ? 'has-error' : '';
            $errorMsg   = !empty($errors[$nameDot]) ? implode(', ', $errors[$nameDot]) : '';

            $field['groupClass'] .= Str::snake($field['id']);

            $itemHtml .= '<div class="js-group form-group ' . $field['groupClass'] . ' ' . $errorClass . '">';
            $itemHtml .=    $this->getLabel($field);
            $itemHtml .=    $this->{$field['tag'] . ''}($field);
            
            /*
            if (!empty($field['hint'])) {
                $itemHtml .=    '<span class="helper-block">' . $field['hint'] . '</span>';
            }
            */

            $itemHtml .=    '<span class="help-block">' . $errorMsg . '</span>';
            $itemHtml .= '</div>';
            
            if (!empty($field['tab']) && !empty($this->form->tabs[$field['tab']])) {
                if (!isset($formBlocks[$field['tab']])) {
                    $formBlocks[$field['tab']] = $this->form->tabs[$field['tab']];
                    $formBlocks[$field['tab']]['type'] = 'tab';
                }
                $formBlocks[$field['tab']]['items'][] = $itemHtml;
            } else {
                $formBlocks[$field['name']]['html'] = $itemHtml;
                $formBlocks[$field['name']]['type'] = 'filed';
            }
        }
        
        //render form fileds with tabs
        foreach ($formBlocks as $key => $formBlock) {
            if ($formBlock['type'] === 'filed') {
                $html .= $formBlock['html'];
            } elseif ($formBlock['type'] === 'tab') {
                $htmlFields = '';
                foreach ($formBlock['items'] as $itemField) {
                    $htmlFields .= $itemField;
                }
                $formBlock['name'] = $key;
                $html .= str_replace('{content}', $htmlFields, $this->renderTab($formBlock));
            }
        }

        if (!empty($this->form->tabs)) {
            //foreach ($this->form->tabs as $tabId => $tabParam) {
                $html .= '</div>';
            //}
        }
        
        if (!empty($this->form->buttons)) {
            if (!empty($this->form->tabs)) {
                $html .= '<div class="card-footer">';
            } else {
                $html .= '<div class="' . $this->form->btnGroupClass . '">';
            }

            foreach ($this->form->buttons as $button) {
                $html .= '<input class="btn '. $button['class'] .'" ';
                foreach ($button['options'] as $k => $v) {
                    $html .= sprintf('%s="%s"', $k, $v);
                }
                $html .= 'type="'. $button['type'] .'" value="' . $button['label'] .'"> &nbsp;&nbsp;';
            }

            $html .= '</div>';
        }

        if (!empty($this->form->ajaxifyPanel)) {
            $html .= '<div class="'. ($this->form->ajaxifyPanel['options']['groupClass'] ?? '') .'" >';
            $html .= '<label class="w-100">&nbsp;</label>';

            if ($this->form->model->exists) {
                if (!empty($this->form->ajaxifyPanel['actions']['edit']) &&
                    $this->form->ajaxifyPanel['actions']['edit']['hasAccess']
                ) {
                    $class = isset($this->form->ajaxifyPanel['actions']['edit']['class']) ?
                        $this->form->ajaxifyPanel['actions']['edit']['class'] : '';

                    $icon = isset($this->form->ajaxifyPanel['actions']['edit']['icon']) ?
                        '<i class="fas ' . $this->form->ajaxifyPanel['actions']['edit']['icon'] . '"></i>' : '';

                    $label = isset($this->form->ajaxifyPanel['actions']['edit']['label']) ?
                        $this->form->ajaxifyPanel['actions']['edit']['label'] : '';

                    $html .= ' <button class="btn ' . $class . ' js-ajaxify-btn-edit" type="button">' .
                            $icon . $label . '</button>';
                }
                if (!empty($this->form->ajaxifyPanel['actions']['destroy']) &&
                    $this->form->ajaxifyPanel['actions']['destroy']['hasAccess']
                ) {
                    $class = isset($this->form->ajaxifyPanel['actions']['destroy']['class']) ?
                        $this->form->ajaxifyPanel['actions']['destroy']['class'] : '';

                    $icon = isset($this->form->ajaxifyPanel['actions']['destroy']['icon']) ?
                        '<i class="fas ' . $this->form->ajaxifyPanel['actions']['destroy']['icon'] . '"></i>' : '';

                    $label = isset($this->form->ajaxifyPanel['actions']['destroy']['label']) ?
                        $this->form->ajaxifyPanel['actions']['destroy']['label'] : '';

                    $html .= ' <button class="btn ' . $class . ' js-ajaxify-btn-destroy" type="button" data-url="' .
                        $this->form->ajaxifyPanel['actions']['destroy']['url']
                        . '"> ' . $icon . $label . ' </button>';
                }
            } else {
                $class = isset($this->form->ajaxifyPanel['actions']['store']['class']) ?
                    $this->form->ajaxifyPanel['actions']['store']['class'] : '';

                $icon = isset($this->form->ajaxifyPanel['actions']['store']['icon']) ?
                        '<i class="fas ' . $this->form->ajaxifyPanel['actions']['store']['icon'] . '"></i>' : '';

                $label = isset($this->form->ajaxifyPanel['actions']['store']['label']) ?
                    $this->form->ajaxifyPanel['actions']['store']['label'] : '';

                $html .= ' <button class="btn ' . $class . ' js-ajaxify-btn-store" type="button">' . $icon . $label .
                            '</button>';
            }

            $html .= '</div>';
        }
        
        $form .= $this->addWrapper($html);
        
        if (!$this->form->hideForm) {
            $form .= '</form>';
        }

        return  $form;
    }
    
    /*
     * @param array $data
     * @return string
     */
    private function renderTab(array $data): string
    {
        $class = array_keys($this->form->tabs)[0] === $data['name'] ? 'active show' : '';
        
        $html = '<div class="tab-pane fade ' . $class . '" id="' . $data['name'] . '" '
                    . 'role="tabpanel" aria-labelledby="' . $data['name'] . '">';
        $html .=     '{content}';
        $html .= '</div>';
        
        return $html;
    }

    /**
     *
     * @param array $field
     *
     * @return string
     */
    public function getLabel(array $field) : string
    {
        $class = $this->form->labelConfig[$field['name']]['class'] ?? '';

        return '<label for="' . $field['id'] . '" class="' . $class . '">' . $field['label'] . ' </label>';
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function input(array $attributes) : string
    {
        return '<input '.join(' ', array_map(function ($key) use ($attributes) {
            if (in_array($key, self::FIELD_ATTRIBUTES)) {
                return $key . '="'.$attributes[$key].'"';
            }
        }, array_keys($attributes))) . ' />';
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function textarea(array $attributes) : string
    {
        return '<textarea '.join(' ', array_map(function ($key) use ($attributes) {
            if (in_array($key, self::FIELD_ATTRIBUTES) && $key !== 'value') {
                return $key . '="'.$attributes[$key].'"';
            }
        }, array_keys($attributes))) . '>' . $attributes['value'] . '</textarea>';
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function select(array $attributes) : string
    {
        if (!empty($attributes['multiple'])) {
            $attributes['class'] = isset($attributes['class']) ?
                $attributes['class'] . ' select2' : $attributes['class'];
        }
        
        $html = '<select '.join(' ', array_map(function ($key) use ($attributes) {
            if (in_array($key, self::FIELD_ATTRIBUTES) && $key !== 'value') {
                return $key . '="'.$attributes[$key].'"';
            }
        }, array_keys($attributes))) . '>';

        if (!isset($attributes['empty']) || $attributes['empty'] !== false) {
            $empty = !empty($attributes['empty']) ? $attributes['empty'] : '-';
            $html .= '<option value="">' . $empty . '</option>';
        }
        
        foreach ($attributes['options'] as $k => $v) {
            $selected = (!is_array($attributes['value']) && $attributes['value'] === $k) ||
            (is_array($attributes['value']) && in_array($k, $attributes['value']))
                ? 'selected="selected"' : '';

            $html .= '<option value="' . $k . '" '. $selected .'>' . $v . '</option>';
        }

        $html .= '</select>';
        
        if (!empty($attributes['append'])) {
            $colField =  $attributes['colFieldClass'] ?? 'col-sm-6';
            $colAppend = $attributes['colAppendClass'] ?? 'col-sm-6';
            
            $html = '<div class="row"><div class="' .  $colField . '">' . $html . '</div>  ' .
                '<div class="' . $colAppend . '">' . $attributes['append'] . '</div></div>';
        }

        return $html;
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function image(array $attributes) : string
    {
        $html = '';

        $html .= '<input type="file"'.join(' ', array_map(function ($key) use ($attributes) {
            if (in_array($key, self::FIELD_ATTRIBUTES) && $key !== 'value') {
                return $key . '="'.$attributes[$key].'"';
            }
        }, array_keys($attributes))) . '>';
        
        if (!empty($attributes['img'])) {
            $html .= sprintf('<br/><img src="%s" height="80">', $attributes['img']);
        }
        
        return $html;
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function rangeFromToInput(array $attributes) : string
    {
        $itemsLabel = array_key_exists('itemsLabel', $attributes) ? $attributes['itemsLabel'] : true;
        $colSm = $itemsLabel ? 6 : 5;
        
        $html = '<div class="row col-sm-12 pad-mar-none">';
        $html .= '<div class="col-sm-'.$colSm.'">';
        if ($itemsLabel) {
            $html .= '<label>РѕС‚ &nbsp;';
        }
        $attrs = ['type' => 'number', 'name' => $attributes['name'] . '_from', 'class' => 'form-control'];
        $attrs['step'] = !empty($attributes['step']) ? $attributes['step'] : 1;
        
        $html .= $this->input($attrs);
        if ($itemsLabel) {
            $html .= '</label>';
        }
        $html .= '</div>';
        
        if (!$itemsLabel) {
            $html .= '<div class="col-sm-2">&nbsp;&nbsp;-</div>';
        }
        
        $html .= '<div class="col-sm-'.$colSm.'">';
        if ($itemsLabel) {
            $html .= '<label>РґРѕ &nbsp;';
        }
        $attrs = ['type' => 'number', 'name' => $attributes['name'] . '_to', 'class' => 'form-control'];
        $attrs['step'] = !empty($attributes['step']) ? $attributes['step'] : 1;
        $html .= $this->input($attrs);
        if ($itemsLabel) {
            $html .= '</label>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function toggle(array $attributes) : string
    {
        $html = '<div class="btn-group from_builder_buttons js-toggle">&nbsp;&nbsp;';
        $html.= '<input type="hidden" data-reset="' . $attributes['value'] . '" id="'
            . $attributes['id'] . '" name="' . $attributes['name']
            . '" value="' . $attributes['value'] . '"/>';

        foreach ($attributes['options'] as $k => $v) {
            $activeClass = $attributes['valueClass'][$k] ?? 'btn-info';
            
            $v = str_replace(' ', '&nbsp;', $v);
            $class = $attributes['value'] === $k ? 'btn-info' : 'btn-default';
            $html.= '<button type="button" data-active-class="' . $activeClass . '" class="js-toggle-btn btn btn-sm  ' .
                $class . '" data-value="' . $k . '">'  . $v . '</button>';
        }

        $html.= '</div>';

        return $html;
    }

    /**
     *
     * @param array $attributes
     *
     * @return string
     */
    public function doubleToggle(array $attributes) : string
    {
        $html = '<div class="btn-group from_builder_buttons js-double-toggle">';
        $html.= '<input type="hidden" data-reset="' . $attributes['value'] . '" id="'
            . $attributes['id'] . '" name="' . $attributes['name']
            . '" value="' . $attributes['value'] . '"/>';

        foreach ($attributes['options'] as $k => $v) {
            $activeClass = $attributes['valueClass'][$k] ?? 'btn-info';
            
            $v = str_replace(' ', '&nbsp;', $v);
            $class = $attributes['value'] === $k ? 'btn-info' : 'btn-default';
            $html.= '<button type="button" data-active-class="' . $activeClass . '" class="js-double-toggle-btn btn  ' .
                $class . '" data-value="' . $k . '">'  . $v . '</button>';
        }

        $html.= '</div>';

        return $html;
    }

    /**
     *
     * @param string $html
     *
     * @return string
     */
    public function addWrapper(string $html) : string
    {
        $htmlWithWtappers = '';

        if (!empty($this->form->wrappers)) {
            foreach ($this->form->wrappers as $wrapper) {
                $htmlWithWtappers .= '<div '.join(' ', array_map(function ($key) use ($wrapper) {
                        return $key . '="'.$wrapper[$key].'"';
                }, array_keys($wrapper))) . '>';
            }

            $htmlWithWtappers .= $html;

            foreach ($this->form->wrappers as $wrapper) {
                $htmlWithWtappers .= '</div>';
            }
        } else {
            return $html;
        }

        return $htmlWithWtappers;
    }
}
