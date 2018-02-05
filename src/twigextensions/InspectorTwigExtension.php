<?php

namespace amacneil\inspector\twigextensions;

use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\TwigMarkup;


class InspectorTwigExtension extends \Twig\Extension\AbstractExtension
{

    public function getName()
    {
        return 'inspect';
    }

    public function inDevMode()
    {
        return Craft::$app->config->general->devMode;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('inspect', [$this, 'inspect'], ['needs_context' => false, 'needs_environment' => true, 'is_safe' => array('html')]),
        );
    }

    /**
     * Display an object as a helpful string representation
     *
     * @param mixed $var
     */
    public function inspect($env, $var)
    {
        if(!$this->inDevMode()){
            return '';
        }

        if (is_null($var)) {
            $out = 'null';
        } elseif (is_array($var)) {
            $out = 'Array: '.$this->inspectArray($var);
        } elseif (is_object($var)) {
            $out = get_class($var);
            $out .= "\n".str_repeat('-', strlen($out));

            if (method_exists($var, 'getHelpText')) {
                $out .= "\n".$var->getHelpText();
            }

            $out .= $this->inspectAttributes($var);
            $out .= $this->inspectMethods($var);
        } else {
            $out = ucfirst(gettype($var)).': '.print_r($var, true);
        }
        
        return "<pre><code>".$out."</code></pre>";
    }

    protected function inspectAttributes($var)
    {
        $attributes = array();

        if (method_exists($var, 'getAttributes')) {
            $attributes = $var->getAttributes();
        }

        $reflector = new \ReflectionClass($var);
        foreach ($reflector->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $attributes[$property->name] = $property->getValue($var);
        }

        ksort($attributes);
        $out = "\n\nAttributes: ";
        foreach ($attributes as $key => $value) {
            if (is_array($value) || (is_object($value) && method_exists($value,'toArray')  ) ) {
                $value = $this->inspectArray($value);
            }
            if ($value instanceof \DateTime) {
                $out .= sprintf("\n    %-20s ", $key).sprintf("%s", $value->format('Y-m-d H:i:s'));
            }
            else {
                $keyout = sprintf("\n    %-20s ", $key);
                $out .= $keyout.sprintf("%s", $value);
            }
        }

        return $out;
    }

    protected function inspectMethods($var)
    {
        $reflector = new \ReflectionClass($var);
        $methods = array();

        foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ('_' !== substr($method->name, 0, 1)) {
                $methods[] = "\n    ".$method->name;
            }
        }

        if ($methods) {
            sort($methods);

            return "\n\nMethods: ".implode('', $methods);
        }
    }

    protected function inspectArray($var)
    {
        // convert objects to strings
        foreach ($var as $key => $value) {
            if (is_object($value)) {
                $var[$key] = get_class($value);
                if (method_exists($value, '__toString')) {
                    $var[$key] .= sprintf(': %s', $value);
                }
            }
        }

        return json_encode($var);
    }
}