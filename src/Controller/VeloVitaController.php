<?php

namespace App\Controller;

use App\Controller\RexSoapController;
use App\Controller\ShopifyController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

//@todo add current date
const veloVitaApiuUrl = "https://www.dropbox.com/sh/e6huk2dzhwkf7rf/AABwmD9zBx6TIUZ6iNq8eKbwa/VV_StockMaster%2028.10.19.txt?dl=1";

class VeloVitaController extends AbstractController
{
    /**
     * Call Soap REX for getting products
     * @Route("/addUpdateProductsVeloVita")
     */
    public function addUpdateProduct(RexToBddController $rexToBddController) {

        $veloVitaTxt = file_get_contents( veloVitaApiuUrl);
        $veloVitaArrayBefore = explode( PHP_EOL, $veloVitaTxt);
        $iZero = array_values(explode('|', $veloVitaArrayBefore[0]));

        foreach ($veloVitaArrayBefore as $value) {
            if (substr_count($value, '|') == 14) {
                $explodedArray = explode('|', $value);
                $veloVitaArray[] = array_combine( $iZero, $explodedArray );
            }
        }
        unset($veloVitaArray[0]);

        $today = new \DateTime();
        $today = $today->format('Y-m-d H:i:s');
        $matchingForRex = "";
        $brands = [];
        foreach ($veloVitaArray as $product) {
            if ($product['BRAND'] != '') {
                $brands[] = $product['BRAND'];
            }

            $supplier = "Velo Vita";
            $supplierCode = "VV";

            $matchingForRex .= "
            <Product>
                <ShortDescription>".$this->xmlEscape($product['Description'])."</ShortDescription>
                <SupplierSKU>".$this->xmlEscape($product['Model No'])."</SupplierSKU>
                <ManufacturerSKU>".$this->xmlEscape($product['EAN'])."</ManufacturerSKU>
                <ProductType>".$this->xmlEscape($category)."</ProductType>
                <Supplier>".$this->xmlEscape($supplier)."</Supplier>
                <SupplierCode>".$this->xmlEscape($supplierCode)."</SupplierCode>
                <Brand>".$this->xmlEscape($product['BRAND'])."</Brand>
                <SupplierBuy>".$this->adjustDecimal($product['WSALE'])."</SupplierBuy>
                <POSPrice>".$this->adjustDecimal($product['F_RETAIL'])."</POSPrice>
                <WebPrice>".$this->adjustDecimal($product['F_RETAIL'])."</WebPrice>
                <LastUpdated>".$this->xmlEscape($today)."</LastUpdated>
            </Product>";
        }

        (new RexSoapController)->addBrands($brands);
        $rexToBddController->rexToBddBrands($brands);
        $rexRequest = (new RexSoapController)->addProducts($matchingForRex);
        $rexRequest = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $rexRequest->getContent());
        $xml = new \SimpleXMLElement($rexRequest);
        $body = $xml->xpath('//soapBody')[0];
        $array = json_decode(json_encode((array)$body), TRUE);

        return $array['SaveProductsResponse']['SaveProductsResult']['Response'];
    }

    function adjustStocksProduct() {

        $veloVitaTxt = file_get_contents( veloVitaApiuUrl);
        $veloVitaArrayBefore = explode( PHP_EOL, $veloVitaTxt);
        $iZero = array_values(explode('|', $veloVitaArrayBefore[0]));

        foreach ($veloVitaArrayBefore as $value) {
            if (substr_count($value, '|') == 14) {
                $explodedArray = explode('|', $value);
                $veloVitaArray[] = array_combine( $iZero, $explodedArray );
            }
        }
        unset($veloVitaArray[0]);

        $matchingForRex = "";
        foreach ($veloVitaArray as $product) {
            $stock = 0;
            switch ($product['STOCKQTY']) {
                case "None":
                    $stock = 0;
                    break;
                case "Low":
                    $stock = 10;
                    break;
                case "Good":
                    $stock = 20;
                    break;
            }

            try {
                $productId = (new ShopifyController())->getProductIdByTitle($product['Model No']);
                $shopifyStockUpdate = (new ShopifyController())->setMetafieldProductSupplierStock($productId, (int)$stock);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }

            $matchingForRex .= "
            <Product>
                <SupplierSKU>".$this->xmlEscape($product['Model No'])."</SupplierSKU>
                <Qty>".$stock."</Qty>
            </Product>";
        }


        $rexRequest = (new RexSoapController)->adjustStocks(trim($matchingForRex));
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
        if (strpos( $value, "$" ) !== false) {
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
