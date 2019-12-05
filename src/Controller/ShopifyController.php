<?php

namespace App\Controller;

use App\Entity\Wishlist;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RexSoapController;
use Psr\Log\LoggerInterface;

const shopifyApiurl = "https://4bcd1fe009483d29119dd1af3e4b320c:c4d1b483afde579d19810953287548da@bikes-com-au.myshopify.com/admin/api/2019-10/";

/**
 * Call Shopify API for add/update image products
 * @Route("/api")
 */
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
    public function addUpdateImage($productId, $image, $name) {

        if (base64_encode(base64_decode($image, true)) === $image){
            $data = [
                'image' => [
                    'attachment' => $image,
                    'alt' => $name,
                ],
            ];
        } else {
            $data = [
                'image' => [
                    'src' => $image,
                    'alt' => $name,
                ],
            ];
        }

        try {
            $client = HttpClient::create();
            $response = $client->request('POST', shopifyApiurl . 'products/' . $productId . '/images.json', ['json' => $data]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        $response = json_decode($response->getContent());

        return $response->image->id;

    }

    /**
     * Call Shopify API for attach image variant
     * @Route("/attachImageWithVariant/{productId}")
     * @param $productId
     * @param $image
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function attachImageWithVariant($variantId, $imageId) {

        $data = [
            'variant' => [
                'id' => $variantId,
                'image_id' => $imageId,
            ],
        ];

        try {
            $client = HttpClient::create();
            $response = $client->request('PUT', shopifyApiurl . 'variants/' . $variantId . '.json', ['json' => $data]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        $response = json_decode($response->getContent());

        return new Response('Image matched to variant');

    }

    /**
     * Call Shopify API for add/update image products
     * @Route("/removeImage/{productId}")
     * @param $productId
     * @param $image
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function removeImage($productId, $shopifyId) {

        try {
            $client = HttpClient::create();
            $response = $client->request('DELETE', shopifyApiurl . 'products/' . $productId . '/images/'. $shopifyId .'.json');
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new Response();

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
        ini_set('max_execution_time', 0);

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
                $response = $client->request('POST', shopifyApiurl . '/pages.json', ['json' => $data]);
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
            $response = $client->request('GET', shopifyApiurl . 'products.json?title='.$this->replaceSpecialCharacters(trim($title)));
        } catch (\Exception $e) {
            var_dump($e);
        }
        $content = json_decode($response->getContent());

        return $content;
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
            $response = $client->request('POST', shopifyApiurl . 'products/'.$productId.'/metafields.json', ['json' => $data]);
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
            $response = $client->request('GET', shopifyApiurl . 'products.json');
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new JsonResponse($response->getContent());
    }

    /**
     * Call Shopify API for getting options of all variants products
     * @Route("/getAllProductVariantOptions", name="getAllProductVariantOptions")
     */
    function getAllProductVariantOptions(Request $request) {
        $collectionName = $request->query->get('collectionName');
        $arr = [];
        $em = $this->getDoctrine()->getManager();
        $products = [];
        if(strpos($collectionName, ',') !== false) {
            $collectionArray = explode(',', $collectionName);
            foreach ($collectionArray as $collectionName) {
                $collection = $em->getRepository('App:ProductTypes')->findOneBy(['name' => $collectionName]);
                $products[] = $em->getRepository('App:Products')->findBy(['type' => $collection]);
            }
        } else {
            $collection = $em->getRepository('App:ProductTypes')->findOneBy(['name' => $collectionName]);
            $products = $em->getRepository('App:Products')->findBy(['type' => $collection]);
        }

        $colorsArr = [];
        $sizeArr = [];

        foreach ($products as $product) {
            if(is_array($product)) {
                foreach ($product as $item) {
                    if ($item->getSize()) {
                        $sizeArr[] =  $item->getSize()->getSize();
                    }

                    if($item->getColor()) {
                        $colorsArr[] =  $item->getColor()->getName();
                    }
                }
            } else {
                if ($product->getSize()) {
                    $sizeArr[] =  $product->getSize()->getSize();
                }

                if($product->getColor()) {
                    $colorsArr[] =  $product->getColor()->getName();
                }
            }
        }

        $arr['colors'] =  array_unique($colorsArr);
        $arr['sizes'] = array_unique($sizeArr);

        return new JsonResponse($arr);
    }

    function paginationRequestVariants($paginationLink) {
        $paginationLinkArr = explode('; ', $paginationLink[0]);
        $url = '';
        if($paginationLinkArr[1] == 'rel="next"') {
            $url = $paginationLinkArr[0];
        } elseif ($paginationLinkArr[2] == 'rel="next"') {
            $url = str_replace('rel="previous", ', '', $paginationLinkArr[1]);
        }

        if ($url !== '') {
            $paginationLinkNext = str_replace('<', '', $url);
            $paginationLinkNext = str_replace('>', '', $paginationLinkNext);
            $parts = parse_url($paginationLinkNext);
            parse_str($parts['query'], $query);
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyApiurl . '/products.json?limit=250&fields=variants&page_info='.$query['page_info']);

            return $response;
        }
    }

    /**
     * Call Shopify API for getting products by title
     * @Route("/getProductsByTitle", name="getProductsByTitle")
     */
    function getProductByTitle(Request $request) {

        $title = $request->get('title');
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyApiurl . 'products.json?title='.$this->replaceSpecialCharacters(trim($title)));
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new JsonResponse($response->getContent());
    }

    /**
     * Call Shopify API for getting products by title
     * @Route("/updateProductShopify", name="updateProductShopify")
     */
    function updateProductShopify($id, $json) {

        try {
            $client = HttpClient::create();
            $response = $client->request('PUT', shopifyApiurl . 'products/'.$id.'.json', ['json' => $json]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new Response($response->getContent());
    }

    /**
     * Call Shopify API for getting product variants
     * @Route("/getProductVariantsShopify", name="getProductVariants")
     */
    function getProductVariantsShopify($id) {

        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyApiurl . 'variants/'.$id.'.json');
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new Response($response->getContent());
    }

    /**
     * Call Shopify API for getting product variants
     * @Route("/updateProductVariantShopify", name="updateProductVariant")
     */
    function updateVariantShopify($id, $json) {

        try {
            $client = HttpClient::create();
            $response = $client->request('PUT', shopifyApiurl . 'variants/'.$id.'.json', ['json' => $json]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return new Response($response->getContent());
    }

    /**
     * Call Shopify API for removing all products
     * @Route("/removeAllProducts", name="removeAllProducts")
     */
    function removeAllProducts() {
        ini_set('max_execution_time', 0);
        try {
            $productsContent = [];
            $client = HttpClient::create();
            $productsRequest = $client->request('GET', shopifyApiurl . 'products.json?limit=250&fields=id');
            $productsContent[] = json_decode($productsRequest->getContent(), true);
            $client = HttpClient::create();
            $countRequest = $client->request('GET', shopifyApiurl . 'products/count.json');
            $countContent = json_decode($countRequest->getContent(), true);
            $count = $countContent['count'];

            if(array_key_exists('link', $productsRequest->getHeaders())) {
                $paginationLink = $productsRequest->getHeaders()['link'];
                $occurence = floor(($count / 250));
                for($i = 1; $i <= $occurence; $i++) {
                    $result = $this->paginationRequestProducts($paginationLink);
                    if ($result != false) {
                        $productsContent[] = json_decode($result->getContent(), true);
                        $paginationLink = $result->getHeaders()['link'];
                    }
                }
            }
            foreach ($productsContent as $productId) {
                foreach ($productId['products'] as $id) {
                    $client = HttpClient::create();
                    $productsDelete = $client->request('DELETE', shopifyApiurl . 'products/'.$id['id'].'.json');
                }
            }

        } catch (\Exception $e) {
            var_dump($e);
        }

        return new Response('finish');
    }

    function paginationRequestProducts($paginationLink) {
        $paginationLinkArr = explode('; ', $paginationLink[0]);
        $url = '';
        if($paginationLinkArr[1] == 'rel="next"') {
            $url = $paginationLinkArr[0];
        } elseif ($paginationLinkArr[2] == 'rel="next"') {
            $url = str_replace('rel="previous", ', '', $paginationLinkArr[1]);
        }

        if ($url !== '') {
            $paginationLinkNext = str_replace('<', '', $url);
            $paginationLinkNext = str_replace('>', '', $paginationLinkNext);
            $parts = parse_url($paginationLinkNext);
            parse_str($parts['query'], $query);
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyApiurl . '/products.json?limit=250&&fields=id&page_info='.$query['page_info']);

            return $response;
        }
    }

    /**
     * Call Shopify API for getting metafield customer
     * @Route("/getMetafieldCustomer")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function getMetafieldCustomer($customerId, $key) {

        try {
            $client = HttpClient::create();
            $response = $client->request('GET', shopifyApiurl . 'customers/'.$customerId.'/metafields.json?key='.$key);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $response->getContent();
    }


    /**
     * Call Shopify API for setting metafield wishlist
     * @Route("/setMetafieldWishlist")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function setMetafieldWishlist(Request $request) {

        $customerId = $request->request->get('customerId');
        $productId = $request->request->get('productId');
        $wishlist = $this->getMetafieldCustomer($customerId,'');
        $wishlistDecoded = json_decode($wishlist, true);
        $productIds = '';

        $em = $this->getDoctrine()->getManager();
        $wishlist = $em->getRepository('App:Wishlist')->findOneBy(['customerId' => $customerId]);
        if($wishlist) {
            $products = $wishlist->getProductIds();
            if($products !== null or $products !== "") {
                $wishlist->setProductIds($productIds);
            } else {
                $wishlist->setProductIds($products . ',' . $productId);
            }
        } else {
            $wishlist = new Wishlist();
            $wishlist->setCustomerId($customerId);
            $wishlist->setProductIds($productIds);

            $em->persist($wishlist);

        }

        $em->flush();
        if(count($wishlistDecoded['metafields']) !== 0) {
            if($wishlistDecoded['metafields'][0]['value'] !== "") {
                if(strpos($wishlistDecoded['metafields'][0]['value'], ',') !== false) {
                    $productIdTab = explode(',' , $wishlistDecoded['metafields'][0]['value']);
                    foreach ($productIdTab as $value) {
                        if($value !== "null") {
                            $productIds .= ',' . $value;
                        }
                    }
                } else {
                    if($wishlistDecoded['metafields'][0]['value'] === "null") {
                        $productIds = $productId;
                    } else {
                        $productIds = $wishlistDecoded['metafields'][0]['value'] .',' . $productId;
                    }

                }

                $data = [
                    "metafield" => [
                        "id" => $wishlistDecoded['metafields'][0]['id'],
                        "value" => '' . $productIds . '',
                        "value_type" => 'string',
                    ]
                ];

                try {
                    $client = HttpClient::create();
                    $response = $client->request('PUT', shopifyApiurl . 'metafields/'.$wishlistDecoded['metafields'][0]['id'].'.json', ['json' => $data]);
                } catch (\Exception $e) {
                    var_dump($e);
                }
            }
        } else {
            $data = [
                "metafield" => [
                    "namespace" => "wishlist",
                    "key" => "wishlist-products",
                    "value" => '' . $productId . '',
                    "value_type" => 'string',
                ]
            ];

            try {
                $client = HttpClient::create();
                $response = $client->request('POST', shopifyApiurl . 'customers/'.$customerId.'/metafields.json', ['json' => $data]);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        return new Response('wishlist saved');
    }


    /**
     * set Products in wishlist bdd
     * @Route("/setWishlist")
     * @return mixed
     */
    function setWishlist(Request $request) {

        $customerId = $request->request->get('customerId');
        $productId = $request->request->get('productId');

        $em = $this->getDoctrine()->getManager();
        $wishlist = $em->getRepository('App:Wishlist')->findOneBy(['customerId' => $customerId]);
        if($wishlist) {
            $products = $wishlist->getProductIds();
            if($products == null or $products == "") {
                $wishlist->setProductIds($productId);
            } else {
                $wishlist->setProductIds($products . ',' . $productId);
            }
        } else {
            $wishlist = new Wishlist();
            $wishlist->setCustomerId($customerId);
            $wishlist->setProductIds($productId);

            $em->persist($wishlist);

        }

        $em->flush();

        return new Response('wishlist saved');
    }

    /**
     * get all products in wishlist
     * @Route("/getWishlist")
     * @return mixed
     */
    function getWishlist(Request $request) {

        $customerId = $request->request->get('customerId');

        $em = $this->getDoctrine()->getManager();
        $wishlist = $em->getRepository('App:Wishlist')->findOneBy(['customerId' => $customerId]);

        if ($wishlist) {
            $result = $wishlist->getProductIds();
        } else {
            $result = null;
        }

        return new Response($result);
    }

    /**
     * remove Products from wishlist bdd
     * @Route("/removeWishlist")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function removeWishlist(Request $request) {

        $customerId = $request->request->get('customerId');
        $productId = $request->request->get('productId');

        $em = $this->getDoctrine()->getManager();
        $wishlist = $em->getRepository('App:Wishlist')->findOneBy(['customerId' => $customerId]);
        $wishlistExploded = explode(',', $wishlist->getProductIds());
        $productIds = [];
        foreach ($wishlistExploded as $product) {
            if($product !== $productId) {
                $productIds[] = $product;
            }
        }
        $wishlistImploded = implode(',', $productIds);
        $wishlist->setProductIds($wishlistImploded);
        $em->flush();

        return new Response('product removed from the wishlist');
    }

    /**
     * Call Shopify API for removing product form metafield wishlist
     * @Route("/removeProductFromWishlist")
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    function removeProductFromWishlist(Request $request) {

        $customerId = $request->request->get('customerId');
        $productIdToDelete = $request->request->get('productId');
        $wishlist = $this->getMetafieldCustomer($customerId,'wishlist-products');
        $wishlistDecoded = json_decode($wishlist, true);
        $productIds = '';
        if(count($wishlistDecoded['metafields']) !== 0) {
            if($wishlistDecoded['metafields'][0]['value'] !== "") {
                if(strpos($wishlistDecoded['metafields'][0]['value'], ',') !== false) {
                    $productIdTab = explode(',' , $wishlistDecoded['metafields'][0]['value']);
                    foreach ($productIdTab as $value) {
                        if((string) $productIdToDelete !== (string) $value) {
                            if($productIds == '') {
                                $productIds .= $value;
                            } else {
                                $productIds .= ',' . $value;
                            }
                        }
                    }
                }

                if($productIds === '') {
                    $productIds = "null";
                }

                $data = [
                    "metafield" => [
                        "id" => $wishlistDecoded['metafields'][0]['id'],
                        "value" => '' . $productIds . '',
                        "value_type" => 'string',
                    ]
                ];

                try {
                    $client = HttpClient::create();
                    $response = $client->request('PUT', shopifyApiurl . 'metafields/'.$wishlistDecoded['metafields'][0]['id'].'.json', ['json' => $data]);
                } catch (\Exception $e) {
                    var_dump($e);
                }
            }
        }

        return new Response('wishlist saved');
    }


    function replaceSpecialCharacters($string) {

        return str_replace(array('&', '-', '\t', '"', '  ', '/', '+', "'"), array('%26', '%2D', '', '', ' ', '%2F', '%2B', ''), $string);
    }

}
