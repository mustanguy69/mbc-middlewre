<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RexSoapController;
use Psr\Log\LoggerInterface;


const url = "http://www.bikecorp.com.au/documents/ProductFeed734430f-f300-45b6-964c-3f7d79c9c975.xml";
const supplier = "BikeCorp";
const supplierCode = "BC";

class BikeCorpController extends AbstractController
{
    /**
     * Call Soap REX for getting products
     * @Route("/getNightlyExportXml")
     */
    public function addUpdateProduct() {

        $bikeCorpXml = simplexml_load_file(url);
        $today = new \DateTime();
        $today = $today->format('Y-m-d H:i:s');
        $matchingForRex = "";
        foreach ($bikeCorpXml[0] as $product) {
            foreach ($product->categories as $category) {
                $level1 = $category->level1;
                $level2 = $category->level2;
                $level3 = $category->level3;
                $level4 = $category->level4;
            }
            $matchingForRex .= "
            <Product>
                <ShortDescription>".$this->xmlEscape($product->description)."</ShortDescription>
                <SupplierSKU>".$this->xmlEscape($product->part)."</SupplierSKU>
                <ManufacturerSKU>".$this->xmlEscape($product->gtin)."</ManufacturerSKU>
                <ProductType>".$this->xmlEscape($level1)."</ProductType>
                <Supplier>".$this->xmlEscape(supplier)."</Supplier>
                <SupplierCode>".$this->xmlEscape(supplierCode)."</SupplierCode>
                <sBrand>".$this->xmlEscape($product->brand)."</sBrand>
                <SupplierBuy>".$this->adjustDecimal($product->yourprice)."</SupplierBuy>
                <PricePOS>".$this->adjustDecimal($product->listprice)."</PricePOS>
                <WebPrice>".$this->adjustDecimal($product->listprice)."</WebPrice>
                <LastUpdated>".$this->xmlEscape($today)."</LastUpdated>
            </Product>";
        }

        $rexRequest = (new RexSoapController)->addProducts($matchingForRex);
        $rexRequest = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $rexRequest->getContent());
        $xml = new \SimpleXMLElement($rexRequest);
        $body = $xml->xpath('//soapBody')[0];
        $array = json_decode(json_encode((array)$body), TRUE);

        return $array['SaveProductsResponse']['SaveProductsResult']['Response'];
    }

    function adjustStocksProduct() {

        $bikeCorpXml = simplexml_load_file(url);
        $matchingForRex = "";
        foreach ($bikeCorpXml[0] as $product) {
            $matchingForRex .= "
            <Product>
                <SupplierSKU>".$this->xmlEscape($product->part)."</SupplierSKU>
                <Qty>".$this->xmlEscape($product->availability)."</Qty>
            </Product>";
        }

        $rexRequest = (new RexSoapController)->adjustStocks($matchingForRex);
        $rexRequest = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $rexRequest->getContent());
        $xml = new \SimpleXMLElement($rexRequest);
        $body = $xml->xpath('//soapBody')[0];
        $array = json_decode(json_encode((array)$body), TRUE);

        return $array['SaveProductOutletDetailsResponse']['SaveProductOutletDetailsResult']['Response'];
    }

    function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"', '”', '$'), array('&amp;', '&lt;', '&gt;', '', '', '', ''), $string);
    }

    function adjustDecimal($value) {
        if (strpos( $value, "$" ) === true) {
            if ( strpos( $value, "." ) === false ) {
                $value = $value . ".00";
            } elseif (strlen($value) - strrpos($value, '.') - 1 == 1) {
                $value = $value . "0";
            }
        } else {
            $value = '';
        }

        return $value;
    }
}
