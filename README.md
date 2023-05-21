## Passimpay API PHP wrapper

This is a library for easy integration of Passimpay API to your PHP project.

## Requirements

* PHP >= 7.1.0
* ext-mbstring
* ext-json
* ext-curl

You will also need to create your platform [here](https://passimpay.io/account/platform/module) and specify certain values in code:
* platform id
* secret key

## Examples

1. Getting balance:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($balance, $error) = $api->balance();

if (null !== $error) {
  throw new Exception($error);
}

dosomething($balance);
```

2. Getting list of currencies:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($currencies, $error) = $api->currencies();

if (null !== $error) {
  throw new Exception($error);
}

dosomething($currencies);
```

3. Creating invoice link:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($url, $error) = $api->invoice('your invoice id', 999.0);

if (null !== $error) {
  throw new Exception($error);
}

dosomething($url);
```

4. Checking invoice status:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($status, $error) = $api->invoiceStatus('your invoice id');

if (null !== $error) {
  throw new Exception($error);
}

dosomething($status);
```

5. Getting wallet address for payments:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($address, $error) = $api->paymentWallet('order id', 'payment id');

if (null !== $error) {
  throw new Exception($error);
}

dosomething($address);
```

6. Withdraw:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($response, $error) = $api->withdraw('payment id', 'addressTo', 999.0);

if (null !== $error) {
  throw new Exception($error);
}

dosomething($response);
```

7. Checking transaction status:

```
use Passimpay/PassimpayApi;

$api = new PassimpayApi(123, 'secret key');

list($response, $error) = $api->transactionStatus('transaction hash');

if (null !== $error) {
  throw new Exception($error);
}

dosomething($response);
```

8. Handling notifications:

Upon creating platform at [passimpay.io](passimpay.io) you could specify endpoint to call when invoice status is changed.<br/>
Use code below to handle this notification:

```
$secretKey = '123';

$hash = $_POST['hash'];

$data = [
  'platform_id'  => (int) $_POST['platform_id'],  // Platform ID
  'payment_id'   => (int) $_POST['payment_id'],   // currency ID
  'order_id'     => (int) $_POST['order_id'],     // Payment ID of your platform
  'amount'       => $_POST['amount'],             // transaction amount
  'txhash'       => $_POST['txhash'],             // Hash or transaction ID. You can find the transaction ID in the PassimPay transaction history in your account.
  'address_from' => $_POST['address_from'],       // sender address
  'address_to'   => $_POST['address_to'],         // recipient address
  'fee'          => $_POST['fee'],                // network fee
];

if (isset($_POST['confirmations']))
{
  $data['confirmations'] = $_POST['confirmations']; // number of network confirmations (Bitcoin, Litecoin, Dogecoin, Bitcoin Cash)
}

$payload = http_build_query($data);

if (!isset($hash) || hash_hmac('sha256', $payload, $secretKey) != $hash)
{
  return false;
}

// payment credited
// your code...
```

## Contribution
Feel free to [create an issue](https://github.com/Passimpay/passimpay-api-php/issues) in case you have found a bug or have a suggestion.
