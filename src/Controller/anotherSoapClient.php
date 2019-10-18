<?php


namespace App\Controller;


class anotherSoapClient extends \SoapClient
{

    function __construct($wsdl = "", $options = []) {
        parent::__construct($wsdl, $options);
        $this->server = new \SoapServer($wsdl, $options);
    }
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $result = parent::__doRequest($request, $location, $action, $version, $one_way = 0);
        return $result;
    }
    function __anotherRequest($call, $params) {
        $location = 'http://v2wsisandbox.retailexpress.com.au/dotnet/admin/webservices/v2/inventoryplanning/inventoryplanningservice.asmx';
        $action = 'http://retailexpress.com.au/'.$call;
        $request = $params;
        $result =$this->__doRequest($request, $location, $action, '1.1');
        return $result;
    }

}