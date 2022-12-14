<?php

namespace pnm1231\PaycorpSampathVault;

use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClient\GatewayClient;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientConfig\ClientConfig;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientPayment\PaymentRealTimeRequest;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientPayment\PaymentInitRequest;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientPayment\PaymentCompleteRequest;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientComponent\CreditCard;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientComponent\TransactionAmount;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientEnums\TransactionType;
use pnm1231\PaycorpSampathVault\Paycorplib\GatewayClientComponent\Redirect;

class PaycorpSampathVault
{
    private $client;
    private $clientConfig;
    private $returnUrl;
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

        $this->returnUrl = config('paycorp-sampath-vault.return_url');
        $this->clientId = config('paycorp-sampath-vault.client_id');
        $this->currency = config('paycorp-sampath-vault.currency');
    }

    public function IPGLoaded()
    {
        return '1.0.0.1';
    }

    public function initRequest(array $data)
    {
        try {
            $initRequest = new PaymentInitRequest();

            $initRequest->setClientId($this->clientId);
            $initRequest->setTransactionType(TransactionType::$PURCHASE);
            $initRequest->setClientRef(!empty($data['clientRef']) ? $data['clientRef'] : '');
            $initRequest->setComment(!empty($data['comment']) ? $data['comment'] : '');
            $initRequest->setTokenize(config('paycorp-sampath-vault.tokenize'));
            //$initRequest->setExtraData(array("msisdn" => "$msisdn", "sessionId" => "$sessionId"));

            $transactionAmount = new TransactionAmount($data['total_amount']);
            $transactionAmount->setTotalAmount($data['total_amount']);
            $transactionAmount->setServiceFeeAmount($data['service_fee_amount']);
            $transactionAmount->setPaymentAmount($data['payment_amount']);
            $transactionAmount->setCurrency($this->currency);
            $initRequest->setTransactionAmount($transactionAmount);

            $redirect = new Redirect($this->returnUrl);
            $redirect->setReturnMethod('GET');
            $initRequest->setRedirect($redirect);

            $initResponse = $this->client->getPayment()->init($initRequest);

            if ($initResponse->getReqid() !== null) {
                $this->response['reqid'] = $initResponse->getReqid();
                $this->response['payment_page_url'] = $initResponse->getPaymentPageUrl();
                $this->response['status'] = true;
            } else {
                $this->response['status'] = false;
                $this->response['msg'] = 'Payment init request failed';
            }
        } catch (\Exception $e) {
            report($e);
            $this->response['status'] = false;
            $this->response['msg'] = $e->getMessage();
        }

        return $this->response;
    }

    public function realTimePayment(array $data)
    {
        try {
            $creditCard = new CreditCard();
            $creditCard->setNumber($data['token']);
            $creditCard->setExpiry($data['expire_at']);

            $realTimeRequest = new PaymentRealTimeRequest();
            $realTimeRequest->setClientId($this->clientId);
            $realTimeRequest->setTransactionType(TransactionType::$PURCHASE);
            $realTimeRequest->setCreditCard($creditCard);

            //$extraData = array("invoice-no" => "I99999", "job-no" => "J10101");
            //$realTimeRequest->setExtraData($extraData);

            $transactionAmount = new TransactionAmount($data['amount']);
            $transactionAmount->setCurrency($this->currency);
            $realTimeRequest->setTransactionAmount($transactionAmount);
            $realTimeRequest->setClientRef($data['clientRef'] ?: '');
            $realTimeRequest->setComment($data['comment'] ?: '');
            $realTimeResponse = $this->client->getPayment()->realTime($realTimeRequest);

            $this->response['TxnReference'] = $realTimeResponse->getTxnReference() ?: '';
            $this->response['ResponseCode'] = $realTimeResponse->getResponseCode() ?: '';
            $this->response['ResponseText'] = $realTimeResponse->getResponseText() ?: '';
            $this->response['SettlementDate'] = $realTimeResponse->getSettlementDate() ?: '';
            $this->response['AuthCode'] = $realTimeResponse->getAuthCode() ?: '';
            $this->response['status'] = true;
        } catch(\Exception $e) {
            report($e);
            $this->response['status'] = false;
            $this->response['msg'] = $e->getMessage();
        }

        return $this->response;
    }

    public function completeRequest($requestId)
    {
        try {
            $completeRequest = new PaymentCompleteRequest();
            $completeRequest->setClientId($this->clientId);
            $completeRequest->setReqid($requestId);

            $completeResponse = $this->client->getPayment()->complete($completeRequest);

            $this->response['ResponseCode'] = $completeResponse->getResponseCode();
            $this->response['ClientID'] = $completeResponse->getClientId();
            $this->response['TransactionType'] = $completeResponse->getTransactionType();
            $this->response['CardType'] = $completeResponse->getCreditCard()->getType();
            $this->response['CardNumber'] = $completeResponse->getCreditCard()->getNumber();
            $this->response['CardHolderName'] = $completeResponse->getCreditCard()->getHolderName();
            $this->response['ExpireAt'] = $completeResponse->getCreditCard()->getExpiry();
            $this->response['ClientRef'] = $completeResponse->getClientRef();
            $this->response['TransactionAmount'] = $completeResponse->getTransactionAmount();
            $this->response['Comment'] = $completeResponse->getComment();
            $this->response['TxnReference'] = $completeResponse->getTxnReference();
            $this->response['ResponseText'] = $completeResponse->getResponseText();
            $this->response['AuthCode'] = $completeResponse->getAuthCode();
            $this->response['ExtraData'] = $completeResponse->getExtraData();
            $this->response['Token'] = $completeResponse->getToken();
            $this->response['status'] = true;
        }catch (\Exception $e){
            report($e);
            $this->response['status'] = false;
            $this->response['msg'] = 'Payment not completed';
            $this->response['ResponseText'] = $e->getMessage();
        }

        return $this->response;
    }
}
