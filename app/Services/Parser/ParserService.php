<?php
declare( strict_types = 1 );

namespace App\Services\Parser;

use App\Modules\Color\Models\Color;
use Illuminate\Support\Str;
use App\Services\Curl;
use App\Modules\Product\Models\{Product, Price, Provider, ProviderItem, ProductProviderPrice, ProviderLog};
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

        DB::statement('DELETE FROM product_provider_log');
        //DB::statement('DELETE FROM product_providers_items');

        $this->skipLastMessId = true;
        //$this->splitProviderItems();

        $providers = [
            'swipe_ua',
            'icoolaopt',
            'ioptua',
            'SBS_Lviv',
            'apple_center_ua',
            'iPeople_UA',
            'imonolit',
            'appteka',
            'restarttradein',
            'iCentr_UA',
            'MrFixUa',
            'ByryndychokApple',
            'ilovephoneopt',
            'applezt',
            'iDesireKH',
            'optomiphone',
            'wearefriendly',
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
                    }
                }
            }
         });
    }

    public function getSplitProductsByColor(string $title): array
    {
        $title = str_replace(["\t", "\n", "\r"], "", $title);

        $colorsTitleVariants = $this->getColorsVariants();
        $doubleColors = $this->getDoubleColors();
        foreach ($doubleColors as $k => $v) {
            $title = str_ireplace($v, $k, $title);
        }
        $title = str_ireplace(array_keys($colorsTitleVariants), $colorsTitleVariants, $title);
        $colors = $this->getColors();
        // replace gold/green/space => gold/green/space
        // replace gold / green / space => gold/green/space

        foreach ($colors as $colorOne) {
            foreach ($colors as $colorTwo) {
                $title = str_replace("$colorOne  $colorTwo", "$colorOne/$colorTwo", $title);
                $title = str_replace("$colorOne $colorTwo", "$colorOne/$colorTwo", $title);
                $title = str_replace("$colorOne / $colorTwo", "$colorOne/$colorTwo", $title);
                $title = str_replace("$colorOne /$colorTwo", "$colorOne/$colorTwo", $title);
                $title = str_replace("$colorOne/ $colorTwo", "$colorOne/$colorTwo", $title);
            }
        }

        $pattern = '/('.implode('|', $colors).')/i';
        preg_match_all($pattern, $title, $match);

        if (!empty($match[1])) {
            //dump($match);
            foreach ($match[1] as $i=>$matchVal) {
                if ($matchVal === 'mat' && substr_count($title, 'matte')) {
                    $match[1][$i] = 'matte';
                } elseif ($matchVal === 'mat' && substr_count($title, 'matt')) {
                    $match[1][$i] = 'matt';
                }
            }

            $productColors = array_filter($match[1], function ($value) use ($colors, $title) {
                return !empty($value) && in_array(strtolower($value), $colors);
            });

            $colorsTitle = implode('/', $productColors);

            if (count($productColors) > 1 && substr_count($title, $colorsTitle)) {
                $products = [];
                foreach ($productColors as $productColor) {
                    $cTitle = str_ireplace($colorsTitle, $productColor, $title);
                    $cTitle = str_replace("$productColor/", "$productColor ", $cTitle);

                    foreach ($doubleColors as $k => $v) {
                        $cTitle = str_replace($k, $v, $cTitle);
                    }

                    $products[] = $cTitle;
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

            if (empty($product['attributes']['title']) ||
                strlen($product['attributes']['title']) > 190 ||
                $product['attributes']['price'] <= 0
            ) {
                //$params['content'] = $product['attributes']['title'];
                //ProviderLog::add($params);
                continue;
            }
            // split product title by colors: iPhone XS Max 64GB Space/Gold/Red
            $productsByColor = $this->getSplitProductsByColor($product['attributes']['title']);

            if (count($productsByColor) === 1) {
                $this->parseProviderItem($provider, $product);
            } else {
                foreach ($productsByColor as $productColorTitle) {
                    $this->parseProviderItem($provider, [
                        "attributes" => [
                            "title" => $productColorTitle,
                            "price" => $product['attributes']['price'],
                          ],
                          "provider_id" => $product['provider_id'],
                          "price_time" => $product['price_time'],
                    ]);
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
        if ($product['attributes']['price'] >= 1000000) {
            return;
        }

        $providerItem = ProviderItem::where('provider_id', $provider->id)
            ->where('title', $product['attributes']['title'])
            ->first();

        //dump($product['attributes']['title']);


        if (substr_count($product['attributes']['title'], 'MJLQ2')) {
        }

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

            if ($providerItem->status != ProviderItem::STATUS_CANCEL && $providerItem->price_time <= $product['price_time']) {
                $providerItem->price = $product['attributes']['price'];
                $providerItem->price_time = $product['price_time'];
                $providerItem->save();
            }

            if ($providerItem->status == ProviderItem::STATUS_ACCEPT) {
                $providerItem->price = $product['attributes']['price'];
                $providerItem->price_time = $product['price_time'];
                $providerItem->save();

                $prices = ProductProviderPrice::query()
                    ->where('provider_item_id', $providerItem->id)
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
            'appteka' => 'Appteka',
            'applezt' => 'Applezt',
            'imonolit' => 'Imonolit',
            'iCentr_UA' => 'ICentrUA',
            'wearefriendly' => 'WeareFriendly',
            'optomiphone' => 'OptomIPhone',
            'iDesireKH' => 'IDesireKH',
            'iPeople_UA' => 'IPeopleUA',
            'ilovephoneopt' => 'ILovePhoneOpt',
            'restarttradein' => 'RestartTradeIn',
            'MrFixUa' => 'MrFixUa',
            'ByryndychokApple' => 'ByryndychokApple',
            'apple_center_ua' => 'AppleCenterUa',
            'SBS_Lviv' => 'SBSLviv',
            'ioptua' => 'IoptUa',
            'icoolaopt' => 'IcoolaOpt',
            'swipe_ua' => 'SwipeUa',
        ];
        $clsProcessor = isset($mapProcessors[$provider->pid]) ?
            "App\Services\Parser\Processors\\" . $mapProcessors[$provider->pid]
            : null;
        if (!$clsProcessor) {
            //echo "Provider $provider->pid processor not set \n";
            return [];
        }

        foreach ($posts as $post) {
            foreach ($this->postParse((new $clsProcessor), $post, $provider) as $item) {
                $products[] = [
                    'attributes' => $item,
                    'provider_id' => $provider->id,
                    'price_time' => $post['price_time'],
                ];
            }
        }
        return $products;
    }

    /**
     * @param IProcessor $processor
     * @param array $post
     * @param Provider $provider
     * @return array
     */
    public function postParse(IProcessor $processor, array $post, Provider $provider): array
    {
        $attributes = [
            'provider_id' => $provider->id,
            'message_time' => $post['price_time'],
        ];

        return $processor->parse($post['content'], $attributes);
    }

    /**
     * @param Provider $provider
     * @return array
     */
    public function getChannelPosts(Provider $provider) : array
    {
        $data = [];

        $lastId = max(0,  $provider->last_guid - 10);

        /*
        $url = "http://88.198.157.69:9000/index.php?channel=$provider->pid";
        if (!empty($provider->last_guid) && !$this->skipLastMessId) {
            $url.= '&min_id=' . $lastId;
        }
        $xml = simplexml_load_string(Curl::getPage($url));
        foreach ($json->messages->message as $item) {
            array_unshift($data, [
                'content' => (string) $item->content,
                'guid' => (int) $item->id,
                'price_time' => (int) $item->time,
            ]);
        }
        */

        $url = "https://tg.i-c-a.su/json/$provider->pid?limit=100";
        $content = file_get_contents($url);
        $json = json_decode($content, true);
        foreach ($json['messages'] as $item) {
            array_unshift($data, [
                'content' => (string) $item['message'],
                'guid' => (int) $item['id'],
                'price_time' => (int) $item['date'],
                'date' => date('Y-m-d', $item['date']),
            ]);
        }

        if (count($data)) {
            $provider->last_guid = end($data)['guid'];
            $provider->save();
        }

        return $data;
    }

    public function getColors(): array
    {
        return array_map(function($value) { return str_replace(' ', '-', strtolower($value)); }, $this->getColorsOriginal());
    }

    public function getColorsOriginal(): array
    {
        $colors = Color::orderByRaw('CHAR_LENGTH(code) ASC')->get()->pluck('code')->toArray();

        usort($colors, function ($a, $b) {
            return substr_count($a, ' ') ? -1 : 1;
        });

        return array_map(function($value) { return strtolower($value); }, $colors);
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function getDoubleColors(): array
    {
        $colors = $this->getColorsOriginal();

        $data = [];
        foreach ($colors as $color) {
            if (substr_count($color, ' ')) {
                $data[str_replace(' ', '-', $color)] = $color;
            }
        }
        return $data;
    }

    /**
     *
     */
    public function textSync()
    {
        ProviderItem::whereRaw("title LIKE '%А%'")->chunk(100, function ($items) {
            foreach ($items as $item) {
                $title = $string = preg_replace('/[[:^print:][а-яА-Я]/', '', $item->title);
                echo "$title : $item->title\n";

               if (mb_substr_count($item->title, 'А', 'UTF-8')) {

                   $parts = explode('А', $item->title);
                   if (count($parts) > 1) {

                       $item->title = replate_to_letter_a($item->title);
                       $item->save();

                       echo $item->title . "\n";
                       echo $item->id . "\n";
                   }
               }
            }
        });
    }
}
