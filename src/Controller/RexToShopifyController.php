<?php

namespace App\Controller;

use App\Entity\Products;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RexToShopifyController extends AbstractController
{

    /**
     * Export excel file for mass upload in rex
     * @Route("rex/export/mass-upload", name="exportExcelMassUpload")
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportExcelMassUpload() {

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('App:Products')->findBy(['massUploadedRex' => false]);

        $templateFile = './dist/excel/templateRexMassUpload.xlsx';
        $spreadsheet = IOFactory::load($templateFile);
        $sheet = $spreadsheet->getActiveSheet();

        $i = 11;
        foreach ($products as $product) {
            $sheet->setCellValue('C'.$i, $product->getSku());
            $sheet->setCellValue('AM'.$i, $product->getMen());
            $sheet->setCellValue('AN'.$i, $product->getWomen());
            $sheet->setCellValue('AO'.$i, $product->getBoys());
            $sheet->setCellValue('AP'.$i, $product->getGirls());
            $sheet->setCellValue('AQ'.$i, $product->getUnisex());

            if($product->getToShopify()) {
                $sheet->setCellValue('AT'.$i, $product->getToShopify());
            } else {
                $sheet->setCellValue('AT'.$i, "FALSE");
            }

            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $today = new \DateTime();
        $today = $today->format('d-m-y');
        $fileName = 'rex-mass-upload_'.$today.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }

    /**
     * mass upload done
     * @Route("rex/export/done", name="massUploadDone")
     *
     */
    public function massUploadDone() {

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('App:Products')->findBy(['massUploadedRex' => false]);

        foreach ($products as $product) {
           $product->setMassUploadedRex(true);
        }

        $em->flush();

        return new Response();

    }


    /**
     * @Route("rex/shopify/synced", name="detectIfShopifySynced")
     *
     */
    public function detectIfShopifySynced() {

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('App:Products')->findBy(['syncWithShopify' => false]);

        foreach ($products as $product) {
            $shopifyIdRequest = (new ShopifyController())->getProductIdByTitle($product->getTitle());
            if (count((array)$shopifyIdRequest->products) === 1) {
                $product->setSyncWithShopify(1);
                $product->setShopifyProductId($shopifyIdRequest->products[0]->id);
                foreach($shopifyIdRequest->products[0]->variants as $variant) {
                    if($variant->sku == $product->getSku()) {
                        $product->setShopifyVariantId($variant->id);
                        if($product->getImages()) {
                            foreach($product->getImages() as $image) {
                                (new ShopifyController())->addUpdateImage($shopifyIdRequest->products[0]->id, $image->getSrc(), $image->getName(), $variant->id);
                            }
                        }
                    }
                }
            }
        }

        $em->flush();

        return new Response('cron passed');

    }


}
