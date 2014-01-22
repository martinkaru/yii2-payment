<?php

/**
 * Represents a financial transaction
 *
 * @date 19.04.2012
 * @author Ivo Kund <ivo@opus.ee>
 */
namespace opus\payment\services\payment;

use opus\payment\Exception;
use opus\payment\PaymentHandlerBase;
use yii\base\Object;

/**
 * Class Transaction
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment\services\payment
 *
 * @method string getSum($default = null)
 * @method string getComment($default = null)
 * @method string getTransactionId($default = null)
 * @method string getReference($default = null)
 * @method string getCurrency($default = null)
 * @method string getLanguage($default = null)
 *
 * @method boolean hasSum()
 * @method boolean hasComment()
 * @method boolean hasTransactionId()
 * @method boolean hasReference()
 * @method boolean hasCurrency()
 * @method boolean hasLanguage()
 */
class Transaction extends Object
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * Create a new transaction object
     *
     * @param mixed $transactionID
     * @param float $sum
     * @param array $params
     * @return self
     */
    public static function create($transactionID, $sum, $params = [])
    {
        $transaction = new self;
        $transaction->setTransactionID($transactionID)
            ->setSum($sum)
            ->setCurrency(PaymentHandlerBase::DEFAULT_CURRENCY)
            ->setLanguage(PaymentHandlerBase::DEFAULT_LANGUAGE);
        foreach ($params as $key => $value)
        {
            $transaction->params[$key] = $value;
        }
        return $transaction;
    }

    /**
     * Sets the transaction language
     *
     * @param string $language
     * @return Transaction
     */
    public function setLanguage($language)
    {
        $this->params['language'] = $language;
        return $this;
    }

    /**
     * Sets the transaction currency
     *
     * @param string $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->params['currency'] = $currency;
        return $this;
    }

    /**
     * Set the transaction sum
     *
     * @param float $sum
     * @return Transaction
     */
    public function setSum($sum)
    {
        $sum = (float)str_replace(',', '.', $sum);
        $this->params['sum'] = $sum;
        return $this;
    }

    /**
     * Set the transaction ID
     *
     * @param int $transactionId
     * @throws \opus\payment\Exception If transaction ID is not numeric
     * @return Transaction
     */
    public function setTransactionId($transactionId)
    {
        if (is_numeric($transactionId) || $transactionId === false) {
            $this->params['transactionId'] = $transactionId;
            return $this;
        }
        throw new Exception("Please use a numeric value for transaction ID");
    }

    /**
     * @return string
     */
    function __toString()
    {
        return \var_export($this->params, true);
    }

    /**
     * Sets the transaction comment
     *
     * @param string $comment
     * @return Transaction
     */
    public function setComment($comment)
    {
        $this->params['comment'] = $comment;
        return $this;
    }

    /**
     * Sets the payment reference
     *
     * @param mixed $reference If NULL, the reference will be generated automatically from Transaction ID
     * @return Transaction
     */
    public function setReference($reference = null)
    {
        $reference === null && $reference = self::generateReference($this->getTransactionId());
        $this->params['reference'] = $reference;
        return $this;
    }

    /**
     * Generate a standard reference number
     * Code borrowed from http://trac.midgard-project.org/browser/branches/branch-2_6/src/net.nemein.payment/handler/nordea.php?rev=14963
     *
     * @author The Midgard Project, http://www.midgard-project.org
     *
     * @param int $transactionId
     * @return string
     */
    protected function generateReference($transactionId)
    {
        $multipliers = array(7, 3, 1);
        $sum = 0;
        $multiplier = 0;
        $reference = (string)$transactionId;
        for ($digitPosition = strlen($reference) - 1; $digitPosition >= 0; $digitPosition--) {
            $digit = $reference{$digitPosition};
            if (!is_numeric($digit)) {
                continue;
            }
            $digit = (int)$digit;
            $sum += $digit * $multipliers[$multiplier];
            $multiplier = ($multiplier == 2) ? 0 : $multiplier + 1;
        }
        // Get the difference to the next highest ten
        $nextTen = (((int)($sum / 10)) + 1) * 10;
        $checkDigit = $nextTen - $sum;
        if ($checkDigit == 10) {
            $checkDigit = 0;
        }
        return (string)$transactionId . $checkDigit;
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        $property = lcfirst(substr($name, 3));
        if (substr($name, 0, 3) === 'get') {
            if (isset($this->params[$property])) {
                return $this->params[$property];
            } elseif (isset($params[0])) {
                return $params[0];
            }
        } elseif (substr($name, 0, 3) === 'has') {
            return isset($this->params[$property]);
        }
        return parent::__call($name, $params);
    }
}