<?php

/**
 * Represents a HTML form used for authentication or payment
 *
 * @date 19.04.2012
 * @author Ivo Kund <ivo@opus.ee>
 */
namespace opus\payment\services\payment;

use opus\payment\widgets\PaymentWidget;

/**
 * Class Form
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment\services\payment
 */
class Form extends Dataset
{

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->getAdapter()->getCharset();
    }
    /**
     * @return string
     */
    public function getAction()
    {
        return $this->getAdapter()->serviceUrl;
    }
    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->getAdapter()->name;
    }

    /**
     * @return string
     */
    public function getProviderTag()
    {
        return $this->getAdapter()->adapterTag;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return PaymentWidget::widget(['form' => $this]);
    }
}
