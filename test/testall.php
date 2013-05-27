#!/usr/bin/env php
<?php

// Loads the Offwave scanner
require_once __DIR__."/../lib/Offwave/Scanner.php";
require_once __DIR__."/../lib/Offwave/Exception.php";
require_once __DIR__."/../lib/Offwave/Agents/Abstract.php";

// Starts the scanner
$offwave_scanner = new Offwave_Scanner();

// Scan the test environment
$path = __DIR__."/testenv";

Offwave_Scanner::$do_debug = FALSE;

$offwave_result = $offwave_scanner->scan( $path, 2);
//or maybe Offwave_Scanner::DEPTH_INFINITE instead of 2 ?

//print_r( Offwave_Scanner::debug());

print_r($offwave_result);

?>