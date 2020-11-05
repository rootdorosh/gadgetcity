<?php

namespace App\Console\Commands;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Provider;
use App\Modules\Product\Models\ProviderItem;
use Illuminate\Console\Command;
use Setting;

class GoogleSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:sheets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Путь к файлу ключа сервисного аккаунта
        $googleAccountKeyFilePath = storage_path() . '/credentials.json';
        putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath );

        // Документация https://developers.google.com/sheets/api/
        $client = new \Google_Client();
        $client->useApplicationDefaultCredentials();

        // Области, к которым будет доступ
        // https://developers.google.com/identity/protocols/googlescopes
        $client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );

        $service = new \Google_Service_Sheets($client);

        // ID таблицы
        //$spreadsheetId = '1KdTaK81c3BURTW4D7QMGgkEexd_ATYx3pUiUNbsmNlY';
        $spreadsheetId = '1RWBYUQnH5IQd5MJzKoWhDM7J-JEnva4qzA7JV4TLw_c';
        $sheetName = 'Sheet1';

        //clear all data
        $range = $sheetName;  // TODO: Update placeholder value.
        $requestBody = new \Google_Service_Sheets_ClearValuesRequest();
        $service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);

        // insert data
        $valueRange = new \Google_Service_Sheets_ValueRange();

        $providers = Provider::where('is_active', '1')->get()->pluck('pid', 'id')->toArray();
        $headers = ['Product', 'Stock', 'qty'];
        foreach ($providers as $providerId => $providerPid) {
            $lastItem = ProviderItem::where('provider_id', $providerId)->orderBy('price_time', 'DESC')->first();
            $providerTitle = $providerPid;
            if ($lastItem && Setting::get('report_google_date')) {
                $providerTitle .= ' ('. date('d.m.y', $lastItem->price_time) .')';
            }

            $headers[] = $providerTitle;
        }

        $values = [$headers];

        foreach ((new Product)->getDataForExportPriceReport('2week') as $product) {
            $row = [
                $product['title'],
                $product['availability']?'Да':'Нет',
                $product['availability']
            ];

            foreach ($providers as $providerId => $providerPid) {
                $price = null;
                if (!empty($product['provider_' . $providerId])) {
                    $prices = $product['provider_' . $providerId];
                    usort($prices, function ($a, $b) {  return $a['price_time'] <= $b['price_time'] ? 1 : -1; });
                    $price = '$' . $prices[0]['price'];
                }
                $row[] = $price;
            }

            $row = array_map(function($value) {
                return empty($value) ? '' : $value;
            }, $row);

            $values[] = $row;
        }

        $body = new \Google_Service_Sheets_ValueRange( [ 'values' => $values] );

        // valueInputOption - определяет способ интерпретации входных данных
        // https://developers.google.com/sheets/api/reference/rest/v4/ValueInputOption
        // RAW | USER_ENTERED
        $options = array( 'valueInputOption' => 'RAW' );
        $service->spreadsheets_values->update( $spreadsheetId, $sheetName . '!A1', $body, $options );

    }
}
