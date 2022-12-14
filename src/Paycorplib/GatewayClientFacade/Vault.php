<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientFacade;

use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\StoreCardJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\RetrieveCardJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\UpdateCardJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\VerifyTokenJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientHelpers\DeleteTokenJsonHelper;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientEnums\Operation;

final class Vault extends BaseFacade {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function storeCard($request) {
        $storeCardJsonHelper = new StoreCardJsonHelper();
        return parent::process($request, Operation::$VAULT_STORE_CARD, $storeCardJsonHelper);
    }

    public function retrieveCard($request) {
        $retrieveCardJsonHelper = new RetrieveCardJsonHelper();
        return parent::process($request, Operation::$VAULT_RETRIEVE_CARD, $retrieveCardJsonHelper);
    }

    public function updateCard($request) {
        $updateCardJsonHelper = new UpdateCardJsonHelper();
        return parent::process($request, Operation::$VAULT_UPDATE_CARD, $updateCardJsonHelper);
    }

    public function verifyToken($request) {
        $verifyTokenJsonHelper = new VerifyTokenJsonHelper();
        return parent::process($request, Operation::$VAULT_VERIFY_TOKEN, $verifyTokenJsonHelper);
    }

    public function deleteToken($request) {
        $deleteTokenJsonHelper = new DeleteTokenJsonHelper();
        return parent::process($request, Operation::$VAULT_DELETE_TOKEN, $deleteTokenJsonHelper);
    }

}
