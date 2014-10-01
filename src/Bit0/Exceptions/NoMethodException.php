<?php
namespace Bit0\Exceptions
{
    /**
     * PathException short summary.
     *
     * PathException description.
     *
     * @version 1.0
     * @author Jain
     */
    class NoMethodException extends \Exception
    {
        public function __construct($message = 'Method is not implemented!', $code = 500, $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
}