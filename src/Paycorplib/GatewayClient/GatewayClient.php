<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClient;

use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientConfig\ClientConfig;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade\Payment;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade\Vault;

class GatewayClient {
    
    public $payment;
    public $vault;
    
    public function __construct(ClientConfig $config) {
        $this->payment = new Payment($config);
        $this->vault = new Vault($config);
    }
    
    public function getPayment() {
        return $this->payment;        
    }
    
    public function setPayment($payment) {
        $this->payment = $payment;
    }
    
    public function getVault() {
        return $this->vault;
    }
    
    public function setVault($vault) {
        $this->vault =$vault;
    }
    
}
