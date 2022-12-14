<?php

namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade;

use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientUtils\RestClient;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientUtils\HmacUtils;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientRoot\PaycorpRequest;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientComponent\RequestHeader;

abstract class BaseFacade
{
    protected $config;

    protected function __construct($config)
    {
        $this->config = $config;
    }

    protected function process($request, $operation, $jsonHelper)
    {
        $jsonRequest = $this->buildRequest($request, $operation, $jsonHelper);

        $headers = $this->buildHeaders($jsonRequest);
        
        $jsonResponse = RestClient::sendRequest($this->config, $jsonRequest, $headers);

        if (!strpos($jsonResponse, 'responseData')) {
            throw new \Exception('Gateway returned an invalid response');
        }

        return $this->buildResponse($jsonResponse, $jsonHelper);
    }

    private function buildHeaders($request)
    {
        $header = new RequestHeader();

        $header->setAuthToken($this->config->getAuthToken());
        $header->setHmac(HmacUtils::genarateHmac($this->config->getHmacSecret(), $request));

        $headers = array();

        $headers[] = 'HMAC: ' . $header->getHmac() . '';
        $headers[] = 'AUTHTOKEN: ' . $header->getAuthToken() . '';
        $headers[] = 'Content-Type: application/json';

        return $headers;
    }

    private function buildRequest($requestData, $operation, $jsonHelper)
    {
        $paycorpRequest = new PaycorpRequest();

        $paycorpRequest->setOperation($operation);
        $paycorpRequest->setRequestDate(date('Y-m-d H:i:s'));
        $paycorpRequest->setValidateOnly($this->config->isValidateOnly());
        $paycorpRequest->setRequestData($requestData);

        $jsonRequest = $jsonHelper->toJson($paycorpRequest);

        return json_encode($jsonRequest);
    }

    private function buildResponse($response, $jsonHelper)
    {
        return $jsonHelper->fromJson(json_decode($response, true));
    }
}
