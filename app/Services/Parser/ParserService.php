<?php
declare( strict_types = 1 );

namespace App\Services\Parser;

use Illuminate\Support\Str;
use App\Services\Curl;
use App\Modules\Product\Models\{
    Product,
    Price,
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
    private $skipLastMessId = false;

    /**
     * @return void
     */
    public function run() : void
    {
        /*
        foreach (Price::get() as $price) {
            $providerItem = ProviderItem::where('product_id', $price->product_id)
                ->where('provider_id', $price->provider_id)
                ->first();

            $pp = ProductProviderPrice::create([
                'product_id' => $providerItem->product_id,
                'provider_item_id' => $providerItem->id,
                'price' => $providerItem->price,
            ]);
            echo $pp->id . "\n";
        }
        */

        //DB::statement('DELETE FROM product_providers_items');
        //$this->skipLastMessId = true;

        $this->splitProviderItems();
       
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
            echo $provider->pid . "\n";
        }
    }

    public function splitProviderItems()
    {
        ProviderItem::chunk(100, function($providerItems) {
            foreach ($providerItems as $providerItem) {
                $productTitles = $this->getSplitProductsByColor($providerItem->title);
                if (count($productTitles) > 1) {
                    foreach ($productTitles as $productTitle) {
                        $product = [
                            'price_time' => $providerItem->price_time,
                            'attributes' => [
                                'price' => $providerItem->price,
                                'title' => $productTitle,
                            ],
                        ];
                        $provider = Provider::find($providerItem->provider_id);
                        $providerItem->delete();
                        $this->parseProviderItem($provider, $product);

                        echo "$providerItem->title \n";
                    }
                }
            }
         });
    }

    public function getSplitProductsByColor(string $title): array
    {
        $title = str_replace(["\t", "\n", "\r"], "", $title);

        $colorsTitleVariants = $this->getColorsVariants();
        $title = str_ireplace(array_keys($colorsTitleVariants), $colorsTitleVariants, $title);

        $colors = $this->getColors();

        $pattern = '/('.implode('|', $colors).')/i';
        preg_match_all($pattern, $title, $match);

        if (!empty($match[1])) {
            $productColors = array_filter($match[1], function ($value) use ($colors, $title) {
                return !empty($value) && in_array(strtolower($value), $colors);
            });
            $colorsTitle = implode('/', $productColors);
            if (count($productColors) > 1 && substr_count($title, $colorsTitle)) {
                $products = [];
                foreach ($productColors as $productColor) {
                    $products[] = str_ireplace($colorsTitle, $productColor, $title);
                }
                return $products;
            }
        }

        return [$title];
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

            // split product title by colors: iPhone XS Max 64GB Space/Gold/Red
            $productsByColor = $this->getSplitProductsByColor($product['attributes']['title']);
            if (count($productsByColor) > 1) {
                $this->parseProviderItem($provider, $product);
            } else {
                foreach ($productsByColor as $productColorTitle) {
                    $product['attributes']['title'] = $productColorTitle;
                    $this->parseProviderItem($provider, $product);
                }
            }

       }
    }

    /**
     * @param Provider $provider
     * @param array $product
     */
    public function parseProviderItem(Provider $provider, array $product)
    {
        $providerItem = ProviderItem::where('provider_id', $provider->id)
            ->where('title', $product['attributes']['title'])
            ->where('price', $product['attributes']['price'])
            ->first();

        if (!$providerItem) {
            $productModel = Product::where('title', $product['attributes']['title'])->first();
            // если товар в БД есть с названием как в канале - то сразу ставим цену провайдера

            $providerItem = ProviderItem::create([
                'provider_id' => $provider->id,
                'title' => $product['attributes']['title'],
                'price' => $product['attributes']['price'],
                'price_time' => $product['price_time'],
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

                $prices = ProductProviderPrice::where('provider_item_id', $providerItem->id)
                    ->where('product_id', $providerItem->product_id)
                    ->get();

                foreach ($prices as $item) {
                    $item->price = $product['attributes']['price'];
                    $item->save();
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
                    'price_time' => $post['price_time'],
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
        if (!empty($provider->last_guid) && !$this->skipLastMessId) {
            $url.= '&min_id=' . $provider->last_guid;
        }

        $xml = simplexml_load_string(Curl::getPage($url));
        foreach ($xml->messages->message as $item) {
            $data[] = [
                'content' => (string) $item->content,
                'guid' => (int) $item->id,
                'price_time' => (int) $item->time,
            ];
        }

        if (count($data)) {
            $provider->last_guid = end($data)['guid'];
            $provider->save();
        }

        return $data;
    }

    public function getColors(): array
    {
        return [
            'blue',
            'black',
            'coral',
            'gold',
            'green',
            'gray',
            'matt',
            'platinum',
            'purpur',
            'purple',
            'red',
            'rose',
            'silver',
            'space',
            'white',
            'yellow',
        ];
    }

    public function getColorsVariants(): array
    {
        $colors = $this->getColors();

        $data = [];
        foreach ($colors as $colorFirst) {
            foreach ($colors as $colorTwo) {
                $data[",{$colorFirst} /{$colorTwo}"] = ", {$colorFirst}/{$colorTwo}";
                $data["{$colorFirst} /{$colorTwo}"] = "{$colorFirst}/{$colorTwo}";
            }
        }
        return $data;
    }
}
