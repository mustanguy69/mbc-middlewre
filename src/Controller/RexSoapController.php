<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\anotherSoapClient;


class HelloServiceController extends AbstractController
{
    /**
     * @Route("/getCustomerBulkDetails", defaults={"_format"="text/xml"})
     */
    public function customerGetBulkDetails()
    {

        $soapClient = new anotherSoapClient('https://v2wsisandbox.retailexpress.com.au/DOTNET/Admin/WebServices/v2/Webstore/Service.asmx?WSDL', array(
            'cache_wsdl'    => WSDL_CACHE_NONE,
            'cache_ttl'     => 86400,
            'trace'         => true,
            'exceptions'    => true,
            'soap_version'  => SOAP_1_1,
            'encoding'      => 'utf-8'
        ));

        $xmlRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ret="http://retailexpress.com.au/">
           <soapenv:Header>
              <ret:ClientHeader>
                 <ret:ClientID>9bf6dd42-35b9-46dd-948a-1c3c91906caa</ret:ClientID>
                 <ret:UserName>wsi</ret:UserName>
                 <ret:Password>wsipass</ret:Password>
              </ret:ClientHeader>
           </soapenv:Header>
           <soapenv:Body>
              <ret:CustomerGetBulkDetails>
                 <ret:LastUpdated>2018-10-10T00:00:00Z</ret:LastUpdated>
                 <ret:OnlyCustomersWithEmails>0</ret:OnlyCustomersWithEmails>
                 <ret:OnlyCustomersForExport>0</ret:OnlyCustomersForExport>
              </ret:CustomerGetBulkDetails>
           </soapenv:Body>
        </soapenv:Envelope>';

        try {
            $request = $soapClient->__anotherRequest('CustomerGetBulkDetails', $xmlRequest);
        } catch (SoapFault $e ){
            var_dump($e);
            exit();
        }

        $response = new Response($request);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }
}