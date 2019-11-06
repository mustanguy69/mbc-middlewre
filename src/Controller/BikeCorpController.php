<?php

namespace App\Controller;

use App\Controller\RexSoapController;
use App\Controller\ShopifyController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;


const bikeCorpApiuUrl = "http://www.bikecorp.com.au/documents/ProductFeed734430f-f300-45b6-964c-3f7d79c9c975.xml";

class BikeCorpController extends AbstractController
{
    /**
     * Call Soap REX for getting products
     * @Route("/addUpdateProductsBikeCorp")
     */
    public function addUpdateProduct(RexToBddController $rexToBddController) {

        $bikeCorpXml = simplexml_load_file(bikeCorpApiuUrl);
        $today = new \DateTime();
        $today = $today->format('Y-m-d H:i:s');
        $matchingForRex = "";
        $brands = [];
        foreach ($bikeCorpXml[0] as $product) {
            if ($product->brand != '') {
                $brands[] = $product->brand;
            }

            foreach ($product->categories as $categories) {
                if (isset($categories->level1) && !isset($categories->level2)) {

                    switch ($categories->level1) {
                        case "Parts and Accessories":
                            $category = "Accessories";
                            break;
                        case "Rubber/Sealant/Protection":
                            $category = "Tyres & Tubes";
                            break;
                        case "Bikes/Trikes/Scooters":
                            $category = "Bikes";
                            break;
                        case "Clothing":
                            $category = "Clothing";
                            break;
                        case "Helmets":
                            $category = "Bike Helmets";
                            break;
                    }
                } else {
                    switch ($categories->level2) {
                        case "Computers":
                            $category = "Cycle Computers";
                            break;
                        case "Sealant":
                        case "Slugplug":
                            $category = "Tubeless Accessories";
                            break;
                        case "Tools":
                        case "Bolts/Nuts/Screws":
                            $category = "Workshop Tools";
                            break;
                        case "Display/Storage Stands":
                        case "Stands":
                            $category = "Workstands";
                            break;
                        case "Lights":
                            $category = "Bicycle Lighting";
                            break;
                        case "Cranks":
                            $category = "Cranks Set";
                            break;
                        case "Triad":
                        case "Oath":
                        case "Scooters":
                            $category = "Scooters";
                            break;
                        case "Locks":
                            $category = "Bike Locks & Security";
                            break;
                        case "Car Racks":
                            $category = "Car Bike Racks";
                            break;
                        case "Knicks":
                            $category = "Cycling Shorts";
                            break;
                        case "GPS Units":
                            $category = "GPS Cycle Computers";
                            break;
                        case "Inflation":
                            $category = "Inflation";
                            break;
                        case "Bottles/Cages":
                            $category = "Bike Bottles/Cages";
                            break;
                        case "MTB Shorts":
                            $category = "MTB Shorts";
                            break;
                        case "Warmers":
                            $category = "Arm, Leg And Knee Warmers";
                            break;
                        case "Bike Covers":
                            $category = "Bike Frame Protectors";
                            break;
                        case "Bike Case":
                        case "Handlebar Bags":
                        case "Bags":
                            $category = "Bike Bags";
                            break;
                        case "Baskets":
                        case "Pannier Bags":
                            $category = "Pannier Bags";
                            break;
                        case "Jackets":
                            $category = "Cycling Jackets";
                            break;
                        case "Grips":
                            $category = "MTB Handlebar Grips";
                            break;
                        case "Gloves":
                            $category = "Cycling Gloves & Mitts";
                            break;
                        case "Helmets":
                            $category = "Bike Helmets";
                            break;
                        case "Head Sets":
                            $category = "Headsets";
                            break;
                        case "Mirrors":
                            $category = "Bicycle Mirrors";
                            break;
                        case "Mudguards":
                            $category = "Bicycle Mudguards";
                            break;
                        case "Brake Pads":
                        case "Disc Brake Pads":
                        case "Truck Pads":
                            $category = "Brake & Disc Pads";
                            break;
                        case "Pedals":
                        case "Toe ClipsStraps":
                            $category = "Pedals & Cleats";
                            break;
                        case "Handlebar Tape":
                            $category = "Road Bike Handlebar Tape";
                            break;
                        case "Rim Tapes":
                            $category = "Rim Tape";
                            break;
                        case "Head Stems":
                            $category = "Stems";
                            break;
                        case "Saddle Covers":
                        case "Saddles":
                            $category = "Saddles";
                            break;
                        case "Tyre Liners":
                        case "Valve Caps":
                        case "Valve Core Removers":
                            $category = "Bike Tyre Accessories";
                            break;
                        case "Training Wheels/Handles":
                        case "Trainers":
                            $category = "Training";
                            break;
                        case "Saddle Bags":
                            $category = "Bike Saddle & Frame Packs";
                            break;
                        case "Bearings":
                            $category = "Hubs";
                            break;
                        case "Bottom Brackets":
                            $category = "Bottom Brackets & Cups";
                            break;
                        case "Cables/Accessories":
                            $category = "Brake Cables";
                            break;
                        case "Bells/Horns":
                            $category = "Bicycle Bells & Horns";
                            break;
                        case "Brakes":
                            $category = "Bicycle Braking";
                            break;
                        case "Hangers":
                            $category = "Gear Hangers";
                            break;
                        case "Baby Seats":
                            $category = "Child Bike Seats";
                            break;
                        case "Bar Ends":
                            $category = "MTB Bar Ends";
                            break;
                        case "Brake Levers":
                            $category = "Brake Levers";
                            break;
                        case "Pegs":
                        case "BMX Components":
                            $category = "BMX";
                            break;
                        case "Toys":
                            $category = "Toys";
                            break;
                        case "Carriers":
                            $category = "Pannier Racks";
                            break;
                        case "Chain Links":
                        case "Chains":
                            $category = "Chains & Chain Accessories";
                            break;
                        case "Spoke Protectors":
                        case "Spokes":
                            $category = "Wheel Spokes & Nipples";
                            break;
                        case "Cassettes/Freewheels":
                            $category = "Cassettes & Sprockets";
                            break;
                        case "Shifters":
                            $category = "Shifters";
                            break;
                        case "Kids Items":
                        case "Phone and Device Mounts":
                        case "Streamers":
                            $category = "Bike Gadgets";
                            break;
                        case "Derailleurs":
                            $category = "Derailleurs";
                            break;
                        case "Tyres":
                            $category = "Bike Tyres";
                            break;
                        case "Tubes":
                            $category = "Inner Tubes";
                            break;
                        case "Handlebars":
                            $category = "Handlebars";
                            break;
                        case "Spacers":
                            $category = "Headset Spares";
                            break;
                        case "Backpack":
                            $category = "Rucksacks & Holdalls";
                            break;
                        case "Bike Maintenance":
                            $category = "Bike Tools & Maintenance";
                            break;
                        case "Frame Protection":
                            $category = "Bike Frame Protection";
                            break;
                        case "Shoe Covers":
                            $category = "Overshoes & Booties";
                            break;
                        case "Socks":
                            $category = "Cycling Socks";
                            break;
                        case "Anti Chafe Cream":
                            $category = "Embrocation & Chamois Creme";
                            break;
                        case "Balance Bikes":
                            $category = "Balance Bikes";
                            break;
                        case "Puncture repair":
                            $category = "Puncture repair";
                            break;
                        case "Trailers/Tagalongs":
                            $category = "Child Bike Seats & Trailers";
                            break;
                        case "Axles":
                            $category = "Bike Wheel Skewers";
                            break;
                        case "Lubricant":
                        case "Carbon Paste":
                            $category = "Bike Lubrication & Fluids";
                            break;
                        case "Safety Items":
                            $category = "High Visibility Safety Clothing";
                            break;
                        case "Trouser Bands":
                            $category = "Cycling Footwear";
                            break;
                        case "Shogun":
                            $category = "unknown";
                            break;
                        case "Seat Clamps":
                            $category = "Seatpost Collars";
                            break;
                        case "Seat Pillars":
                            $category = "Seatposts";
                            break;
                        case "Unicycles":
                            $category = "unknown";
                            break;
                        case "Wheels":
                            $category = "Wheels";
                            break;
                    }
                }
            }
            $supplier = "Bike Corp 2";
            $supplierCode = "BC2";


            $matchingForRex .= "
            <Product>
                <ShortDescription>".$this->xmlEscape($product->description)."</ShortDescription>
                <SupplierSKU>".$this->xmlEscape($product->part)."</SupplierSKU>
                <ManufacturerSKU>".$this->xmlEscape($product->gtin)."</ManufacturerSKU>
                <ProductType>".$this->xmlEscape($category)."</ProductType>
                <Supplier>".$this->xmlEscape($supplier)."</Supplier>
                <SupplierCode>".$this->xmlEscape($supplierCode)."</SupplierCode>
                <Brand>".$this->xmlEscape($product->brand)."</Brand>
                <SupplierBuy>".$this->adjustDecimal($product->yourprice)."</SupplierBuy>
                <POSPrice>".$this->adjustDecimal($product->listprice)."</POSPrice>
                <WebPrice>".$this->adjustDecimal($product->listprice)."</WebPrice>
                <LastUpdated>".$this->xmlEscape($today)."</LastUpdated>
            </Product>";
        }
        $brands = array_unique($brands);
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

        $bikeCorpXml = simplexml_load_file(bikeCorpApiuUrl);
        $matchingForRex = "";

        foreach ($bikeCorpXml[0] as $product) {

            try {
                $productId = (new ShopifyController())->getProductIdByTitle($product->description);
                $shopifyStockUpdate = (new ShopifyController())->setMetafieldProductSupplierStock($productId, (int)$product->availability);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }


            $matchingForRex .= "
            <Product>
                <SupplierSKU>".$this->xmlEscape($product->part)."</SupplierSKU>
                <Qty>".round($product->availability, 0)."</Qty>
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
