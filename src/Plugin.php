<?php

namespace amacneil\inspector;

use amacneil\inspector\twigextensions\InspectorTwigExtension;

use Craft;

class Plugin extends \craft\base\Plugin
{

    public function init( )
    {
        \Craft::$app->view->twig->addExtension(new InspectorTwigExtension);
    }
    public function getName()
    {
        return Craft::t('app','Inspector');
    }

    public function getVersion()
    {
        return '1.0';
    }

    public function getDeveloper()
    {
        return 'Adrian Macneil';
    }

    public function getDeveloperUrl()
    {
        return 'http://adrianmacneil.com';
    }


    public static function dd($data)
    {
		\yii\helpers\VarDumper::dump($data, 10, true);
		Craft::$app->end();
    }

    public static function dump($data)
    {
		return \yii\helpers\VarDumper::dumpAsString($data, 10, true);
		
    }

}


