<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FrontPage;
use App\Modules\Auto\Services\Fetch\MakeFetchService;
use App\Modules\Auto\Services\Fetch\ModelFetchService;
use App\Modules\Auto\Services\Fetch\ModelYearFetchService;

class AjaxController extends Controller
{
    /*
     * @var MakeFetchService
     */
    private $makeFetchService;
    
    /*
     * @var ModelFetchService
     */
    private $modelFetchService;
    
    /*
     * @var ModelYearFetchService
     */
    private $modelYearFetchService;
        
    /*
     * AjaxController constructor
     * 
     * @param MakeFetchService $makeFetchService
     * @param ModelFetchService $modelFetchService
     * @param ModelYearFetchService $modelYearFetchService
     */
    public function __construct(
        MakeFetchService $makeFetchService, 
        ModelFetchService $modelFetchService, 
        ModelYearFetchService $modelYearFetchService
    )
    {
        $this->makeFetchService = $makeFetchService;
        $this->modelFetchService = $modelFetchService;
        $this->modelYearFetchService = $modelYearFetchService;
    }

    /*
     * Get list makes by year
     * 
     * @param int $year
     */
    public function getListMakesByYear(int $year)
    {
        return response()->json($this->modelYearFetchService->getListMakesByYear($year));
    }

    /*
     * Get list models by year and make_id
     * 
     * @param int $year
     * @param int $makeId
     */
    public function getListModelsByYearAndMakeId(int $year, int $makeId)
    {
        return response()->json($this->modelYearFetchService->getListModelsByYearAndMakeId($year, $makeId));
    }
}
