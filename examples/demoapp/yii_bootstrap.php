<?php
/**
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @date 21.01.14
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$rootDir = dirname(dirname(__DIR__));
$config = [
    'id' => 'banklink-demo',
    'basePath' => dirname(__DIR__),
    'vendorPath' => $rootDir . '/vendor',
    'extensions' => require($rootDir . '/vendor/yiisoft/extensions.php'),
    'components' => [
        'urlManager' => ['enablePrettyUrl' => true, 'showScriptName' => false]
    ]
];


$application = new \yii\web\Application($config);
//$application->run();