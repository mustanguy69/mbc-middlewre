<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FrontendController extends AbstractController
{

    /**
     * Homepage
     * @Route("/", name="homepage")
     *
     */
    public function homepage() {

        return $this->render('home.html.twig');

    }

    /**
     * List shopify products page
     * @Route("product/list", name="listProductsShopify")
     *
     */
    public function listProductsShopify() {

        $products = (new ShopifyController())->getProducts();

        return $this->render('list.html.twig', ['products' => $products]);

    }

    /**
     * create shopify + rex products page
     * @Route("product/create", name="createProductsPage")
     *
     */
    public function createProductPage(Request $request) {


        $em = $this->getDoctrine()->getManager();
        $suppliers = $em->getRepository('App:Suppliers')->findAll();
        $brands = $em->getRepository('App:Brands')->findAll();
        $productTypes = $em->getRepository('App:ProductTypes')->findAll();
        $productSizes = $em->getRepository('App:ProductSizes')->findAll();
        $productColors = $em->getRepository('App:ProductColors')->findAll();

        return $this->render('create.html.twig', ['suppliers' => $suppliers,
            'brands' => $brands,
            'types' => $productTypes,
            'sizes' => $productSizes,
            'colors' => $productColors
        ]);

    }

    /**
     * POST shopify + rex product
     * @Route("product/create/post", name="createProductsPagePost")
     *
     */
    public function createProductPost(Request $request) {


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
        $images = $request->request->get('images');
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

        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('App:Brands')->find($brand);
        $supplier = $em->getRepository('App:Suppliers')->find($supplier);
        $type = $em->getRepository('App:ProductTypes')->find($type);
        $size = $em->getRepository('App:ProductSizes')->find($size);
        $color = $em->getRepository('App:ProductColors')->find($color);


        $product = new Products();
        $product->setTitle($title)
            ->setSku($sku)
            ->setSupplier($supplier)
            ->setBrand($brand)
            ->setDescription($description)
            ->setType($type)
            ->setTags($tags)
            ->setPrice($price)
            ->setCompare($compare)
            ->setBarcode($barcode)
            ->setSupplierStock($supplierStock)
            ->setStock($stock)
//                ->setImages($images)
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
            ->setToShopify($shopify);

        $em->persist($product);
        $em->flush();

        return $this->redirect($this->generateUrl('updateProductsPage', ['id' => $product->getId(), 'status' => 'success']), 301);

    }

    /**
     * update shopify + rex products page
     * @Route("product/update/{id}", name="updateProductsPage")
     *
     */
    public function updateProductPage(Request $request, $id, $status = null,ValidatorInterface $validator) {


        $em = $this->getDoctrine()->getManager();

        $suppliers = $em->getRepository('App:Suppliers')->findAll();
        $brands = $em->getRepository('App:Brands')->findAll();
        $productTypes = $em->getRepository('App:ProductTypes')->findAll();
        $productSizes = $em->getRepository('App:ProductSizes')->findAll();
        $productColors = $em->getRepository('App:ProductColors')->findAll();

        $product = $em->getRepository('App:Products')->find($id);

        if($request->getMethod() === 'POST') {
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
            $images = $request->request->get('images');
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

            $brandObj = $em->getRepository('App:Brands')->find($brand);
            $supplierObj = $em->getRepository('App:Suppliers')->find($supplier);
            $typeObj = $em->getRepository('App:ProductTypes')->find($type);
            $sizeObj = $em->getRepository('App:ProductSizes')->find($size);
            $colorObj = $em->getRepository('App:ProductColors')->find($color);

            $product->setTitle($title)
                ->setSku($sku)
                ->setSupplier($supplierObj)
                ->setBrand($brandObj)
                ->setDescription($description)
                ->setType($typeObj)
                ->setTags($tags)
                ->setPrice($price)
                ->setCompare($compare)
                ->setBarcode($barcode)
                ->setSupplierStock($supplierStock)
                ->setStock($stock)
//                ->setImages($images)
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
                ->setToShopify($shopify);

            $errors = $validator->validate($product);
            if (count($errors) > 0) {
                $status = 'error';
            } else {
                $status = 'success';
            }
        }


        $em->flush();

        return $this->render('edit.html.twig', [
            'product' => $product,
            'suppliers' => $suppliers,
            'brands' => $brands,
            'types' => $productTypes,
            'sizes' => $productSizes,
            'colors' => $productColors,
            'status' => $status,
        ]);

    }

    /**
     * List|Edit|Add attribute rex
     * @Route("attributes", name="attributesPage")
     *
     */
    public function attributesPage() {

        $em = $this->getDoctrine()->getManager();

        $suppliers = $em->getRepository('App:Suppliers')->findBy([], ['name' => 'ASC']);
        $brands = $em->getRepository('App:Brands')->findBy([], ['name' => 'ASC']);
        $productTypes = $em->getRepository('App:ProductTypes')->findBy([], ['name' => 'ASC']);
        $productSizes = $em->getRepository('App:ProductSizes')->findBy([], ['size' => 'ASC']);
        $productColors = $em->getRepository('App:ProductColors')->findBy([], ['name' => 'ASC']);

        return $this->render('attributes.html.twig', [
            'suppliers' => $suppliers,
            'brands' => $brands,
            'types' => $productTypes,
            'sizes' => $productSizes,
            'colors' => $productColors
        ]);

    }




}
