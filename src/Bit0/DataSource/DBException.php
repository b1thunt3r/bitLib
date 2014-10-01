<?php
namespace Bit0\DataSource
{
    /**
     * DBException short summary.
     *
     * DBException description.
     *
     * @version 1.0
     * @author Jain
     */
    class DBException extends \Exception
    {
        function __construct($message = '', $code = 0, $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
        
    }
}