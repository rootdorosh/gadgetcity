<?php
declare( strict_types = 1 );

namespace App\Services\Parser;

use Illuminate\Support\Str;
use App\Services\Curl;
use App\Modules\Product\Models\{
    Product,
    Provider,
    ProviderItem,
    ProductProviderPrice
};
use App\Services\Parser\Processors\IProcessor;
use DB;

/**
 * Class ParserService
 * @package App\Services\Parser
 */
class ParserService
{
    /**
     * @return void
     */
    public function run() : void
    {
        DB::statement('DELETE FROM product_providers_items');

        $providers = [
            'ByryndychokApple',
            'MrFixUa',
            'restarttradein',
            'ilovephoneopt',
            'applezt',
            'iPeople_UA',
            'iDesireKH',
            'optomiphone',
            'appteka',
            'wearefriendly',
            'imonolit',
            'iCentr_UA',
        ];
        $providerIds = Provider::where('pid', $providers)->pluck('id')->toArray();
        DB::statement('UPDATE product_providers SET is_active = 0');
        foreach ($providers as $pid) {
            $provider = Provider::where('pid', $pid)->first();
            $provider->is_active = 1;
            $provider->save();

            $this->parseProvider($provider);
            dump($provider->pid);
        }
    }

    /**
     * @param Provider $provider
     * @return void
     */
    public function parseProvider(Provider $provider)
    {
        $posts = $this->getChannelPosts($provider);
        $products = $this->getProducts($provider, $posts);

        foreach ($products as $product) {

            if (empty($product['attributes']['title']) || strlen($product['attributes']['title']) > 255) {
                continue;
            }

            $providerItem = ProviderItem::where('provider_id', $provider->id)
                ->where('title', $product['attributes']['title'])
                ->first();

            if (!$providerItem) {
                $productModel = Product::where('title', $product['attributes']['title'])->first();
                // если товар в БД есть с названием как в канале - то сразу ставим цену провайдера

                $providerItem = ProviderItem::create([
                    'provider_id' => $provider->id,
                    'title' => $product['attributes']['title'],
                    'price' => $product['attributes']['price'],
                    'status' => $productModel === null ? ProviderItem::STATUS_AWAIT : ProviderItem::STATUS_AUTO,
                ]);

                if ($productModel) {
                    $attrs = [
                        'provider_item_id' => $providerItem->id,
                        'product_id' => $productModel->id,
                    ];
                    ProductProviderPrice::updateOrCreate($attrs, array_merge($attrs, ['price' => $product['attributes']['price']]));
                }

            } else {
                if ($providerItem->status == ProviderItem::STATUS_ACCEPT) {
                    $attrs = [
                        'provider_item_id' => $providerItem->id,
                        'product_id' => $providerItem->product_id,
                    ];
                    ProductProviderPrice::updateOrCreate($attrs, array_merge($attrs, ['price' => $product['attributes']['price']]));
                }
            }
        }
    }

    /**
     * @param Provider $provider
     * @param array $posts
     * @return array
     */
    public function getProducts(Provider $provider, array $posts) : array
    {
        $products = [];
        $mapProcessors = [
            'iCentr_UA' => 'ICentrUA',
            'imonolit' => 'Imonolit',
            'wearefriendly' => 'WeareFriendly',
            'appteka' => 'Appteka',
            'optomiphone' => 'OptomIPhone',
            'iDesireKH' => 'IDesireKH',
            'iPeople_UA' => 'IPeopleUA',
            'applezt' => 'Applezt',
            'ilovephoneopt' => 'ILovePhoneOpt',
            'restarttradein' => 'RestartTradeIn',
            'MrFixUa' => 'MrFixUa',
            'ByryndychokApple' => 'ByryndychokApple',
        ];
        $clsProcessor = isset($mapProcessors[$provider->pid]) ?
            "App\Services\Parser\Processors\\" . $mapProcessors[$provider->pid]
            : null;
        if (!$clsProcessor) {
            echo "Provider $provider->pid processor not set \n";
            return [];
        }

        foreach ($posts as $post) {
            foreach ($this->postParse((new $clsProcessor), $post['content']) as $item) {
                $products[] = [
                    'attributes' => $item,
                    'provider_id' => $provider->id,
                ];
            }
        }
        return $products;
    }

    /*
     * @param IProcessor $processor
     * @param string $post
     *
     * @return array
     */
    public function postParse(IProcessor $processor, string $post): array
    {
        return $processor->parse($post);
    }

    /**
     * @param Provider $provider
     * @return array
     */
    public function getChannelPosts(Provider $provider) : array
    {
        $data = [];
        $url = "http://88.198.157.69:9000/index.php?channel=$provider->pid";
        $xml = simplexml_load_string(Curl::getPage($url));
        foreach ($xml->messages->message as $item) {
            $data[] = [
                'content' => (string) $item->content,
                'guid' => (int) $item->id,
            ];
        }

        return $data;
    }
}
