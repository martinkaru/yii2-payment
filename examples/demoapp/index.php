<?php
require 'yii_bootstrap.php';

require 'PaymentHandler.php';

$service = PaymentHandler::createPaymentService();
$transaction = $service->createTransaction(14, 35, ['comment' => 'Comment']);
$transaction->setReference();

echo '<h1>Transaction</h1>' . $transaction . '<h1>Submit forms</h1>';

foreach ($service->generateForms($transaction) as $key => $form)
{
    echo "<h2>$key</h2>" . $form;

    echo "<h4>Debug form:</h4>";
    echo \opus\payment\widgets\PaymentWidget::widget(['form' => $form, 'debug' => true]);
}

