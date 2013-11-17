<?php

namespace h4kuna\ImageManager\Macros;

use Nette;

class Helpers extends Nette\Object {

    public static function prepareMacroArguments($macro) {
        $arguments = array_map(function ($value) {
            return trim($value);
        }, explode(",", $macro));

        $namespace = NULL;
        $name = $arguments[0];

        if (count($ns = explode("/", $name)) == 2) {
            list($namespace, $name) = $ns;
        }

        $args = array(
            "namespace" => $namespace,
            "name" => $name,
        );

        return $args;
    }

}
