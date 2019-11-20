<?php

namespace App\Command;

use App\Controller\BddController;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;


class BulkImportCommand extends Command
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected static $defaultName = 'app:bulk-import';

    protected function configure()
    {
        $this
            ->setDescription('Bulk import product MW')
            ->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $id = $input->getArgument('id');
        $bulkImport = $this->em->getRepository('App:BulkImport')->find($id);
        $bulkImport->setStatus('In progress');

        try {
            $spreadsheet = IOFactory::load('upload/import/'.$bulkImport->getFileName());
            $sheet = $spreadsheet->getActiveSheet();
            $sheetTable = [];
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $sheetTable[$cell->getRow()][] = $cell->getValue();
                }
            }

            foreach ($sheetTable as $key => $table) {
                if ($key !== 1) {
                    $finalArray[] = array_combine($sheetTable[1], $table);
                }
            }

            foreach ($finalArray as $value) {
                $supplier = $this->em->getRepository('App:Suppliers')->findOneBy(['name' => $value['supplier']]);
                $brand = $this->em->getRepository('App:Brands')->findOneBy(['name' => $value['brand']]);
                $productType = $this->em->getRepository('App:ProductTypes')->findOneBy(['name' => $value['type']]);
                $productSize = $this->em->getRepository('App:ProductSizes')->findOneBy(['size' => $value['size']]);
                $productColor = $this->em->getRepository('App:ProductColors')->findOneBy(['name' => $value['color']]);

                $controller = new BddController($this->em);

                $request = new Request(
                    $_GET,
                    $_POST,
                    [],
                    $_COOKIE,
                    [],
                    $_SERVER
                );
                $imgArray = [$value['image'],$value['image2']];
                $imgArrayName = [$value['image_name'],$value['image2_name']];
                $imgArray = array_filter($imgArray);
                $imgArrayName = array_filter($imgArrayName);
                $request->request->add([
                    'title'             =>  $value['title'],
                    'sku'               =>  $value['sku'],
                    'supplier'          =>  $supplier->getId(),
                    'brand'             =>  $brand->getId(),
                    'description'       =>   $value['description'],
                    'product-type'      =>  $productType->getId(),
                    'tags'              =>  $value['tags'],
                    'price'             =>  $value['price'],
                    'compare'           =>  $value['compare'],
                    'barcode'           =>  $value['barcode'],
                    'supplier-stock'    =>  $value['supplier_stock'],
                    'stock'             =>  $value['stock'],
                    'weight'            =>  $value['weight'],
                    'length'            =>  $value['length'],
                    'season'            =>  $value['season'],
                    'size'              =>  $productSize->getId(),
                    'color'             =>  $productColor->getId(),
                    'women'             =>  $value['women'],
                    'men'               =>  $value['men'],
                    'boys'              =>  $value['boys'],
                    'girls'             =>  $value['girls'],
                    'unisex'            =>  $value['unisex'],
                    'shopify'           =>  $value['to_shopify'],
                    'base64Image'       =>  $imgArray,
                    'base64ImageName'   =>  $imgArrayName,
                ]);

                $controller->addProductBdd($request);
            }

            $bulkImport->setStatus('Done');

            $output->writeln('done');
        } catch (\Exception $e) {
            $output->writeln($e);
            $bulkImport->setStatus('Error');
        }

        $this->em->flush();
    }
}