<?php

namespace App\Controller;

use App\Command\BulkImportCommand;
use App\Entity\BulkImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ExcelBulkImportController extends AbstractController
{


    /**
     * @Route("/downlod-template", name="downloadtemplate")
     */
    public function downloadTemplate() {

        $templateFile = './dist/excel/templateBulkImport.xlsx';
        $today = new \DateTime();
        $today = $today->format('YmdHis');

        return $this->file($templateFile, "templateBulkImport-". $today .".xlsx", ResponseHeaderBag::DISPOSITION_INLINE);

    }

    /**
     * @Route("/upload/bulk/import", name="uploadBulkImport")
     */
    public function uploadBulkImport(Request $request, LoggerInterface $logger) {

        $file = $request->files->get('file');

        $em = $this->getDoctrine()->getManager();
        $bulkImport = new BulkImport();
        $bulkImport->setDate(new \DateTime())->setStatus('Started');
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $file->move(
                'upload/import',
                $fileName
            );
            $bulkImport->setFileName($fileName);
        } catch (FileException $e) {
            dump($e);
        }

        $em->persist($bulkImport);
        $em->flush();


        $command = new BulkImportCommand($em, $logger);
        $input = new ArrayInput(['id' => $bulkImport->getId()]);
        $output = new BufferedOutput();

        try {
            $resultCode = $command->run($input, $output);
        } catch (\Exception $e) {
            $bulkImport->setStatus('Error');
            dump($e);
        }


        $em->flush();

        return new Response($resultCode);

    }

    /**
     * @Route("/downlod-file/{file}", name="downloadFileBulkImport")
     */
    public function downloadFileBulkImport($file) {

        $templateFile = './upload/import/'.$file;

        return $this->file($templateFile, $file, ResponseHeaderBag::DISPOSITION_INLINE);

    }



}
