<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RexSoapController;
use Psr\Log\LoggerInterface;



const shopifyImageApiurl = "https://4bcd1fe009483d29119dd1af3e4b320c:c4d1b483afde579d19810953287548da@bikes-com-au.myshopify.com/admin/api/2019-10/products/";

class AddUpdateImagesShopifyController extends AbstractController
{

    /**
     * Call Shopify API for add/update image products
     * @Route("/addUpdateImage")
     * @param $productId
     * @param $image
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function addUpdateImage($productId, $image) {
        $data = [
            'image' => [
                'src' =>  $image,
            ]
        ];

        try {
            $client = HttpClient::create();
            $response = $client->request('POST', shopifyImageApiurl . $productId . '/images.json', ['json' => $data]);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return json_decode($response->getContent());

    }

}
