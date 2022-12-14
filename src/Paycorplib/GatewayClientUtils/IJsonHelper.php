<?php
namespace pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientUtils;

interface IJsonHelper {

    public function fromJson($json);

    public function toJson($instance);
    
}
