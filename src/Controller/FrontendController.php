<?php

namespace App\Controller;

use App\Entity\Products;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("product/list", name="listProducts")
     *
     */
    public function listProductsShopify(PaginatorInterface $paginator, Request $request) {

        $term = $request->query->get('term');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('App:Products');
        $qb = $repository->getWithSearchQueryBuilder($term);

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('list.html.twig', ['pagination' => $pagination]);

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
     * Update shopify + rex products page
     * @Route("product/update/{id}", name="updateProduct")
     *
     */
    public function updateProductPage(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('App:Products')->find($id);
        $suppliers = $em->getRepository('App:Suppliers')->findAll();
        $brands = $em->getRepository('App:Brands')->findAll();
        $productTypes = $em->getRepository('App:ProductTypes')->findAll();
        $productSizes = $em->getRepository('App:ProductSizes')->findAll();
        $productColors = $em->getRepository('App:ProductColors')->findAll();

        return $this->render('edit.html.twig', ['suppliers' => $suppliers,
            'brands' => $brands,
            'types' => $productTypes,
            'sizes' => $productSizes,
            'colors' => $productColors,
            'product' => $product
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

    /**
     * Popup rex mass upload
     */
    public function popupRexUpload(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $cookie = $request->cookies->get('remind-later');

        if($cookie === null){
            $products = $em->getRepository('App:Products')->findBy(['massUploadedRex' => false]);

            if ($products) {
                return $this->render('popup-rex-upload.html.twig', [
                    'products' => $products,
                ]);
            }
        }

        return new Response();
    }

    /**
     * Popup rex remind later
     * @Route("popup-rex/remind-later", name="popupRemindLater")
     */
    public function popupRemindLater() {

        $cookie = new Cookie('remind-later', '1', time()+600);
        $response = new Response();
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * Excel bulk import page
     * @Route("excel-bulk-import", name="excelBulkImportPage")
     */
    public function excelBulkImportPage() {

        $em = $this->getDoctrine()->getManager();
        $bulkImports = $em->getRepository('App:BulkImport')->findBy([], ['date' => 'DESC']);

        return $this->render('excel-bulk-import.html.twig', ['bulkImports' => $bulkImports]);
    }

}
