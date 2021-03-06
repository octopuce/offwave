<?php

// Loads the Offwave scanner
require_once __DIR__."/../lib/Offwave/Scanner.php";
require_once __DIR__."/../lib/Offwave/Exception.php";
require_once __DIR__."/../lib/Offwave/Agents/Abstract.php";

// Builds the Offwave object
$offwave_configuration = array(

//    "db_accounts" => array(
//        "mysql"     => new PDO("mysql","user","pass"),
//        "postgres"  => new PDO("mysql","user","pass")
//    )
);

// Starts the scanner
$offwave_scanner = new Offwave_Scanner( $offwave_configuration );

// Loop through accounts 

// Sets the scanned path
// For the sake of the demo, we identify a dummy CMS in the folder
// Note : you should really use absolute path
$path = __DIR__."/../tests/Cms/Spip/2.0";

Offwave_Scanner::$do_debug = TRUE;

$offwave_result = $offwave_scanner->scan( $path );

print_r( Offwave_Scanner::debug());

print_r($offwave_result);

/*
array (
  "application_type" => "cms",
  "application_name" => "spip",
  "application_version" => "1.0+"
  "application_plugins" => array(
	"plugin_name" => "plugin_version"
  )
)
*/
