#!/usr/bin/php
<?php
/*
 * Attempts to extract download links
 * 
 * @usage : 003-utility-extract $source $pattern $output
 * 
 */
// Checks arguments
if( 4 != $argc){
    die("[!] Invalid arguments count : 3 arguments expected, got ".($argc-1));
}
$source                     = $argv[1];
$pattern                    = $argv[2];
$output                     = $argv[3];
// Checks path
if( !is_readable($source)){
    die("[!] Invalid source file");
}
// Checks output
if( !is_writable($output)){
    if( ! touch($output) || ! chmod($output,0700)){
        die("[!] Invalid output file");
    }
}
$file_content               = file_get_contents($source);
preg_match_all("%{$pattern}%", $file_content,$matches);
if( ! isset($matches[1])){
    die("! No capture found in pattern");
}
$resultList                     = $matches[1];
echo "[.] ".count($resultList)." result(s) found.\n";
// Resets output
file_put_contents($output, "");
foreach ($resultList as $link) {
    file_put_contents($output, $link."\n", FILE_APPEND);
}
echo "[x] Done writing to $output\n";