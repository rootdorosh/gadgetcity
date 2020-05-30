<?php

namespace App\Base;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Services\Response\FractalManager;

class AdminController extends Controller
{
    /**
     * Render view from module
     *
     * @param string $view
     * @param array $data
     * @return View
     */
    public function view(string $view, array $data = []): View
    {
        preg_match('/App\\\\Modules\\\\(.*?)\\\\Admin\\\\Http/', get_class($this), $match);
        if (empty($match[1])) {
            dd("Controller is our of module");
        }
        
        return view($match[1] . '.admin::' . $view, $data);
    }

}
