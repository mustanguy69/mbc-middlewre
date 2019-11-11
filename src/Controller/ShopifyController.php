<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RexSoapController;
use Psr\Log\LoggerInterface;

const shopifyIApiurl = "https://4bcd1fe009483d29119dd1af3e4b320c:c4d1b483afde579d19810953287548da@bikes-com-au.myshopify.com/admin/api/2019-10/";

class ShopifyController extends AbstractController
{

    /**
     * Call Shopify API for add/update image products
     * @Route("/addUpdateImage/{productId}")
     * @param $productId
     * @param $image
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function addUpdateImage($productId, $images = null) {
        $images = [
            'https://www.bikes.com.au/media/catalog/product/cache/2/small_image/300x300/9df78eab33525d08d6e5fb8d27136e95/p/a/paralane_al_105.jpg',
            'https://www.bikes.com.au/media/catalog/product/cache/2/small_image/300x300/9df78eab33525d08d6e5fb8d27136e95/2/0/2017-focus-mares-ax-commuter-_0.jpg'
        ];

        $data = [
            'product' => [
                'images' => [],
            ],
        ];
        foreach ($images as $image) {
            $data['product']['images'][] = ['src' => $image];
        }

        try {
            $client = HttpClient::create();
            $response = $client->request('PUT', shopifyIApiurl . 'products/' . $productId . '.json', ['json' => $data]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return json_decode($response->getContent());

    }

    /**
     * Call Shopify API for add custom collection
     * @Route("/createCustomCollections")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function createCustomCollection() {
        $string = "
        §  Inner Tubes
        §  Rim Cement
        §  Puncture Repair
        §  Tubeless Accessories
        §  Rim Tape
        §  Valve Extensions
        §  Short Sleeved Cycling Jerseys
        §  Long Sleeved Cycling Jerseys
        §  Cycling Bib Shorts
        §  Cycling Shorts
        §  3/4 Length Shorts
        §  Cycling Bib Tights & Trousers
        §  Cycling Jackets
        §  Cycling Gilets
        §  Cycling Base Layers
        §  Cycling Socks
        §  Cycling Gloves & Mitts
        §  Arm, Leg And Knee Warmers
        §  Cycling Headwear
        §  Compression
        §  High Visibility Safety Clothing
        §  Cycling Skin Suits
        §  Kids Cycling Clothing
        §  MTB Body Armour
        §  Clothing Care
        §  MBC Cycling Team Clothing
        §  Road Bike Shoes
        §  Mountain Bike Shoes
        §  Overshoes & Booties
        §  Shoe Spares
        §  Road Bike Helmets
        §  Mountain Bike Helmets
        §  Helmet Spares & Accessories
        §  Kids Bike Helmets
        §  Cycling Sunglasses
        §  Mountain Bike Goggles
        §  CyclingT-Shirts § Cycling Hoodies
        §  Bike Locks & Security
        §  BicycleMirrors
        §  Bicycle Bells & Horns
        §  Bicycle Mudguards
        §  GearHangers
        §  Bike Frame Protectors
        §  Embrocation & Chamois Creme
        §  Bike Light Sets
        §  Rear Bike Lights
        §  Front Bike Lights
        §  Bike Bottles
        §  Bottle Cages
        §  Hydration Packs
        §  Hydration Pack Accessories
        §  Cycle Computers
        §  Heart Rate Monitors
        §  GPS Cycle Computers
        §  Sports Watches
        §  Cycle Computer Accessories
        §  Bike Bags
        §  Bike Wheel Bags
        §  Car Bike Racks
        §  Pannier Bags
        §  Pannier Racks
        §  Bike Saddle & Frame Packs
        §  Rucksacks & Holdalls
        §  Cycling Turbo Trainers
        §  Cycle Rollers
        §  Training Guides
        §  Trainer Accessories
        §  Child Bike Seats
        §  Child Tag-a-Long Bikes
        §  Child Bike Trailers
        §  Wheels
        §  Bikes and Frames
        §  Parts and Accessories § Clothing
        §  Sports Energy Drinks
        §  Energy Bars
        §  Energy & Hydration Gels
        §  Workshop Tools
        §  Bicycle Pumps
        §  Multitools
        §  Bike Cleaning
        §  Bike Lubrication & Fluids
        §  Workstands
        §  Bike Storage
        §  Maintenance Guides";

        $cockpit = "
        § Road Bike Aero Tribars 
        § MTB Handlebar Grips 
        § Road Bike Handlebar Tape 
        § Handlebars 
        § MTB Handlebars 
        § Road Bike Handlebars 
        § MTB Bar Ends 
        § Headset Spares 
        § Road Bike Headset Spares 
        § MTB Headset Spares 
        § Headsets 
        § MTB Headsets 
        § Road Bike Headsets 
        § Pedals & Cleats 
        § MTB Bike Pedals & Cleats 
        § Road Bike Pedals & Cleats 
        § Saddles 
        § MTB Saddles 
        § Road Bike Saddles 
        § Seatpost Collars 
        § Road Bike Seatpost Collars 
        § MTB Seatpost Collars 
        § Seatposts 
        § Stems 
        § MTB Seatposts 
        § Road Bike Seatposts 
        § MTB Stems 
        § Road Bike Stems";

        $training =

        $string = trim(preg_replace('/\s+/', ' ', $string));
        $cockpit = trim(preg_replace('/\s+/', ' ', $cockpit));
//        $firstArray = explode('• ', $string);
//        foreach ($firstArray as $value) {
//            $secondArray = explode('o ',$value);
//            $level2[] = explode('o ',$value);
//            foreach ($secondArray as $value) {
//                $thirdArray = explode('§  ',$value);
//                foreach ($thirdArray as $key => $value) {
//                    $fourthArray[] = explode('§ ',$value);
//                }
//            }
//        }
//
//        //categories level 1
//        unset($firstArray[0]);
//        foreach ($firstArray as $value) {
//            $level1[strstr($value, ' o ', true)] = $this->multiexplode(['o ',' §  ', ' § '], $value);
//        }
//        foreach (array_keys($level1) as $key) {
//            unset($level1[$key][0]);
//        }
//
//        //categories level 2
//        foreach ($level2 as $value) {
//            foreach(array_keys($value) as $key) {
//                unset($level2[$key][0]);
//            }
//        }
//
//        foreach ($level2 as $value) {
//            foreach ($value as $item) {
//                $level2Sanityze[] = $item;
//            }
//        }
//        $level2 = [];
//        foreach ($level2Sanityze as $value) {
//            $test = strstr($value, ' § ', true);
//            $level2Before[] = explode(' § ',$value);
//            foreach ($level2Before as $value) {
//                $level2[$value[0]] = $value;
//            }
//        }
//
//        foreach(array_keys($level2) as $key) {
//            unset($level2[$key][0]);
//        }

//        foreach ($level1 as $key => $value) {
//            $rules = [];
//            foreach ($value as $item) {
//                $rules[] = [
//                    "column" => "type",
//                    "relation" => "equals",
//                    "condition" => trim($item)
//                ];
//            }
//            $data = [
//                "smart_collection" => [
//                    "title" => trim($key),
//                    "published_scope" => "global",
//                    "disjunctive" => "true",
//                    "rules" => $rules
//                ]
//            ];
//            //dump($data);
//            try {
//                $client = HttpClient::create();
//                $response = $client->request('POST', shopifyIApiurl . 'smart_collections.json', ['json' => $data]);
//            } catch (\Exception $e) {
//                dump($e);
//            }
//        }


//        //categories level 3
//        $level3 = explode('§  ',$string);
//        unset($level3[0]);
//
//        foreach ($level3 as $value) {
//            $title = trim($value);
//            $data = [
//                "smart_collection" => [
//                    "title" => $title,
//                    "published_scope" => "global",
//                    "disjunctive" => "false",
//                    "rules" => [
//                        [
//                            "column" => "type",
//                            "relation" => "equals",
//                            "condition" => $title,
//                        ],
//                    ],
//                ],
//            ];
//
//            try {
//                $client = HttpClient::create();
//                $response = $client->request('POST', shopifyIApiurl . 'smart_collections.json', ['json' => $data]);
//            } catch (\Exception $e) {
//                dump($e);
//            }
//        }

//        foreach ($level2 as $key => $value) {
//            $rules = [];
//            foreach ($value as $item) {
//                $rules[] = [
//                    "column" => "type",
//                    "relation" => "equals",
//                    "condition" => trim($item)
//                ];
//            }
//            $data = [
//                "smart_collection" => [
//                    "title" => trim($key),
//                    "published_scope" => "global",
//                    "disjunctive" => "true",
//                    "rules" => $rules
//                ]
//            ];
//            try {
//                $client = HttpClient::create();
//                $response = $client->request('POST', shopifyIApiurl . 'smart_collections.json', ['json' => $data]);
//            } catch (\Exception $e) {
//                var_dump($e);
//            }
//        }

        //cockpit update
//        $cockpitUdate = explode('§ ',$cockpit);
//        unset($cockpitUdate[0]);
//
//        foreach ($cockpitUdate as $value) {
//            $title = trim($value);
//            $rules[] = [
//                "column" => "type",
//                "relation" => "equals",
//                "condition" => trim($title)
//            ];
//        }
//        $data = [
//            "smart_collection" => [
//                "id" => "153358073916",
//                "title" => "Cockpit",
//                "published_scope" => "global",
//                "disjunctive" => "true",
//                "rules" => $rules,
//            ],
//        ];
//
//        try {
//            $client = HttpClient::create();
//            $response = $client->request('PUT', shopifyIApiurl . 'smart_collections/153358073916.json', ['json' => $data]);
//        } catch (\Exception $e) {
//            dump($e);
//        }


        return new JsonResponse(['status' => 'finish']);

    }


    function multiexplode ($delimiters,$string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }


    /**
     * Call Shopify API for add Page
     * @Route("/createPages")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function createPages() {

        $pages = "Store Location,Store Trading Hours,Payment Options,Shipping & Delivery,Return / Refund Policy,Our Brands,Bike Hire,Bike Servicing,Mobile Mechanic Service,Bicycle Size Guide,Component Fitting,Corporate Sales,Our Team,Contact Us,Our Quality Promise,Privacy Statement,Disclaimer,Blog / Articles";
        $pages = explode(',', $pages);

        foreach ($pages as $page) {
            $data = [
                'page' => [
                    'title' => $page,
                    'body_html' => "<h2>".$page."</h2>",
                ]
            ];

            try {
                $client = HttpClient::create();
                $response = $client->request('POST', shopifyIApiurl . '/pages.json', ['json' => $data]);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }


        return new JsonResponse(['status' => 'finish']);
    }

    /**
     * Call Shopify API for getting product ID by title
     * @Route("/getProductIdByTitle/{title}")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function getProductIdByTitle($title) {

        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyIApiurl . 'products.json?title='.$this->replaceSpecialCharacters(trim($title)));
        } catch (\Exception $e) {
            var_dump($e);
        }
        $content = json_decode($response->getContent());

        return $content->products[0]->id;
    }

    /**
     * Call Shopify API for setting metafield product supplier stock
     * @Route("/setMetafieldProductSupplierStock/{productId}/{stock}")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function setMetafieldProductSupplierStock($productId, $stock) {

        $data = [
            "metafield" => [
                "namespace" => "inventory",
                "key" => "supplier_stock",
                "value" => ''.(int)$stock . '',
                "value_type" => 'integer',
            ]
        ];

        try {
            $client = HttpClient::create();
            $response = $client->request('POST', shopifyIApiurl . 'products/'.$productId.'/metafields.json', ['json' => $data]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $response->getContent();
    }

    /**
     * Call Shopify API for getting products
     * @Route("/getProductsShopify", name="getProductsShopify")
     */
    function getProducts() {

        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyIApiurl . 'products.json');
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $response->toArray();
    }

    /**
     * Call Shopify API for getting products by title
     * @Route("/getProductsByTitle", name="getProductsByTitle")
     */
    function getProductByTitle(Request $request) {

        $title = $request->get('title');
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyIApiurl . 'products.json?title='.$this->replaceSpecialCharacters(trim($title)));
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new JsonResponse($response->getContent());
    }


    function replaceSpecialCharacters($string) {

        return str_replace(array('&', '-', '\t', '"', '  ', '/', '+', "'"), array('%26', '%2D', '', '', ' ', '%2F', '%2B', ''), $string);
    }

}
