#!/bin/env php
<?php
/** File: bin/apigen-it.php
 *
 * Fcj.2013-04-01
 *
 * Basically run ApiGen on your Composer.phar-based project.
 *
 * Usage: ./apigen-it.php
 *
 * TODO: Pass additional command line arguments ($argv[]) to bin/apigen.php
 * TODO.. Or have this script be parameteriz-able and pass arguments after e.g. '--' to ApiGen.
 * TODO: phpuml -o doc/phpuml-ed/ -f htmlnew -n Nymfony -i .svn -i .git src/ ...
 */

$here = realpath(__DIR__);
chdir($here);

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require_once "vendor/autoload.php";

$destDir = "doc/apigen-ed/"; // -d ...
$main = "Nymfony"; // --main ...
$title = "Nymfony & al. API documentation"; // --title ...

// --exclude ...
$excludes = array(
    '*.js',
    '*Test*',
    '*test*'
);
$excludes = array_map(function($globspec) { return "--exclude $globspec"; }, $excludes);

/*
 * Get paths from Composer's class loader.
 */
$prefixes     = $loader->getPrefixes();
$fallbackDirs = $loader->getFallbackDirs();
$classMap     = $loader->getClassMap();
//var_dump($prefixes); var_dump($fallbackDirs); var_dump($classMap); exit;

$sources = call_user_func_array('array_merge', $prefixes);
$sources = array_merge($sources, $fallbackDirs, $classMap);

$sources[] = "app/AppCache.php";
$sources[] = "app/AppKernel.php";
$sources[] = "app/check.php";
$sources[] = "app/SymfonyRequirements.php";

$sources = array_filter($sources);
$sources = array_unique($sources);
//var_dump($sources); exit;

$hereLength = strlen($here);
$sources = array_map(function ($p) use ($here, $hereLength) {
        $p = realpath($p);
        // Strip project root ($here) from paths (sugar, for compacity) :
        $p = strncmp($p, $here, $hereLength) == 0 ?
            substr($p, $hereLength+1) : $p;
        return "-s $p";
    },
    $sources);

/*
 * Build command line.
 */
$command = '' //"echo "
    . "bin/apigen.php "
    . "-d $destDir "
    . "--todo=yes "
    . "--deprecated=yes "
    . "--main $main "
    . "--title $title "
    . implode(' ', $excludes) . ' '
    . implode(' ', $sources);

/*
 * Execute... end.
 */
$command = escapeshellcmd($command);
echo "----\nCOMMAND:\n\t$command\n----";
$retval = 255;
passthru($command, $retval); exit;

echo "\nRETURN VALUE: $retval";

/* Humm...
use Symfony\Component\Process\ProcessBuilder;
$args = array('bin/apigen.php');
$builder = ProcessBuilder::create($args);
$process = $builder->getProcess();
$process->run();
*/
