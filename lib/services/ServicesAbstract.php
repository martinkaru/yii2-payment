<?php

/**
 * Abstract class containing some common functionality for all services provided
 * by PaymentHandler
 *
 * @date 19.04.2012
 * @author Ivo Kund <ivo@opus.ee>
 */
namespace opus\payment\services;

use opus\payment\adapters\AbstractAdapter;
use opus\payment\Exception;
use opus\payment\PaymentHandlerBase;
use opus\payment\services\payment\AdapterInterface;

/**
 * Class ServicesAbstract
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment\services
 */
abstract class ServicesAbstract
{
    /** @var PaymentHandlerBase Reference to payment handler object */
    protected $paymentHandler;
    /** @var AbstractAdapter[]|AdapterInterface[] Array of adapter */
    protected $paymentAdapters = array();

    /**
     * Create a new service
     *
     * @param PaymentHandlerBase $paymentHandler Reference to the payment handler
     * @throws \opus\payment\Exception
     */
    function __construct(PaymentHandlerBase $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
        $this->loadAdapters();
        if (!isset($this->paymentAdapters)) {
            throw new Exception("No providers found for service: " . $this->getServiceCode());
        }
    }

    /**
     * Load supported adapters for this service
     */
    public function loadAdapters()
    {
        $this->paymentAdapters = array();
        foreach ($this->paymentHandler->getAdapters() as $adapterName => $paymentAdapter) {
            $interfaceClass = sprintf(
                '%s\%s\AdapterInterface',
                __NAMESPACE__,
                ucwords($this->getServiceCode())
            );
            if ($paymentAdapter instanceof $interfaceClass) {
                $this->paymentAdapters[$adapterName] = $paymentAdapter;
            }
        }
    }

    /**
     * Returns the code/type/tag of the service
     *
     * @return string
     */
    abstract public function getServiceCode();
}