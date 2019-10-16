<?php

namespace App\Controller;

use SoapFault;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\AnotherSoapClient;

const clientId = "9bf6dd42-35b9-46dd-948a-1c3c91906caa";
const userName = "wsi";
const password = "wsipass";
const soapHead = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ret="http://retailexpress.com.au/"><soapenv:Header><ret:ClientHeader><ret:ClientID>'.clientId.'</ret:ClientID><ret:UserName>'.userName.'</ret:UserName><ret:Password>'.password.'</ret:Password></ret:ClientHeader></soapenv:Header><soapenv:Body>';
const soapFoot = '</soapenv:Body></soapenv:Envelope>';


class RexSoapController extends AbstractController
{

    public function getSoapClient() {
        try {
            $soapClient = new anotherSoapClient('http://v2wsisandbox.retailexpress.com.au/dotnet/admin/webservices/v2/inventoryplanning/inventoryplanningservice.asmx?WSDL', array(
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace' => 1,
                'exceptions' => true,
            ));
        } catch (SoapFault $e) {
            var_dump($e);
        }

        return $soapClient;
    }

    /**
     * Call Soap REX for getting products
     * @Route("/getProducts", defaults={"_format"="text/xml"})
     */
    public function getProducts()
    {
        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:GetProducts/>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('GetProducts', $xmlRequest);
            $xmlRequest     = new \SimpleXMLElement($request);
            $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->GetProductsResponse;
            $result = (string) $response->GetProductsResult;

            $requestDecoded = base64_decode($result);
            $products = gzdecode($requestDecoded);

        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($products);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }

    /**
     * Call Soap REX for getting products detailed
     * @Route("/getProductsDetailed", defaults={"_format"="text/xml"})
     */
    public function getProductsDetailed()
    {
        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:GetProductsDetailed><ret:lastUpdated>2019-06-25T12:00:00.000Z</ret:lastUpdated></ret:GetProductsDetailed>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('GetProductsDetailed', $xmlRequest);
            $xmlRequest     = new \SimpleXMLElement($request);
            $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->GetProductsDetailedResponse;
            $result = (string) $response->GetProductsDetailedResult;

            $requestDecoded = base64_decode($result);
            $products = gzdecode($requestDecoded);

        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($products);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }

    /**
     * Call Soap REX for getting stock
     * @Route("/getStock", defaults={"_format"="text/xml"})
     */
    public function getStock()
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:GetStock></ret:GetStock>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('GetStock', $xmlRequest);
            $xmlRequest     = new \SimpleXMLElement($request);
            $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->GetStockResponse;
            $result = (string) $response->GetStockResult;

            $requestDecoded = base64_decode($result);
            $stock = gzdecode($requestDecoded);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($stock);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }

    /**
     * Call Soap REX for adding products
     * @Route("/addProducts", defaults={"_format"="text/xml"})
     */
    public function addProducts()
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:SaveProducts xmlns="http://retailexpress.com.au/">
            <ret:productsXml>
                <![CDATA[
                    <Product>
                        <ProductID>89898989</ProductID>
                        <SupplierSKU>MPC24</SupplierSKU>
                        <ShortDescription>Tyre for road bikes 2</ShortDescription>
                        <SupplierCode>S1</SupplierCode>
                        <ProductType>T-shirt</ProductType>
                        <Weight>14.00</Weight>
                        <Type>Tyres - Road -Race</Type>
                    </Product>
                ]]>
            </ret:productsXml>
        </ret:SaveProducts >' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProducts', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($request);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }

    /**
     * Test
     */
    public function testCommand()
    {

        return new Response("YOLO");

    }


}
