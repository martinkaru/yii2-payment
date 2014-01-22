<?php
/**
 * Return form
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @date 22.01.14
 */

require 'yii_bootstrap.php';
require 'PaymentHandler.php';

$service = PaymentHandler::createPaymentService();

$response = $service->handleResponse($_REQUEST);
echo $response;
echo $response->getTransaction();
