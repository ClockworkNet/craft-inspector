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

}


