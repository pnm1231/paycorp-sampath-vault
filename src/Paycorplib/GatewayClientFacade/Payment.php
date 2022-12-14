<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade;

use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade\BaseFacade;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\PaymentRealTimeJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\PaymentInitJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\PaymentCompleteJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\PaymentBatchJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientEnums\Operation;

final class Payment extends BaseFacade {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function realTime($request) {
        $paymentRealTimeJsonHelper = new PaymentRealTimeJsonHelper();
        return parent::process($request, Operation::$PAYMENT_REAL_TIME, $paymentRealTimeJsonHelper);
    }

    public function init($request) {
        $paymentInitJsonHelper = new PaymentInitJsonHelper();
        return parent::process($request, Operation::$PAYMENT_INIT, $paymentInitJsonHelper);
    }

    public function complete($request) {
        $paymentCompleteJsonHelper = new PaymentCompleteJsonHelper();
        return parent::process($request, Operation::$PAYMENT_COMPLETE, $paymentCompleteJsonHelper);
    }
    
    public function batch($request){
           $paymentBatchJsonHelper = new PaymentBatchJsonHelper();
           return parent::process($request, Operation::$PAYMENT_BATCH, $paymentBatchJsonHelper);        
    }

}
