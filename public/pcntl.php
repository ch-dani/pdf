<?php

#phpinfo();
#exit();

ini_set('display_errors', 1);
error_reporting(E_ALL);

var_dump([
    'extension_loaded' => extension_loaded('pcntl'),
    'pcntl_signal exists' => function_exists('pcntl_signal'),
    'pcntl_async_signals'=>function_exists('pcntl_async_signals')
]);


pcntl_async_signals(true);


exit("end");

