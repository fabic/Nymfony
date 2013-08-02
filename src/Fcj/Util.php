<?php
/**
 * FC.2013 April : Some old school procedural-minded utility functions.
 *
 */
namespace Fcj;

use Symfony\Component\PropertyAccess\PropertyAccess;


class Util
{

    /** Re-indexes a $list of things by/-following ... ~~ a list of property paths ~~.
     *
     * @see http://localhost:8000/nymfony/doc/apigen-ed/class-Symfony.Component.PropertyAccess.PropertyAccess.html
     * @see http://symfony.com/blog/new-in-symfony-2-2-new-propertyaccess-component
     *
     * @param \Traversable $list A list of things.
     * @param string ... A list of property paths.
     * @return array A new list.
     */
    public static function reindex($list)
    {
        $ppaths = func_get_args();
        array_shift($ppaths);
        if (empty($ppaths))
            return $list;

        $retval = array();
        $path = array_shift($ppaths);
        $accessor = PropertyAccess::getPropertyAccessor();

        foreach ($list AS $item) {
            //try {
            $index = $accessor->getValue($item, $path);
            if ($item instanceOf \Traversable) // FIXME ?
            $retval[$index] = reindex($item, $ppaths);
            else
                $retval[$index] = $item;
            //catch($ex) {
            //}
        }

        return $retval;
    }

}

/** Old impl.
 *
 * TODO: Move it where it belongs!
 *
function array_reindex(Array $a)
{
$keys = func_get_args();
array_shift($keys);
if( empty($keys) )
return $a;
$retval = array();
$k = array_shift($keys);
foreach($a as $b) {
if (array_key_exists($k, $b)) {
$index = $b[$k];
$retval[$index] = call_user_func_array('array_reindex', $keys);
}
else error_log(__FUNCTION__ . "WARNING: Key '$k' not found!");
}
return $retval;
}
 */

