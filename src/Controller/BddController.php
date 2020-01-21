<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Entity\Images;
use App\Entity\ProductColors;
use App\Entity\Products;
use App\Entity\ProductSizes;
use App\Entity\ProductTypes;
use App\Entity\Suppliers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Utils\UploadedBase64File;
use App\Utils\Base64FileExtractor;

class BddController extends AbstractController
{

    public $em2;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em2 = $entityManager;
    }

    /**
     * Homepage
     * @Route("/addProductBdd", name="addProductBdd")
     *
     */
    public function addProductBdd(Request $request)
    {
        // get request data
        $title = $request->request->get('title');
        $sku = $request->request->get('sku');
        $supplier = $request->request->get('supplier');
        $brand = $request->request->get('brand');
        $description = $request->request->get('description');
        $type = $request->request->get('product-type');
        $tags = $request->request->get('tags');
        $price = $request->request->get('price');
        $compare = $request->request->get('compare');
        $barcode = $request->request->get('barcode');
        $supplierStock = $request->request->get('supplier-stock');
        $stock = $request->request->get('stock');
        $weight = $request->request->get('weight');
        $length = $request->request->get('length');
        $season = $request->request->get('season');
        $size = $request->request->get('size');
        $color = $request->request->get('color');
        $women = $request->request->get('women');
        $men = $request->request->get('men');
        $boys = $request->request->get('boys');
        $girls = $request->request->get('girls');
        $unisex = $request->request->get('unisex');
        $shopify = $request->request->get('shopify');
        if($shopify === null) {
            $shopify = 0;
        }

        $base64Image = $request->request->get('base64Image');
        $base64ImageName = $request->request->get('base64ImageName');

        // get entity manager
        $em = $this->em2;

        // get entity object with request data
        $brand = $em->getRepository('App:Brands')->find($brand);
        $supplier = $em->getRepository('App:Suppliers')->find($supplier);
        $type = $em->getRepository('App:ProductTypes')->find($type);
        $size = $em->getRepository('App:ProductSizes')->find($size);
        $color = $em->getRepository('App:ProductColors')->find($color);

        // get today and format it
        $today = new \DateTime();
        $todayFormated = $today->format('Y-m-d H:i:s');

        // create object and set data
        $product = new Products();
        $product->setTitle($this->xmlEscape($title))
            ->setSku($this->xmlEscape($sku))
            ->setSupplier($supplier)
            ->setBrand($brand)
            ->setDescription($description)
            ->setType($type)
            ->setTags($tags)
            ->setPrice($price)
            ->setCompare($compare)
            ->setBarcode($this->xmlEscape($barcode))
            ->setSupplierStock($supplierStock)
            ->setStock($stock)
            ->setWeight($weight)
            ->setLength($length)
            ->setSeason($season)
            ->setSize($size)
            ->setColor($color)
            ->setWomen($women)
            ->setMen($men)
            ->setBoys($boys)
            ->setGirls($girls)
            ->setUnisex($unisex)
            ->setToShopify($shopify)
            ->setUpdatedAt($today)
            ->setMassUploadedRex(false)
            ->setSyncWithShopify(false);

        // if image/s and set it to product
        if ($base64Image) {
            $images = array_combine($base64ImageName, $base64Image);
            foreach ($images as $key => $image) {
                if (!base64_encode(base64_decode($image, true)) === $image){
                    $image = base64_encode(file_get_contents($image));
                } else {
                    $imageObj = new Images();
                    $imageObj->setSrc($image);
                    $imageObj->setName($key);
                    $em->persist($imageObj);

                    $product->addImage($imageObj);
                }
            }
        }

        // save product
        $em->persist($product);
        $em->flush();

        // get name of color for setting it to REX
        if($product->getColor()) {
            $productColor = $product->getColor()->getName();
        } else {
            $productColor = "";
        }

        // get name of size for setting it to REX
        if($product->getSize()) {
            $productSize = $product->getSize()->getSize();
        } else {
            $productSize = "";
        }

        // create xml for REX
        $products = "
        <Product>
            <ShortDescription>".$this->xmlEscape($product->getTitle())."</ShortDescription>
            <SupplierSKU>".$this->xmlEscape($product->getSku())."</SupplierSKU>
            <ManufacturerSKU>".$this->xmlEscape($product->getBarcode())."</ManufacturerSKU>
            <ProductType>".$this->xmlEscape($product->getType()->getName())."</ProductType>
            <Supplier>".$this->xmlEscape($product->getSupplier()->getName())."</Supplier>
            <SupplierCode>".$this->xmlEscape($product->getSupplier()->getCode())."</SupplierCode>
            <Brand>".$this->xmlEscape($product->getBrand()->getName())."</Brand>
            <POSPrice>$".$this->adjustDecimal($product->getPrice())."</POSPrice>
            <WebPrice>$".$this->adjustDecimal($product->getPrice())."</WebPrice>
            <Size>".$this->xmlEscape($productSize)."</Size>
            <Weight>".$this->xmlEscape($product->getWeight())."</Weight>
            <Length>".$this->xmlEscape($product->getLength())."</Length>
            <Colour>".$this->xmlEscape($productColor)."</Colour>
            <Season>".$this->xmlEscape($product->getSeason())."</Season>
            <LastUpdated>".$todayFormated."</LastUpdated>
        </Product>";

        // call REX controller to send product
        $request = (new RexSoapController())->addProducts($products);
        $xmlRequest     = new \SimpleXMLElement($request);
        $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->SaveProductsResponse;
        $status = (string) $response->SaveProductsResult->Response->Status;

        // update stock for supplier
        $stockSupplier = "
        <Product>
            <SupplierSKU>".$product->getSku()."</SupplierSKU>
            <Qty>".round($product->getSupplierStock(), 0)."</Qty>
        </Product>";
        $requestAdjustStockSupplier = (new RexSoapController())->adjustStocksSupplier($stockSupplier);

        // update stock for in shop
        $stockInShop = "
        <Product>
            <SupplierSKU>".$product->getSku()."</SupplierSKU>
            <Qty>".round($product->getStock(), 0)."</Qty>
        </Product>";
        $requestAdjustStockInShop = (new RexSoapController())->adjustStocksInShop($stockInShop);

        return new Response($status);

    }

    /**
     * update shopify + rex products page
     * @Route("product/update", name="updateProductBdd")
     *
     */
    public function updateProductPage(Request $request, ValidatorInterface $validator, Base64FileExtractor $base64FileExtractor) {

        // get doctrine manager
        $em = $this->getDoctrine()->getManager();

        // get product id by request to get the product
        $id = $request->request->get('product-id');

        // get product by ID
        $product = $em->getRepository('App:Products')->find($id);

        // get form data by request
        $title = $request->request->get('title');
        $sku = $request->request->get('sku');
        $supplier = $request->request->get('supplier');
        $brand = $request->request->get('brand');
        $description = $request->request->get('description');
        $type = $request->request->get('product-type');
        $tagsInput = $request->request->get('tags');
        $price = $request->request->get('price');
        $compare = $request->request->get('compare');
        $barcode = $request->request->get('barcode');
        $supplierStock = $request->request->get('supplier-stock');
        $stock = $request->request->get('stock');
        $weight = $request->request->get('weight');
        $length = $request->request->get('length');
        $season = $request->request->get('season');
        $size = $request->request->get('size');
        $color = $request->request->get('color');
        $women = $request->request->get('women');
        $men = $request->request->get('men');
        $boys = $request->request->get('boys');
        $girls = $request->request->get('girls');
        $unisex = $request->request->get('unisex');
        $shopify = $request->request->get('shopify');
        if($shopify === null) {
            $shopify = 0;
        }

        $base64Image = $request->request->get('base64Image');
        $base64ImageName = $request->request->get('base64ImageName');
        $base64ImageUploaded = $request->request->get('base64ImageUploaded');
        $base64ImageNameUploaded = $request->request->get('base64ImageNameUploaded');

        // get entity object with request data
        $brandObj = $em->getRepository('App:Brands')->find($brand);
        $supplierObj = $em->getRepository('App:Suppliers')->find($supplier);
        $typeObj = $em->getRepository('App:ProductTypes')->find($type);
        $sizeObj = $em->getRepository('App:ProductSizes')->find($size);
        $colorObj = $em->getRepository('App:ProductColors')->find($color);

        // get today and format it
        $today = new \DateTime();
        $todayFormated = $today->format('Y-m-d H:i:s');

        // update object and set data
        $product->setTitle($this->xmlEscape($title))
            ->setSku($this->xmlEscape($sku))
            ->setSupplier($supplierObj)
            ->setBrand($brandObj)
            ->setDescription($description)
            ->setType($typeObj)
            ->setPrice($price)
            ->setCompare($compare)
            ->setBarcode($this->xmlEscape($barcode))
            ->setSupplierStock($supplierStock)
            ->setStock($stock)
            ->setWeight($weight)
            ->setLength($length)
            ->setSeason($season)
            ->setSize($sizeObj)
            ->setColor($colorObj)
            ->setWomen($women)
            ->setMen($men)
            ->setBoys($boys)
            ->setGirls($girls)
            ->setUnisex($unisex)
            ->setToShopify($shopify)
            ->setUpdatedAt($today);

        // if image/s and set it to product
        if ($base64Image) {
            $images = array_combine($base64ImageName, $base64Image);
            foreach ($images as $key => $image) {
                if (!base64_encode(base64_decode($image, true)) === $image){
                    $image = base64_encode(file_get_contents($image));
                }
                $imageObj = new Images();
                $imageObj->setSrc($image);
                $imageObj->setName($key);
                $em->persist($imageObj);

                $product->addImage($imageObj);
                if ($product->getSyncWithShopify()) {
                    $productId = $product->getShopifyProductId();
                    $imgFile = preg_replace('#data:image/[^;]+;base64,#', '', $image);
                    $shopifyAddImageProductId = (new ShopifyController())->addUpdateImage($productId, $imgFile, $key);
                    $imageObj->setShopifyId($shopifyAddImageProductId);
                    $shopifyVariantAttach = (new ShopifyController())->attachImageWithVariant($product->getShopifyVariantId(), $imageObj->getShopifyId());
                    $shopifyAddImageProduct[] = $shopifyAddImageProductId;
                }
            }
        }

        $uploadedArray = [];
        if ($base64ImageUploaded) {
            foreach ($base64ImageUploaded as $uploaded) {
                $uploadedArray[] = $uploaded;
            }
        }

        $productImages = [];
        foreach ($product->getImages() as $image) {
            $productImages[] = "".$image->getId()."";
        }

        $diffs = array_diff($productImages, $uploadedArray);

        foreach ($diffs as $diff) {
            if($diff) {
                $image = $em->getRepository('App:Images')->find($diff);
                if($product->getSyncWithShopify()) {
                    (new ShopifyController())->removeImage($product->getShopifyProductId(), $image->getShopifyId());
                }
                $em->remove($image);
                $product->removeImage($image);
            }
        }

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            $status = 'error';
        } else {
            $status = 'success';
        }


        if($product->getColor()) {
            $productColor = $product->getColor()->getName();
        } else {
            $productColor = "CLEAR DATA";
        }

        if($product->getSize()) {
            $productSize = $product->getSize()->getSize();
        } else {
            $productSize = "CLEAR DATA";
        }

        if($product->getSeason()) {
            $productSeason = $product->getSeason();
        } else {
            $productSeason = "CLEAR DATA";
        }


        $allVariants = $em->getRepository('App:Products')->findBy(['barcode' => $product->getBarcode()]);
        $tags = array();
        foreach ($allVariants as $variant) {

            if($variant->getBrand()) {
                $productBrand = $variant->getBrand()->getName();
                array_push($tags, $productBrand);
            }
            if($variant->getColor()) {
                $productColor = $variant->getColor()->getName();
                array_push($tags, $productColor);
            }
            if($variant->getSize()) {
                $productSize = $variant->getSize()->getSize();
                array_push($tags, $productSize);
            }

            if($variant->getSeason()) {
                $productSeason = $variant->getSeason();
                array_push($tags, $productSeason);
            }

        }

        $tags = array_unique($tags);

        if($tagsInput !== '') {
            $tagsInputTab = explode(',', $tagsInput);
            $tagsToAdd = array_diff($tagsInputTab, $tags);
            $tagsToAdd = implode(',', $tagsToAdd);
            array_push($tags, $tagsToAdd);
        }

        $tags = array_filter($tags);
        $tags = implode(',', $tags);

        foreach ($allVariants as $variant) {
            $variant->setTags($tags);
        }


        // update product on REX
        $products = "
        <Product>
            <ShortDescription>".$this->xmlEscape($product->getTitle())."</ShortDescription>
            <SupplierSKU>".$this->xmlEscape($product->getSku())."</SupplierSKU>
            <ManufacturerSKU>".$this->xmlEscape($product->getBarcode())."</ManufacturerSKU>
            <ProductType>".$this->xmlEscape($product->getType()->getName())."</ProductType>
            <Supplier>".$this->xmlEscape($product->getSupplier()->getName())."</Supplier>
            <SupplierCode>".$this->xmlEscape($product->getSupplier()->getCode())."</SupplierCode>
            <Brand>".$this->xmlEscape($product->getBrand()->getName())."</Brand>
            <POSPrice>$".$this->adjustDecimal($product->getPrice())."</POSPrice>
            <WebPrice>$".$this->adjustDecimal($product->getPrice())."</WebPrice>
            <Size>".$this->xmlEscape($productSize)."</Size>
            <Weight>".$this->xmlEscape($product->getWeight())."</Weight>
            <Length>".$this->xmlEscape($product->getLength())."</Length>
            <Colour>".$this->xmlEscape($productColor)."</Colour>
            <Season>".$this->xmlEscape($productSeason)."</Season>
            <LastUpdated>".$todayFormated."</LastUpdated>
        </Product>";

        // call REX controller to send product
        $requestAddProduct = (new RexSoapController())->addProducts($products);
        $xmlRequest     = new \SimpleXMLElement($requestAddProduct);
        $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->SaveProductsResponse;
        $status = (string) $response->SaveProductsResult->Response->Status;

        // update stock for supplier
        $stockSupplier = "
        <Product>
            <SupplierSKU>".$product->getSku()."</SupplierSKU>
            <Qty>".round($product->getSupplierStock(), 0)."</Qty>
        </Product>";
        $requestAdjustStockSupplier = (new RexSoapController())->adjustStocksSupplier($stockSupplier);

        // update stock for in shop
        $stockInShop = "
        <Product>
            <SupplierSKU>".$product->getSku()."</SupplierSKU>
            <Qty>".round($product->getStock(), 0)."</Qty>
        </Product>";
        $requestAdjustStockInShop = (new RexSoapController())->adjustStocksInShop($stockInShop);

        // if product already synced with rex and shopify
        if ($product->getSyncWithShopify()) {
            $productId = $product->getShopifyProductId();
            if ($productId) {
                // create json product
                $jsonProduct = [
                    "product" => [
                        "id" => $productId,
                        "tags" => $tags,
                    ]
                ];

                if($product->getDescription() !== '') {
                    $jsonProduct['product']['body_html'] = $product->getDescription();
                }

                // call controller to send data to shopify
                $shopifyProductUpdate = (new ShopifyController())->updateProductShopify($productId, $jsonProduct);

                // get variants
                $variantId = $product->getShopifyVariantId();
                $variant = (new ShopifyController())->getProductVariantsShopify($variantId);
                $variantObj = json_decode($variant->getContent());

                // if there is variant, update it too
                if($variantObj->variant) {
                    if($variantObj->variant->sku === $product->getSku()) {
                        $jsonVariant = [
                            "variant" => [
                                "id" => $variantObj->variant->id,
                                "compare_at_price" => $product->getCompare(),
                                "metafield" => [
                                    "namespace" => "inventory",
                                    "key" => "supplier_stock",
                                    "value" => ''.(int)$product->getSupplierStock() . '',
                                    "value_type" => 'integer',
                                ]
                            ]
                        ];
                        if(!empty($shopifyAddImageProduct)) {
                            foreach ($shopifyAddImageProduct as $uploadedImage) {
                                $jsonVariant['variant']['image_id'] = $uploadedImage[0];
                            }
                        }

                        // save variant
                        $shopifyVariantUpdate = (new ShopifyController())->updateVariantShopify($variantObj->variant->id, $jsonVariant);
                    }
                } else {
                    $shopifyStockUpdate = (new ShopifyController())->setMetafieldProductSupplierStock($productId, $product->getSupplierStock());
                }
            }

        }

        $em->flush();

        return new Response($status);

    }

    /**
     * Add attribute to bdd and send to REX
     * @Route("/addUpdateAttributeBdd",  name="addUpdateAttributesBdd",defaults={"_format"="text/xml"})
     * @param  $attribute
     * @return Response
     */
    public function addUpdateAttributesBdd(Request $request)
    {
        $attributeType = $request->request->get('attributeType');
        $attributeId = $request->request->get('attributeId');
        $attributeXml = $request->request->get('attributeXml');
        $attributeName = $request->request->get('attributeName');
        $attributeCode = $request->request->get('attributeCode');
        $action = $request->request->get('action');

        $request = (new RexSoapController())->addUpdateAttributes($attributeXml);
        $xmlRequest = new \SimpleXMLElement($request);
        $response = $xmlRequest->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->SaveProductAttributesResponse;
        $id = (string) $response->SaveProductAttributesResult->Response->Details->Attributes->Attribute->AttributeID;

        $em = $this->getDoctrine()->getManager();
        if ($action === "create") {
            if($attributeType === 'brand-attribute') {
                $brand = new Brands();
                $brand->setId($id);
                $brand->setName($attributeName);

                $em->persist($brand);
            } elseif($attributeType === 'supplier-attribute') {
                $supplier = new Suppliers();
                $supplier->setId($id);
                $supplier->setName($attributeName);
                $supplier->setCode($attributeCode);

                $em->persist($supplier);
            } elseif($attributeType === 'type-attribute') {
                $type = new ProductTypes();
                $type->setId($id);
                $type->setName($attributeName);

                $em->persist($type);
            } elseif($attributeType === 'size-attribute') {
                $size = new ProductSizes();
                $size->setId($id);
                $size->setSize($attributeName);

                $em->persist($size);
            } elseif($attributeType === 'color-attribute') {
                $color = new ProductColors();
                $color->setId($id);
                $color->setName($attributeName);

                $em->persist($color);
            }
        } elseif ($action === "update") {
            if($attributeType === 'brand-attribute') {
                $brand = $em->getRepository('App:Brands')->find($attributeId);
                $brand->setName($attributeName);
            } elseif($attributeType === 'supplier-attribute') {
                $supplier = $em->getRepository('App:Suppliers')->find($attributeId);
                $supplier->setName($attributeName);
                $supplier->setCode($attributeCode);
            } elseif($attributeType === 'type-attribute') {
                $type = $em->getRepository('App:ProductTypes')->find($attributeId);
                $type->setName($attributeName);
            } elseif($attributeType === 'size-attribute') {
                $size = $em->getRepository('App:ProductSizes')->find($attributeId);
                $size->setSize($attributeName);
            } elseif($attributeType === 'color-attribute') {
                $color = $em->getRepository('App:ProductColors')->find($attributeId);
                $color->setName($attributeName);
            }
        }

        $em->flush();

        $response = new Response($request);

        return $response;

    }

    /**
     * Remove attribute from BDD
     * @Route("/removeAttributeBdd", defaults={"_format"="text/xml"})
     * @return Response
     */
    public function removeAttributeBdd(Request $request)
    {
        $attribute = $request->request->get('attribute');
        $attributeId = $request->request->get('id');

        $em = $this->getDoctrine()->getManager();

        if($attribute === 'brand-attribute') {
            $brand = $em->getRepository('App:Brands')->find($attributeId);
            $em->remove($brand);
        } elseif($attribute === 'supplier-attribute') {
            $supplier = $em->getRepository('App:Suppliers')->find($attributeId);
            $em->remove($supplier);
        } elseif($attribute === 'type-attribute') {
            $type = $em->getRepository('App:ProductTypes')->find($attributeId);
            $em->remove($type);
        } elseif($attribute === 'size-attribute') {
            $size = $em->getRepository('App:ProductSizes')->find($attributeId);
            $em->remove($size);
        } elseif($attribute === 'color-attribute') {
            $color = $em->getRepository('App:ProductColors')->find($attributeId);
            $em->remove($color);
        }

        $em->flush();

        return new Response('Attribute deleted');

    }

    function adjustDecimal($value) {

        if ( strpos( $value, "." ) === false ) {
            $value = $value . ".00";
        } elseif (strlen($value) - strrpos($value, '.') - 1 == 1) {
            $value = $value . "0";
        }

        return $value;
    }

    function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"', '”', '$'), array('&amp;', '&lt;', '&gt;', '', 'inch', 'inch', ''), $string);
    }

}