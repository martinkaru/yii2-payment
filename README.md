yii2-payment
============

Library to handle payments with Estonian banks.

# Installation 
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
            'common' => array(
                'returnRoute' => 'bankController/return',
            ),
            'adapters' => require 'demo-config.php',
        ];
    }
}

```
### Render all payment forms

```php
$service = MyPaymentHandler::createPaymentService();
$transaction = $service->createTransaction(1234, 234, ['comment' => 'Comment']);

foreach ($service->generateForms($transaction) as $key => $form) {
    echo $form;
    // this is the same as:
    // echo \opus\payment\widgets\PaymentWidget::widget(['form' => $form]);
}

```
#### Customize the payment form
The default implementation uses the widget `opus\payment\widgets\PaymentWidget`. You can override and use this to use custom forms or elements. 
```php
class MyPaymentWidget extends PaymentWidget
{
    protected function generateSubmit()
    {
        return Html::submitButton('MyCustomLabel');
    }
}
```

And then just render your customized widget instead of the default one (see the widget example above).

### Receive return reques from the bank
```php
$service = PaymentHandler::createPaymentService();
$response = $service->handleResponse($_REQUEST);
$transaction = $response->getTransaction();

if ($response->isSuccessful()) {
  // see the Transaction object for more details
  echo $transaction->getTransactionId();
}
```
