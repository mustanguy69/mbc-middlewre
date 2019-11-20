<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Entity\Suppliers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class RexToBddController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get REX Suppliers to add into bdd
     * @Route("/rexToBddSuppliers", name="rexToBddSuppliers")
     *
     */
    public function rexToBddSuppliers() {

        $suppliersXml = (new RexSoapController())->getSuppliers();
        $suppliersObject = simplexml_load_string($suppliersXml);
        $em = $this->getDoctrine()->getManager();
        foreach ($suppliersObject as $supplier) {
            $entity = $em->getRepository('App:Suppliers')->findBy(['name' => $supplier->Supplier]);
            if (!$entity) {
                $supplierBdd = new Suppliers();
                $supplierBdd->setCode($supplier->Supplier_Code);
                $supplierBdd->setName($supplier->Supplier);

                $em->persist($supplierBdd);
            }
        }

        $em->flush();

        return $this->render('home.html.twig');

    }

    /**
     * Set Brands in BDD when added to REX
     * @Route("/rexToBddBrands", name="rexToBddBrands")
     *
     */
    public function rexToBddBrands($brands) {

        foreach ($brands as $brand) {
            $entity = $this->em->getRepository('App:Brands')->findBy(['name' => $brand]);
            if (!$entity) {
                $brandObject = new Brands();
                $brandObject->setName($brand);
                $this->em->persist($brandObject);
            }
        }
        $this->em->flush();

        return $this->render('home.html.twig');
    }

}
