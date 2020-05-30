<?php

namespace App\Services\Form;

use Closure;

class Builder
{
    /**
     * The Form resolver callback.
     *
     * @var Closure
     */
    protected $resolver;

    /**
     * The Form instance.
     *
     * @var Form
     */
    protected $form;

    /**
     * Create a new form.
     *
     * @param  array    $params
     * @param  Closure  $callback
     *
     * @return self
     */
    public function create(array $params, Closure $callback) : self
    {
        tap($this->createForm($params), function ($form) use ($callback) {
            $this->form = $form;

            $callback($form);
        });

        return $this;
    }

    /**
     * Create a new form set with a Closure.
     *
     * @param  array  $params
     * @param  Closure|null  $callback
     * @return Form
     */
    protected function createForm(array $params, Closure $callback = null) : Form
    {
        return new Form($params, $callback);
    }

    /**
     *
     * @return string
     */
    public function __toString() : string
    {
        return (new Html($this->form))->render();
    }
}
