<?php

Kint::$return = true;
Kint::$aliases[] = 'f';
Kint::$plugins[] = 'blacklist';
Kint\Renderer\RichRenderer::$folder = true;
Kint\Parser\BlacklistPlugin::$shallow_blacklist = array('database');

function print_func($v = false) {
    if ($v) {
        echo $v;
    }
}

function f(...$vars) {
    $output = Kint::dump(...$vars);
    register_shutdown_function('print_func', $output);
}
