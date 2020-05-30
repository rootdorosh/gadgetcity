<?php
declare( strict_types = 1 );

namespace App\Services\Parser;

use Illuminate\Support\Str;
use App\Services\Curl;
use App\Modules\Product\Models\{
    Product,
    Provider,
    ProviderItem
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
        //DB::statement('DELETE FROM product_providers_items');

        $this->parseProvider(Provider::where('pid', 'imonolit')->first());
        $this->parseProvider(Provider::where('pid', 'iCentr_UA')->first());
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

            $providerItem = ProviderItem::where('provider_id', $provider->id)
                ->where('title', $product['attributes']['title'])
                ->first();

            if (!$providerItem) {
                $productModel = Product::where('title', $product['attributes']['title'])->first();
                // если товар в БД есть с названием как в канале - то сразу ставим цену провайдера

                ProviderItem::create([
                    'provider_id' => $provider->id,
                    'title' => $product['attributes']['title'],
                    'price' => $product['attributes']['price'],
                    'status' => $productModel === null ? ProviderItem::STATUS_AWAIT : ProviderItem::STATUS_AUTO,
                ]);

                if ($productModel) {
                    $attrs = [
                        'provider_id' => $provider->id,
                        'product_id' => $productModel->id,
                    ];
                    Price::updateOrCreate($attrs, array_merge($attrs, ['price' => $product['attributes']['price']]));
                }

            } else {
                if ($providerItem->status == ProviderItem::STATUS_ACCEPT) {
                    $attrs = [
                        'provider_id' => $provider->id,
                        'product_id' => $providerItem->product_id,
                    ];
                    Price::updateOrCreate($attrs, array_merge($attrs, ['price' => $product['attributes']['price']]));
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
        echo $post . "\n\n";

        return $processor->parse($post);
    }

    /**
     * @param Provider $provider
     * @return array
     */
    public function getChannelPosts(Provider $provider) : array
    {
        $data = [];
        $url = "https://tg.i-c-a.su/rss/$provider->pid?limit=100";
        $xml = simplexml_load_string(Curl::getPage($url));
        foreach ($xml->channel->item as $item) {
            $data[] = [
                'content' => (string) $item->description,
                'guid' => (int)Str::afterLast($item->guid, '/'),
            ];
        }

        return $data;
    }
}
