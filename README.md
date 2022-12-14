# Paycorp Sampath IPG for Laravel


Paycorp Sampath IPG is a Laravel package for making payment using Sampath Bank Payment Gateway through Paycorp. In this package you can accept:

  - Redirect Page Payments
  - Realtime Payments
  - Tokenized Payments

# Features

  - Using with composer
  - Easy integration
  - Compatible with Laravel

# Requirements

> PHP >= 5.6
> OpenSSL >= 1.0.1
> curl >= 7.34
> Composer

# Usage

### Installation


```sh
composer require pnm1231/paycorp-sampath-ipg
```

### Configurations

#### Laravel

After install via composer add Config values to .env file as following:

```sh
SAMPATH_SERVICE_ENDPOINT=
SAMPATH_AUTHTOKEN=
SAMPATH_HMAC=
SAMPATH_CURRENCY=
SAMPATH_CLIENT_ID=
SAMPATH_RETURN_URL=
```

### Methods

##### PaymentInit

Import package class in you class header:

```sh
use pnm123\PaycorpSampathVault\PaycorpSampathVault;
```
Sample InitRequest

```sh
    $paymentInit = new PaycorpSampathVault();
    $data['clientRef'] = $request->user()->id;
    $data['comment'] = 'Your comment';
    $data['total_amount'] = 1010;
    $data['service_fee_amount'] = 1010;
    $data['payment_amount'] = 1010;
    $res = $paymentInit->initRequest($data);
    
    return response()->json($res);
```

You will receive reqid, payment_page_url for the redirect. When you redirected to the "payment_page_url" user can enter the card details and pay. Then paycorp will return the response to "SAMPATH_RETURN_URL" you configured in .env file. When get the correct response, you need to call PaymentCompleteRequest.

#### completeRequest

```sh
    $data['reqid'] = $_GET['reqid'];
    $data['clientRef'] = $_GET['clientRef'];
    $paymentComplete = new PaycorpSampathVault();
    $response = $paymentComplete->completeRequest($data);
    
    return response()->json($res);
```

#### Make Real Time Payments using Token

In Payment complete response you will get the "Token" and necessary data. Using "Token" you can make instant payments without entering card details or redirecting user everytime to payment page. This is the special feature of Vault in paycorp.

```sh
    $payment = new PaycorpSampathVault();

    $data = [];
    $data['clientRef'] = 'Clent Ref';
    $data['token'] = 'token';
    $data['comment'] = 'Your Comment';
    $data['amount'] = 1010; // in cents
    $data['expire_at'] = 'Expiry Date of Card'; //1223
    $data['payment_amount'] = 1010;
    $response = $payment->realTimePayment($data);
    
    return $response;
```

# NOTE:

> Please read Paycorp Technical document and understand the workflow well before use this package. This package only for developers to save their life.


License
----

### MIT

**Free Software, Hell Yeah!**

