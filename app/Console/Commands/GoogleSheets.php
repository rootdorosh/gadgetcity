<?php

namespace App\Console\Commands;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Provider;
use Illuminate\Console\Command;

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
            $headers[] = $providerPid;
        }

        $values = [$headers];
        $valueRange->setValues($values);

        $range = $sheetName . '!A1:A';
        $conf = ["valueInputOption" => "USER_ENTERED"];
        $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf);
        sleep(1);

        foreach ((new Product)->getDataForExportPriceReport(request()->get('period')) as $product) {
            $row = [
                $product['title'],
                $product['availability']?'Да':'Нет',
                (string)$product['availability']
            ];

            foreach ($providers as $providerId => $providerPid) {
                $price = null;
                if (!empty($product['provider_' . $providerId])) {
                    $prices = $product['provider_' . $providerId];
                    usort($prices, function ($a, $b) {  return $a['price_time'] <= $b['price_time'] ? 1 : -1; });
                    $price = '$' . $prices[0]['price'];
                }
                $row[] = '-';
            }
            print_r($row);
            $valueRange->setValues($row);

            $range = $sheetName . '!A1:A';
            $conf = ["valueInputOption" => "USER_ENTERED"];
            $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf);
            sleep(1);
        }

    }
}
