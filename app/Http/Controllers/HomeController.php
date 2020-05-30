<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Parser\ParserService;

class HomeController extends Controller
{

    /*
     * HomeController constructor
     *
     */
    public function __construct(
    )
    {
    }

    /*
     * Home
     */
    public function index()
    {
        //(new ParserService)->run();
    }
}
