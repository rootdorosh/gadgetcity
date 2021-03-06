<?php

namespace App\Modules\Product\Admin\Http\Controllers;

use App\Base\AdminController;
use App\Modules\Product\Models\ProductProviderPrice;
use App\Modules\Product\Models\Provider;
use App\Modules\Product\Models\ProviderItem;
use App\Modules\Product\Services\Crud\ProductCrudService;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Modules\Product\Admin\Http\Requests\Product\{
    IndexFilter,
    PriceReportFilter,
    FormRequest,
    ImportAvailabilityRequest,
    CreateRequest,
    EditRequest,
    DestroyRequest,
    BulkDestroyRequest,
    BulkToggleRequest
};
use Illuminate\Support\Str;
use DB;
use Setting;

/**
 */
class ProductController extends AdminController
{
    /*
     * var ProductCrudService
     */
    protected $crudService;

    /*
     * @param  ProductCrudService     $crudService
     */
    public function __construct(ProductCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * Products list
     *
     * @param IndexFilter $request
     */
    public function index(IndexFilter $modelFilter)
    {
        //dd($modelFilter->getData());
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('product.index', compact('modelFilter'));
    }

    /**
     * Price report
     *
     * @param PriceReportFilter $request
     */
    public function priceReport(PriceReportFilter $modelFilter)
    {
        if ($modelFilter->ajax()) {
            return $modelFilter->getData();
        }

        return $this->view('product.price_report', compact('modelFilter'));
    }

    /**
     * Product create
     *
     * @param CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
        $product = new Product;

        return $this->view('product.create', compact('product'));
    }

    /**
     * Product store
     *
     * @param FormRequest $request
     */
    public function store(FormRequest $request)
    {
        $product = $this->crudService->store($request->validated());

        return redirect(r('admin.product.products.index'))
            ->with('success', __('product::product.success.created'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function importAvailability()
    {
        return $this->view('product.import_availability');
    }

    public function importAvailabilityPost(ImportAvailabilityRequest $request)
    {
        $file = $request->file_import;
        $inputFileType = ucfirst(Str::afterLast($file->getClientOriginalName(), '.'));
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($file->getPathname());
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        Product::query()->update(['availability' => 0]);
        $notFounded = [];
        foreach ($sheetData as $item) {
            $product = Product::where('title', trim($item['A']))->first();
            if (!empty($product)) {
                $product->availability = $item['B'];
                $product->save();
            } else {
                $notFounded[] = $item['A'];
             }
        }

        if (!empty($notFounded)) {
            Session::flash('not_found_products', $this->view('product._error_import_not_found_products', compact('notFounded'))->render());
        }

        return redirect(r('admin.product.products.import-availability'))
            ->with('success', __('product::product.success.imported'));
    }

    /**
     * Product edit
     *
     * @param Product $product
     * @param EditRequest $request
     */
    public function edit(Product $product, EditRequest $request)
    {
        return $this->view('product.update', compact('product'));
    }

    /**
     * Product update
     *
     * @param Product $product
     * @param FormRequest $request
     */
    public function update(Product $product, FormRequest $request)
    {
        $product = $this->crudService->update($product, $request->validated());

        return redirect(r('admin.product.products.index'))
            ->with('success', __('product::product.success.updated'));
    }

    /**
     * Product destroy
     *
     * @param DestroyRequest $request
     * @param Product $product
     */
    public function destroy(Product $product, DestroyRequest $request)
    {
        $this->crudService->destroy($product);

        return response()->json(null, 204);
    }

    /**
     * Products bulk destroy
     *
     * @param BulkDestroyRequest $request
     */
    public function bulkDestroy(BulkDestroyRequest $request)
    {
        $this->crudService->bulkDestroy($request->ids);

        return response()->json(null, 204);
    }

    /**
     * Products bulk toggle attribute
     *
     * @param BulkToggleRequest $request
     */
    public function bulkToggle(BulkToggleRequest $request)
    {
        $this->crudService->bulkToggle($request->validated());

        return response()->json(null, 204);
    }

    /**
     * Products autocomplete
     */
    public function autocomplete()
    {
        $data = [];
        $q = trim(request()->get('q'));

        $query = Product::query();
        foreach (explode(' ', $q) as $text) {
            $query->where('title', 'like', "%$text%");
        }

        foreach ($query->limit(20)->get() as $product) {
            $data[] = [
                'id' => $product->id,
                'title' => $product->title,
            ];
        }

        return response()->json($data);
    }

    public function exportPriceReportXml()
    {
        $providers = Provider::where('is_active', '1')->get()->pluck('pid', 'id')->toArray();

        header('Content-Type: text/xml; charset=utf-8', true);
        $xml = new \DOMDocument("1.0", "UTF-8");

        $itemsNode = $xml->createElement("items");
        $rootNode = $xml->appendChild($itemsNode);
        $rootNode->setAttribute("version","2.0");
        foreach ((new Product)->getDataForExportPriceReport(request()->get('period')) as $product) {
            $item = $xml->createElement('item');
            $rootNode->appendChild($item);

            $productNode = $xml->createElement('product');
            $item->appendChild($productNode);
            $productNode->appendChild($xml->createTextNode($product['title']));

            $stockNode = $xml->createElement('stock');
            $item->appendChild($stockNode);
            $stockNode->appendChild($xml->createTextNode($product['availability']?'Да':'Нет'));

            $qtyNode = $xml->createElement('qty');
            $item->appendChild($qtyNode);
            $qtyNode->appendChild($xml->createTextNode($product['availability']));

            foreach ($providers as $providerId => $providerPid) {
                $lastItem = ProviderItem::where('provider_id', $providerId)->orderBy('price_time', 'DESC')->first();

                $price = null;
                if (!empty($product['provider_' . $providerId])) {
                    $prices = $product['provider_' . $providerId];
                    usort($prices, function ($a, $b) {  return $a['price_time'] <= $b['price_time'] ? 1 : -1; });
                    $price = '$' . $prices[0]['price'];

                    if ($lastItem && Setting::get('report_xml_date')) {
                        $price .= ' ('. date('d.m.Y', $lastItem->price_time) .')';
                    }
                }
                $providerNode = $xml->createElement($providerPid);
                $item->appendChild($providerNode);
                $providerNode->appendChild($xml->createTextNode($price));
            }

        }

        echo $xml->saveXML();
        die();
    }

    public function refreshGoogleTable()
    {
        Artisan::call('google:sheets');

        return back()->with('success', __('product::product.success.updated_google_table'));
    }
}
