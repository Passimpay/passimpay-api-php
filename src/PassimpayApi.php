<?php declare(strict_types=1);

namespace Passimpay;

class PassimpayApi
{
    const URL_BASE = 'https://passimpay.io/api';

    const URL_BALANCE            = URL_BASE . '/balance';
    const URL_CURRENCIES         = URL_BASE . '/currencies';
    const URL_INVOICE_CREATE     = URL_BASE . '/createorder';
    const URL_INVOICE_STATUS     = URL_BASE . '/orderstatus';
    const URL_PAYMENT_WALLET     = URL_BASE . '/getpaymentwallet';
    const URL_WITHDRAW           = URL_BASE . '/withdraw';
    const URL_TRANSACTION_STATUS = URL_BASE . '/transactionstatus';

    protected $platformId;
    protected $secretKey;

    public __construct(string $platformId, string $secretKey)
    {
        $this->platformId = $platformId;
        $this->secretKey  = $secretKey;
    }
    
    public function balance(): array
    {
        $response = post(URL_BALANCE);
        
        if (isset($response['result']) && (1 == $response['result'])) {
            return [$response['balance'], null];
        }

        return [null, $response['message']];
    }

    public function currencies(): array
    {
        $response = post(URL_CURRENCIES);
        
        if (isset($response['result']) && (1 == $response['result'])) {
            return [$response['list'], null];
        }

        return [null, $response['message']];
    }

    public function invoice(string $id, float $amount): array
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

    public function invoiceStatus(string $id): array
    {
        $response = post(URL_INVOICE_STATUS, ['order_id' => $id]);
        
        if (isset($response['result']) && (1 == $response['result'])) {
            return [$response['status'], null];
        }

        return [null, $response['message']];
    }

    public function paymentWallet(string $orderId, string $paymentId): array
    {
        $response = post(URL_PAYMENT_WALLET, [
            'order_id'   => $orderId,
            'payment_id' => $paymentId
        ]);
        
        if (isset($response['result']) && (1 == $response['result'])) {
            return [$response['address'], null];
        }

        return [null, $response['message']];
    }

    public function withdraw(string $paymentId, string $addressTo, float $amount): array
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

    public function transactionStatus(string $txHash): array
    {
        $response = post(URL_TRANSACTION_STATUS, ['txhash' => $txHash]);
        
        if (isset($response['result']) && (1 == $response['result'])) {
            unset($response['result']);
            unset($response['message']);
            
            return [$response, null];
        }

        return [null, $response['message']];
    }

    protected function request(string $url, array $parameters = []): array
    {
        if (empty($this->secretKey)) {
            throw new Exception('Passimpay: secret key can not be empty.');
        }

        if (empty($this->platformId)) {
            throw new Exception('Passimpay: platform id can not be empty.');
        }

        $payload = ['platform_id' => $this->platformId];
        $payload = array_merge($payload, $parameters);
        $payload = array_merge($payload, ['hash' => hash_hmac('sha256', http_build_query($payload), $this->secretKey)]);
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
}