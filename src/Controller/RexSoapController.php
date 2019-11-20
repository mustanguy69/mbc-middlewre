<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Entity\ProductColors;
use App\Entity\Products;
use App\Entity\ProductSizes;
use App\Entity\ProductTypes;
use App\Entity\Suppliers;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\anotherSoapClient;

const clientId = "e1ca82cd-e94c-4e1b-a271-1897a0ec0e8f";
const userName = "777";
const password = "tanguy777";
const soapHead = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ret="http://retailexpress.com.au/"><soapenv:Header><ret:ClientHeader><ret:ClientID>'.clientId.'</ret:ClientID><ret:UserName>'.userName.'</ret:UserName><ret:Password>'.password.'</ret:Password></ret:ClientHeader></soapenv:Header><soapenv:Body>';
const soapFoot = '</soapenv:Body></soapenv:Envelope>';


class RexSoapController extends AbstractController
{

    public function getSoapClient() {
        try {
            $soapClient = new anotherSoapClient('http://trainingmelbournebicyclecentre.retailexpress.com.au/dotnet/admin/webservices/v2/inventoryplanning/inventoryplanningservice.asmx?WSDL', array(
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

        $xmlRequest = soapHead . '<ret:GetProductsDetailed><ret:lastUpdated>2019-10-29T12:00:00.000Z</ret:lastUpdated></ret:GetProductsDetailed>' . soapFoot;

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
     * Call Soap REX for getting suppliers
     * @Route("/getSuppliers", name="getSuppliers")
     */
    public function getSuppliers()
    {
        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:GetSuppliers></ret:GetSuppliers>' . soapFoot;

        try {
//            $request = $soapClient->__anotherRequest('GetSuppliers', $xmlRequest);
            $xml = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
   <soap:Body>
      <GetSuppliersResponse xmlns="http://retailexpress.com.au/">
         <GetSuppliersResult>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</GetSuppliersResult>
      </GetSuppliersResponse>
   </soap:Body>
</soap:Envelope>';
            $xmlRequest     = new \SimpleXMLElement($xml);
            $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->GetSuppliersResponse;
            $result = (string) $response->GetSuppliersResult;

            $requestDecoded = base64_decode($result);
            $suppliers = gzdecode($requestDecoded);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($suppliers);
        $response->headers->set('Content-Type', 'text/xml');

        return $response->getContent();

    }

    /**
     * Call Soap REX for adding products
     * @Route("/addProducts", defaults={"_format"="text/xml"})
     * @param  $products
     * @return Response
     * @throws \Exception
     */
    public function addProducts($products)
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:SaveProducts xmlns="http://retailexpress.com.au/">
            <ret:productsXml>
                <![CDATA[
                <Products>'.$products.'</Products>
                ]]>
            </ret:productsXml>
        </ret:SaveProducts>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProducts', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        return $request;

    }

    /**
     * Call Soap REX for adjust supplier stocks
     * @Route("/adjustStocksSupplier", defaults={"_format"="text/xml"})
     * @param  $products
     * @return Response
     */
    public function adjustStocksSupplier($products)
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:SaveProductOutletDetails>
            <ret:productsXml>
                <![CDATA[
                <Products>'.$products.'</Products>
                ]]>
            </ret:productsXml>
            <ret:outletRef>3</ret:outletRef> 
        </ret:SaveProductOutletDetails>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProductOutletDetails', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        return $request;

    }

    /**
     * Call Soap REX for adjust in-shop stocks
     * @Route("/adjustStocksInShop", defaults={"_format"="text/xml"})
     * @param  $products
     * @return Response
     */
    public function adjustStocksInShop($products)
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:SaveProductOutletDetails>
            <ret:productsXml>
                <![CDATA[
                <Products>'.$products.'</Products>
                ]]>
            </ret:productsXml>
            <ret:outletRef>1</ret:outletRef> 
        </ret:SaveProductOutletDetails>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProductOutletDetails', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        return $request;

    }


    /**
     * Call Soap REX for adding Brands
     * @Route("/addBrands", defaults={"_format"="text/xml"})
     * @param  $brands
     * @return Response
     */
    public function addBrands($brands)
    {

        $soapClient = $this->getSoapClient();

        $xmlBrand = "";
        foreach ($brands as $brand) {
            $xmlBrand .= "<Attribute><Type>Brand</Type><Text>".$brand."</Text></Attribute>";
        }

        $xmlRequest = soapHead . '<ret:SaveProductAttributes>
            <ret:attributesXml>
                <![CDATA[
                <Attributes>'.$xmlBrand.'</Attributes>
                ]]>
            </ret:attributesXml>
        </ret:SaveProductAttributes>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProductAttributes', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        return $request;

    }

    /**
     * Call Soap REX for adding Attributes
     * @Route("/addUpdateAttributeRex", defaults={"_format"="text/xml"})
     * @param  $attribute
     * @return Response
     */
    public function addUpdateAttributes($attributeXml)
    {

        $soapClient = $this->getSoapClient();

        $xmlRequest = soapHead . '<ret:SaveProductAttributes>
            <ret:attributesXml>
                <![CDATA[
                <Attributes>'.$attributeXml.'</Attributes>
                ]]>
            </ret:attributesXml>
        </ret:SaveProductAttributes>' . soapFoot;

        try {
            $request = $soapClient->__anotherRequest('SaveProductAttributes', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        return $request;

    }

}
