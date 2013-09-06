<?php
/** File: lib/dev_functions.php
 *
 * Development & trial'n'error debugging helper stuff.
 *
 * todo: Move display code out of _pnb() for reuse in e.g. _pr().
 */
use \ChromePhp;
use \FirePHP;

/** Log arguments "everywhere" (ChromePhp, FirePHP, error_log()).
 *
 * @param ...
 * @return void
 */
function _xl()
{
    $backtrace = _backtrace();
    $backtraceStop = count($backtrace);
    $backtraceStop = $backtraceStop < 5 ? $backtraceStop : 5;
    for($i=1; $i<=$backtraceStop; $i++)
        error_log($backtrace[$i]);

    ChromePhp::log($backtrace[1]);
    // FirePHP::log...

    $args = func_get_args();

    // todo: check if xdebug is present for else that thing ends up crashing...
    //      » or use xdebug_var_dump(...)
    call_user_func_array('_vd', $args);
    //call_user_func_array('_pr', $args);

    call_user_func_array('_cl', $args);
    call_user_func_array('_fb', $args);
}

/** Alias for ChromePhp::log(...)
 *
 * todo: Have a way of having an URL for displaying file:line
 */
function _cl()
{
    $args = func_get_args();
    foreach($args AS $argNb => $arg) {
        ChromePhp::log("  #$argNb : ", $arg);
    }
}

/** Alias for FirePHP's fb(...).
 */
function _fb()
{
    $args = func_get_args();
    $firephp = FirePHP::getInstance(true);
    return call_user_func_array(array($firephp, 'fb'), $args);
}

/**
 * @param bool $toStringOnly
 * @param int $limit
 * @param bool $ignoreArgs
 * @return array
 * @See php manual about debug_backtrace().
 *
 * todo: $limit impl for php<5.4.0
 */
function _backtrace($toStringOnly=true, $limit=0, $ignoreArgs=false)
{
    $options = 0;
    if (PHP_VERSION >= "5.3.6") {
        $options |= DEBUG_BACKTRACE_PROVIDE_OBJECT;
        $options |= $ignoreArgs ? DEBUG_BACKTRACE_IGNORE_ARGS : 0;
    }
    if (PHP_VERSION >= "5.4.0")
        $backtrace = debug_backtrace($options, $limit);
    else
        $backtrace = debug_backtrace(true);
    // Build the __toString key :
    foreach($backtrace AS $level => &$call) {
        foreach(array('file', 'line', 'function', 'class', 'object', 'type', 'args') AS $key)
            $$key = isset($call[$key]) ? $call[$key] : '<<?>>';

        $call['__toString'] = "File: $file, line: $line, function: $function, class: $class"
            . (is_object($object) ? (", object: ".get_class($object)) : '')
            . ", args: " . (is_array($args) ? count($args) : $args);
    }
    if ($toStringOnly)
         //return array_reduce($backtrace, create_function('$str,$e', 'return "$str\n{$e[\'__toString\']}";'), "Call stack:\n");
        return array_map(
            create_function('$e', 'return $e["__toString"];'),
            $backtrace);
    else return $backtrace;
}

/** Essentially a var_dump(...) output through php's error_log().
 *
 * @param mixed $a, $b, ...
 * @return string That which was passed to error_log().
 */
//function _var_dump()
function _vd()
{
    if (! ob_start()) return;
    $args = func_get_args();
    //call_user_func_array('var_dump', $args);
    foreach($args AS $arg) {
        echo "\n====\n";
        var_dump($arg);
    }
    $str = ob_get_clean();
    error_log( $str );
    return $str;
}

/** Basically a php's print_r() through error_log().
 *
 * @param mixed $a, $b, ...
 * @return void
 */
function _pr()
{
    $args = func_get_args();
    error_log ("»»» " . __FUNCTION__
        //. "(...) :"
        // gettype() on each argument :
        . "(" . implode(", ", array_map(create_function('$e', 'return gettype($e);'), $args)) . ") :"
        . " «««");
    $i = 0;
    foreach($args AS $elt) {
        $i++;
        error_log( "»» ". "Argument #$i, " . gettype($elt) . ": ««");
        error_log( "\n" . print_r($elt, true) );
    }
    error_log( "«««o»»»");
}

/** Print 'n' Break : Sort of a "simple" utility function for breaking
 * the execution flow somewhere.
 *
 * @param mixed $a, $b, $c, ...
 * @return void DIE() is called once finished.
 *
 * Todo: Have $_SERVER, $_GET, ... printed at the bottom and somehow folded? ^^
 *       Or maybe just issue a phpinfo() ?
 *       Or better keep that func. simple ?
 * Todo: Display some info on caller (script name and line number) ?
 * Todo: Work with, and without Xdebug extension.
 */
//function _print_n_break()
function _pnb ()
{
    $epilogue = $prologue = '';
    if (!headers_sent()) {
        $me = __FUNCTION__;
        header("HTTP/1.0 500 Oooops! $me()");
        header('Content-Type: text/html');
        $css = <<<EOS
    <style type='text/css'>
        .argument {
            border: 1px solid black;
            padding: 1em;
        }
        h1, h2, h3 {
            border-bottom: 1px solid black;
        }
        table th { text-align: left; }
    </style>
EOS;
        $prologue =
              "<!DOCTYPE html>\n"
            . "<html>\n<head>\n\t<title>500 $me</title>\n$css\n</head>\n<body>\n";
        $epilogue =
              "<hr /><strong style='color:red;'>End of <em>$me(...)</em>.</strong>\n"
            . "\n</body>\n</html>";
    }

    echo $prologue;
    echo "<pre class='debug'>\n";

    // var_dump() of the arguments :
    $n = func_num_args();
    for($i=0;$i<$n;$i++) {
        $arg_i = func_get_arg($i);
        echo "<div class='argument arg$i'>\n";
        //$text = print_r($arg_i, true);
        //echo htmlentities($text);
        var_dump( $arg_i );
        echo "\n</div>\n";
    }

    echo "<h2>Stack trace:</h2>";
    xdebug_print_function_stack();


    // Dump out a few configuration settings :
    echo "<h2>PHP configuration settings:</h2>",
         "<table class='ini'>";
    foreach(array(
        'log_errors', 'error_log',
        'display_errors', 'display_startup_errors',
        'error_reporting') AS $var) {
        echo "<tr>",
            "<th>$var</th>",
            "<td>", ini_get($var), "</td>"
            ;
    }
    echo "</table>";

    /*
    // CLASSES :
    echo "<h2>get_declared_classes():</h2>",
         "<ul>";
    $classes = get_declared_classes();
    sort($classes, SORT_STRING);
    foreach($classes AS $class) {
        echo "<li>$class</li>";
    }
    echo "</ul>";

    // INTERFACES :
    echo "<h2>get_declared_interfaces():</h2>",
         "<ul>";
    $interfaces = get_declared_interfaces();
    sort($interfaces, SORT_STRING);
    foreach($interfaces AS $iface) {
        echo "<li>$iface</li>";
    }
    echo "</ul>";
     */

    echo "<h2>Inheritance graph of defined classes and interfaces:</h2>",
        '';
    list($dag, $dag_t, $t) = _inheritance_graph();
    //var_dump($dag);
    //print_r($dag);
    if (!empty($dag)) {
        echo "<ul>";
        foreach($dag AS $typeName => $t) {
            echo "<li>", "$typeName";
            if ($t['parent']) {
                echo " <strong>extends</strong> ", $t['parent']['name'];
            }
            if (!empty($t['parent']['interfaces'])) {
                echo " <strong>implements</strong> ";
                foreach($t['parent']['interfaces'] AS $ifaceName => $_iface) {
                    echo "$ifaceName, ";
                }
            }
            echo "</li>";
        }
        echo "</ul>";
    }

    echo "</pre>\n";
    echo $epilogue;
    DIE(__FUNCTION__ . ": This is the end, beautiful friend...");
}

/*
 *
 * todo: Strip PHP stuff, incl. DOM, SPL, ...
 */
/*
function _inheritance_graph()
{
    $classes    = get_declared_classes();
    $interfaces = get_declared_interfaces();
    sort($classes,    SORT_STRING);
    sort($interfaces, SORT_STRING);

    $classes = array_fill_keys ($classes, array(
    ));
    $interfaces = array_fill_keys ($interfaces, array(
    ));

    $dag   = array();
    $dag_t = array(); // transpose of $dag.
    foreach($classes AS $className => &$class) {
        $extends    = class_parents   ($className, true);
        $implements = class_implements($className, true);
        foreach ($extends AS $parent) {
            $dag[$parent][$className] = 'E';
            $dag_t[$className]['extends'][$parent] = true;
        }
        foreach($implements AS $ifaceName => &$iface) {
            $dag[$ifaceName][$className] = 'I';
            $dag_t[$className]['implements'][$ifaceName] = true;
        }
    }
    return array($dag, $dag_t);
}
 */

/**
 *
 */
function _inheritance_graph()
{
    static $verticeSkeleton = array(
        'name'      => '',
        'shortname' => '',
        'rc'     => NULL,
        'parent' => NULL,
        'interfaces' => array(),
        'abstract'  => FALSE,
        'final'     => FALSE,
        'interface' => FALSE,
        'file' => '',
        'line' => 0
    );

    $typeNames = func_get_args();
    if (empty($typeNames)) {
        $typeNames = get_declared_classes() + get_declared_interfaces();
        sort($typeNames, SORT_STRING);
    } else {
        // assert object, or valid class name.
    }

    $dag   = array();
    $dag_t = array(); // transpose of $dag.

    foreach($typeNames AS $typeName) {
        $rc = new \ReflectionClass( $typeName );
        $fqName = $rc->getName();

        //
        if ($rc->isInternal()) {
            continue;
        }
        //
        else if (!isset($dag[$fqName])) {
            $dag[ $fqName ] = $verticeSkeleton;
        }

        $v =& $dag[ $fqName ];

        $v['name'] = $fqName;
        $v['shortname'] = $rc->getShortName();
        $v['rc'] = $rc;
        $v['abstract']  = $rc->isAbstract();
        $v['final']     = $rc->isFinal();
        $v['interface'] = $rc->isInterface();

        if (! $v['interface']) {
            $parent = $rc->getParentClass();
            if ($parent) {
                $parentName = $parent->getName();
                if (!isset($dag[$parentName])) {
                    $dag[$parentName] = $verticeSkeleton;
                }
                $v['parent'] =& $dag[$parentName];
            }
        }

        $interfaces = $rc->getInterfaces();
        foreach($interfaces AS $ifaceName => $iface) {
            if (!isset($dag[$ifaceName])) {
                $dag[$ifaceName] = $verticeSkeleton;
            }
            $v['interfaces'][$ifaceName] =& $dag[$ifaceName];
        }

        unset( $v );
    }

    return array($dag, $dag_t, $typeNames);
}
