<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientPayment;

class PaymentBatchResponse {
    
    private $groupId;

    public function getGroupId() {
        return $this->groupId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }
}
