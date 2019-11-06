<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/list", name="listProductsShopify")
     *
     */
    public function listProductsShopify() {

        $products = (new ShopifyController())->getProducts();

        return $this->render('list.html.twig', ['products' => $products]);

    }

    /**
     * create shopify + rex products page
     * @Route("/create", name="createProductsShopify")
     *
     */
    public function createProductsShopify() {

        $em = $this->getDoctrine()->getManager();
        $suppliers = $em->getRepository('App:Suppliers')->findAll();
        $brands = $em->getRepository('App:Brands')->findAll();

        return $this->render('create.html.twig', ['suppliers' => $suppliers, 'brands' => $brands]);

    }

}
