<?php /** @noinspection SpellCheckingInspection */

/**
 * Handles payment actions for Danske (formerly Sampo) bank, based on iPizza platform.
 * Supports only ISO-8859-1, all other characters will be transliterated
 *
 * Specification: http://www.danskebank.ee/et/14732.html
 *
 * @date 17.04.2012
 * @author Ivo Kund <ivo@opus.ee>
 * @author Martin Karu <martin@opus.ee>
 */
namespace opus\payment\adapters;

use opus\payment\services\payment\Response;

/**
 * Class Danske
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment\adapters
 */
class Danske extends AbstractIPizza
{
    /**
     * @inheritdoc
     */
    public $name = 'Danske Bank';
    /**
     * @inheritdoc
     */
    public $charset = 'iso-8859-1';

    /**
     * @inheritdoc
     */
    public function canHandlePaymentResponse(Response $response)
    {
        $isDanske = 'SAMPOPANK' == $response->getParam('VK_SND_ID', false);
        return $isDanske && parent::canHandlePaymentResponse($response);
    }
}