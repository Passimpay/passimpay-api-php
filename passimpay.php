<?php declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

error_reporting(E_ALL);

const URL_BASE               = 'https://passimpay.io/api';
const URL_BALANCE            = URL_BASE . '/balance';
const URL_CURRENCIES         = URL_BASE . '/currencies';
const URL_INVOICE_CREATE     = URL_BASE . '/createorder';
const URL_INVOICE_STATUS     = URL_BASE . '/orderstatus';
const URL_WITHDRAW           = URL_BASE . '/withdraw';
const URL_TRANSACTION_STATUS = URL_BASE . '/transactionstatus';

const PLATFORM_ID = 000;
const SECRET_KEY  = '';

function balance(): array
{
    $response = post(URL_BALANCE);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        return [$response['balance'], null];
    }

    return [null, $response['message']];
}

function currencies(): array
{
    $response = post(URL_CURRENCIES);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        return [$response['list'], null];
    }

    return [null, $response['message']];
}

function invoice(string $id, float $amount): array
{
    $response = post(URL_INVOICE_CREATE, [
        'order_id' => $id,
        'amount'   => $amount
    ]);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        return [$response['url'], null];
    }

    return [null, $response['message']];
}

function invoiceStatus(string $id): array
{
    $response = post(URL_INVOICE_STATUS, ['order_id' => $id]);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        return [$response['status'], null];
    }

    return [null, $response['message']];
}

function withdraw(string $paymentId, string $addressTo, float $amount): array
{
    $response = post(URL_WITHDRAW, [
        'payment_id' => $paymentId,
        'address_to' => $addressTo,
        'amount'     => $amount
    ]);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        unset($response['result']);
        unset($response['message']);
        
        return [$response, null];
    }

    return [null, $response['message']];
}

function transactionStatus(string $txHash): array
{
    $response = post(URL_TRANSACTION_STATUS, ['txhash' => $txHash]);
    
    if (isset($response['result']) && (1 == $response['result'])) {
        unset($response['result']);
        unset($response['message']);
        
        return [$response, null];
    }

    return [null, $response['message']];
}

function post(string $url, array $parameters = []): array
{
    $payload = ['platform_id' => PLATFORM_ID];
    $payload = array_merge($payload, $parameters);
    $payload = array_merge($payload, ['hash' => hash_hmac('sha256', http_build_query($payload), SECRET_KEY)]);
    $payload = http_build_query($payload);
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    
    return json_decode($response, true);
}
