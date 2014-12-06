<?php
namespace Bit0\Exceptions {
  /**
   * PathException short summary.
   *
   * PathException description.
   *
   * @version 1.0
   * @author Jain
   */
  class CreateFileException extends \Exception {
    public function __construct( $message = '', $code = 500, \Exception $previous = null ) {
      parent::__construct( $message, $code, $previous );
    }
  }
}