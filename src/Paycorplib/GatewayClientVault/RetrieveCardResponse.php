<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientVault;

class RetrieveCardResponse {

    private $responseCode;
    private $responseText;

    public function __construct() {
        
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function setResponseCode($responseCode) {
        $this->responseCode = $responseCode;
    }

    public function getResponseText() {
        return $this->responseText;
    }

    public function setResponseText($responseText) {
        $this->responseText = $responseText;
    }

}
