<?php

/**
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @date 21.01.14
 */
class PaymentHandler extends \opus\payment\PaymentHandlerBase
{

    /**
     * Returns the configuration array
     */
    public function getConfiguration($key = null)
    {
        return [
            'common' => array(
                'returnRoute' => '/ret.php',
                'params' => [],
            ),
            'adapters' => \yii\helpers\ArrayHelper::merge(require 'demo-config.php', @require 'local-config.php'),
        ];
    }

    /**
     * Overwrite this to add custom file path parsing
     * @param string $relativePath
     * @return string
     */
    public function createFilePath($relativePath)
    {
        return 'keys/' . $relativePath;
    }
}