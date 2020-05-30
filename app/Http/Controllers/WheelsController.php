<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Auto\Services\Fetch\ModelFetchService;
use App\Modules\Auto\Services\Fetch\ModelYearFetchService;
use App\Modules\Auto\Services\Fetch\MakeFetchService;
use App\Modules\Auto\Services\View\ModelViewService;
use FrontPage;

class WheelsController extends Controller
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
     * @var ModelViewService
     */
    private $modelViewService;
    
    /*
     * HomeController constructor
     * 
     * @param MakeFetchService $makeFetchService
     * @param ModelFetchService $modelFetchService
     * @param ModelYearFetchService $modelYearFetchService
     * @param ModelViewService $modelViewService
     */
    public function __construct(
        MakeFetchService $makeFetchService, 
        ModelFetchService $modelFetchService, 
        ModelYearFetchService $modelYearFetchService, 
        ModelViewService $modelViewService
    )
    {
        $this->makeFetchService = $makeFetchService;
        $this->modelFetchService = $modelFetchService;
        $this->modelYearFetchService = $modelYearFetchService;
        $this->modelViewService = $modelViewService;
    }

    /*
     * Home
     */
    public function index()
    {
        $seo = FrontPage::getSeo('wheels');
        
        return view('front.wheels.index');
    }
    
    /*
     * Make
     */
    public function make(string $makeAlias)
    {
        $pageAlias = 'wheels.make';
        
        $make = $this->makeFetchService->getItemBySlug($makeAlias);
        if ($make === null) {
            abort(404);
        }
        
        $models = $this->modelFetchService->getItemsByMakeIdWheels($make['id']);
        $startYear = $this->modelYearFetchService->getItemStartByMakeId($make['id']);
        $modelMostWheels = $this->modelFetchService->getItemWithMostWheelsByMakeId($make['id']);
        $banners = FrontPage::getBanners($pageAlias);

        $seo = FrontPage::getSeo($pageAlias, [
            'make' => $make['title'],
            'count' => count($models),
            'start_year' => $startYear['year'],
            'start_model_year' => $startYear['model_title'],
            'count_most_rims' => $modelMostWheels['count_wheels'],
            'model_most_rims' => $modelMostWheels['model_title'],
        ]);
        
        FrontPage::setBreadcrumbs($seo['breadc_title'], $seo['breadc_label']);
        
        return view('front.wheels.make', compact(
            'make',
            'models',
            'seo',
            'banners'
        ));
    }

    /*
     * Model
     */
    public function model(string $makeAlias, string $modelAlias)
    {
        $make = $this->makeFetchService->bySlug($makeAlias);
        if ($make === null) {
            abort(404);
        }
        
        $model = $this->modelFetchService->getItemByMakeIdAndSlug($make['id'], $modelAlias);
        if ($model === null) {
            abort(404);
        }
        
        $seo = FrontPage::getSeo('wheels.make.model', [
            'make' => $make['title'],
            'model' => $model['title'],
        ]);
        
        FrontPage::setBreadcrumbs(
            FrontPage::getSeoParamByPage('wheels.make', 'breadc_title', ['make' => $make['title']]),
            FrontPage::getSeoParamByPage('wheels.make', 'breadc_label', ['make' => $make['title']])
        );



        $this->modelViewService->add($model['id']);
        
        return view('front.wheels.model');
    }
}
