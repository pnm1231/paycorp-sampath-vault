<?php

namespace createch\PaycorpSampathVault;

use createch\PaycorpSampathVault\Paycorplib\GatewayClient\GatewayClient;
use createch\PaycorpSampathVault\Paycorplib\GatewayClientConfig\ClientConfig;
use createch\PaycorpSampathVault\Paycorplib\GatewayClientPayment\PaymentRealTimeRequest;
use createch\PaycorpSampathVault\Paycorplib\GatewayClientComponent\CreditCard;
use createch\PaycorpSampathVault\Paycorplib\GatewayClientComponent\TransactionAmount;
use createch\PaycorpSampathVault\Paycorplib\GatewayClientEnums\TransactionType;

class PaycorpSampathRealTimePayment
{
    private $client;
    private $clientConfig;
    private $clientId;
    private $currency;
    private $response = [];

    public function __construct()
    {
        $this->clientConfig = new ClientConfig();

        $this->clientConfig->setServiceEndpoint(config('paycorp-sampath-vault.service_endpoint'));
        $this->clientConfig->setAuthToken(config('paycorp-sampath-vault.auth_token'));
        $this->clientConfig->setHmacSecret(config('paycorp-sampath-vault.hmac_secret'));

        $this->client = new GatewayClient($this->clientConfig);

        $this->clientId = config('paycorp-sampath-vault.client_id');
        $this->currency = config('paycorp-sampath-vault.currency');

    }

    public function realTimePayment(array $data)
    {
        try {
            $creditCard = new CreditCard();

            $creditCard->setType($data['card_type']); //VISA, MASTER
            $creditCard->setHolderName($data['card_holder_name']);
            $creditCard->setExpiry($data['expire_at']);
            $creditCard->setNumber($data['card_number']);
            $creditCard->setSecureId($data['secure_id']);
            $creditCard->setSecureIdSupplied(true);

            $realTimeRequest = new PaymentRealTimeRequest();
            $realTimeRequest->setClientId($this->clientId);
            $realTimeRequest->setTransactionType(TransactionType::$PURCHASE);
            $realTimeRequest->setCreditCard($creditCard);

            $transactionAmount = new TransactionAmount($data['amount']);
            $transactionAmount->setCurrency($this->currency);
            $realTimeRequest->setTransactionAmount($transactionAmount);
            $realTimeRequest->setClientRef($data['clientRef'] ?: '');
            $realTimeRequest->setComment($data['comment'] ?: '');
            $realTimeResponse = $this->client->getPayment()->realTime($realTimeRequest);

            $this->response['TxnReference'] = $realTimeResponse->getTxnReference();
            $this->response['ResponseCode'] = $realTimeResponse->getResponseCode();
            $this->response['ResponseText'] = $realTimeResponse->getResponseText();
            $this->response['SettlementDate'] = $realTimeResponse->getSettlementDate();
            $this->response['AuthCode'] = $realTimeResponse->getAuthCode();
            $this->response['status'] = true;
        }catch(\Exception $e){
            $this->response['status'] = false;
            $this->response['msg'] = $e->getMessage();
        }

        return $this->response;
    }
}
