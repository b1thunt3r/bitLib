<?php
namespace Bit0\Core;

/**
 * Base short summary.
 *
 * Base description.
 *
 * @version 1.0
 * @author Jain
 */
class BaseClass
{
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }
}
