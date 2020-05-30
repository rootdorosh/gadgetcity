<?php

namespace App\Modules\Dashboard\Admin\Http\Controllers;

use Illuminate\View\View;
use App\Base\AdminController;

class IndexController extends AdminController
{
    /*
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Index
     *
     * @return View
     */
    public function index(): View
    {
        return $this->view('index.index');
    }
}
