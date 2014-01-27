yii2-payment
============

Library to handle payments with Estonian banks. Main features includes:
- Payment service implemented with the following handlers: IPizza (Swedbank, SEB, LHV, Danske, Krediidipank), Estcard, Nordea
- Object oriented and extensible code, you can easily add custom adapters
- Generates payment forms from transaction objects
- Handles bank post-back requests

## Installation
Define in your `composer.json` the repository and required package. 
```json
{
	"require": {
		"opus-online/yii2-payment": "*",
	},

	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/opus-online/yii2-payment"
		}
	]
}
```

## Usage
Overwrite `PaymentHandlerBase` class and at least the method `getConfiguration`. Complement `demo-config.php` with your account details and locations of key and certificate files.  
```php
class MyPaymentHandler extends \opus\payment\PaymentHandlerBase
{

    /**
     * Returns the configuration array
     */
    public function getConfiguration($key = null)
    {
        return [
            // common parameters for all adapters
            'common' => array(
                'returnRoute' => 'bankController/return',
            ),
            // adapter-specific parameters
            'adapters' => require 'demo-config.php',
        ];
    }
}
```
Example `demo-config.php` (includes only SEB configuration)
```php
return [
    'SEB' => [
        'class' => '\opus\payment\adapters\Seb',
        'serviceUrl' => 'https://pangalink.net/banklink/seb',
        'pkcKeyPath' => 'SEB/private_key.pem',
        'pkcCertPath' => 'SEB/cert.pem',
        'params' => [
            // add service parameters here
            'VK_ACC' => '',
            'VK_NAME' => '',
            'VK_SND_ID' => '',
        ],
    ],
];
```

### Render all payment forms
In your
```php
// create the payment service
$service = MyPaymentHandler::createPaymentService();

// create a transaction object
$transaction = $service->createTransaction(1234, 234, ['comment' => 'Comment']);

// render the widget
echo \opus\payment\widgets\PaymentWidget::widget([
    'forms' => $service->generateForms($transaction),
]);

```
#### Customize the payment form
The default implementation uses the widget `opus\payment\widgets\PaymentWidget`. You can override and use this to use custom forms or elements. 
```php
class MyPaymentWidget extends PaymentWidget
{
    protected function generateSubmit(Form $form) {
        return Html::submitButton('MyCustomLabel');
    }
}
```

And then just render your customized widget instead of the default one (see the widget example above).

### Receive return requests from the bank
```php
$service = PaymentHandler::createPaymentService();
$response = $service->handleResponse($_REQUEST);

// re-generate the transaction object from the response
$transaction = $response->getTransaction();

if ($response->isSuccessful()) {
  // see the Transaction object for more details
  echo $transaction->getTransactionId();
}
```
