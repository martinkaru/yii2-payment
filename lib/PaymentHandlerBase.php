<?php /** @noinspection SpellCheckingInspection */
/**
 * Extendable PaymentHandler base class that provides payment functionality.
 * Payment adapters supported so far:
 *    - IPizza (seb, swedbank, danske, krediidipank)
 *    - Nordea
 *    - Estcard (credit card payments)
 *
 * Charsets supported:
 * - UTF-8
 *        - krediidipank
 *        - swedbank
 * - ISO-5589-1 (with transliteration)
 *        - seb (utf-8 may be possible, but not implemented)
 *    - danske (utf-8 not supported by API)
 *    - lhv (utf-8 not supported by API)
 *    - nordea (utf-8 not supported by API)
 *    - estcard (utf-8 not supported by API)
 *
 * ------------------ Common usage (payment):
 * <code>
 * try
 * {
 *    $oHandler = new PaymentHandler(); // class extending PaymentHandlerBase
 *    $oPaymentService = $oHandler->createService(PaymentHandler::SERVICE_PAYMENT);
 * }
 * catch (PaymentException $oException)
 * {
 *    // could not load adapters, faulty configuration etc..
 * }
 *
 * ------------------ To generate payment forms:
 * try
 * {
 *    $oTransaction = $oPaymentService->createTransaction($iTransactionID, $fSum);
 *    $aForms = $oPaymentService->generateForms($oTransaction);
 *    echo $aForms['seb']->__toString();
 * }
 * catch (PaymentException $oException)
 * {
 *    // errors with signature files, configuration parameters, etc
 * }
 *
 * ------------------ To receive server responses:
 * try
 * {
 *    $oResponse = $oPaymentService->handleResponse($_REQUEST);
 *    $bSuccess = $oResponse->isSuccessful();
 *  $oTransaction = $oResponse->getTransaction(); // see method documentation !!
 * }
 * catch (PaymentException $oException)
 * {
 *    // tampering with MACs, configuration errors etc..
 * }
 *
 * </code>
 *
 * @todo Implement authentication services, unicode problems with SEB
 *
 * @date 19.04.2012
 * @author Ivo Kund <ivo@opus.ee>
 */

namespace opus\payment;

use opus\payment\adapters\AbstractAdapter;
use opus\payment\services\Payment;
use opus\payment\services\payment\Form;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * Class PaymentHandlerBase
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment
 */
abstract class PaymentHandlerBase
{
    /** @var string Estonian language */
    const LANGUAGE_ET = 'et';
    /** @var string Russian language */
    const LANGUAGE_RU = 'ru';
    /** @var string English language */
    const LANGUAGE_EN = 'en';

    /** @var string Default currency to use with transactions */
    const DEFAULT_CURRENCY = 'EUR';
    /** @var string Defines default language to use when one is not specified */
    const DEFAULT_LANGUAGE = self::LANGUAGE_ET;

    /** @var string Payment service type */
    const SERVICE_PAYMENT = 'payment';
    /** @var string Authentication service type (not implemented yet) */
    const SERVICE_AUTHENTICATION = 'authentication';

    /** @var array Array of configuration parameters */
    protected $conf;
    /** @var array Holds the array of adapter objects */
    protected $adapters;
    /**
     * @var Application
     */
    public $app;

    /**
     * Create a new PaymentHandler object
     */
    public function __construct()
    {
        $this->conf = $this->getConfiguration();
        $this->app = \Yii::$app;
        $this->initAdapters();
    }

    /**
     * Returns the configuration array
     */
    abstract public function getConfiguration($key = null);

    /**
     * Initialize all enabled adapters (specified in the configuration)
     *
     * @throws Exception
     */
    protected function initAdapters()
    {
        if (!isset($this->conf)) {
            throw new Exception("Adapter configuration not set, cannot initialize");
        }
        $this->adapters = array();
        foreach ($this->conf['adapters'] as $key => $adapter) {
            $adapterConf = ArrayHelper::merge($this->conf['common'], $adapter);

            $adapterConf['paymentHandler'] = $this;
            $adapterConf['adapterTag'] = $key;
            $this->adapters[$key] = \Yii::createObject($adapterConf);
        }
    }

    /**
     * Returns an array of usable adapters
     *
     * @return array
     * @throws Exception
     */
    public function getAdapters()
    {
        if (!isset($this->adapters)) {
            throw new Exception("Adapters not loaded yet.");
        }

        return $this->adapters;
    }

    /**
     * Create and return a service handler object.
     *
     * @param string $serviceType Possible options: PaymentHandlerBase::SERVICE_PAYMENT
     * @return mixed
     * @throws Exception
     */
    public function createService($serviceType)
    {
        $class = sprintf('%s\services\%s', __NAMESPACE__, ucwords($serviceType));
        if (class_exists($class)) {
            $handler = new $class($this);
            return $handler;
        } else {
            throw new Exception("Unknown service: $serviceType");
        }
    }

    /**
     * @return Payment
     */
    public static function createPaymentService()
    {
        $class = get_called_class();
        $handler = new $class;
        /** @var $handler self */
        return $handler->createService(self::SERVICE_PAYMENT);
    }

    /**
     * Format configuration parameters right before usage.
     *
     * @param string $param
     * @param mixed $value
     * @return mixed
     */
    public function formatConfigParam($param, $value)
    {
        return $value;
    }

    /**
     * Finalize a FORM (add custom elements, etc)
     *
     * @param Form $form
     * @param AbstractAdapter $adapter Adapter associated with this form
     */
    public function finalizeForm(Form $form, AbstractAdapter $adapter)
    {
        // default behaviour does nothing here
    }

    /**
     * Overwrite this to add custom file path parsing
     * @param string $relativePath
     * @return string
     */
    public function createFilePath($relativePath)
    {
        return $relativePath;
    }

    /**
     * Creates an absolute url from a relative one
     *
     * @param $route
     * @return string
     */
    public function createAbsoluteUrl($route)
    {
        return $this->app->urlManager->createAbsoluteUrl($route);
    }
}
