<?php
namespace Bit0\Exceptions {
    /**
     * ExceptionHandling short summary.
     *
     * ExceptionHandling description.
     *
     * @version 1.0
     * @author Jain
     */
    class ExceptionHandling
    {
        private $m_OldHandlers = array();
        private $m_Call = null;
        private $m_Buffer = null;
        
        public function __construct(\Bit0\Core\Buffer &$buffer, callable $call = null)
        {
            $this->m_Buffer = $buffer;
            $this->SetCustomCall($call);
            
            $this->m_OldHandlers['Exception'] = set_exception_handler(array($this, 'ExceptionHandler'));
            $this->m_OldHandlers['Error']     = set_error_handler(array($this, 'ErrorHandler'), E_ALL);
        }
        
        public function SetCustomCall(callable $call = null)
        {
            $this->m_Call = $call;
        }
        
        public function ExceptionHandler($exception) 
        {
            if ($this->m_Call != null)
                call_user_func($this->m_Call, $exception);
            
            $name = (new \ReflectionObject($exception))->getName();
            
            switch ($name)
            {
                case 'Bit0\Exceptions\PathException':
                    \Bit0\Web\HTTPHeader::Status($exception->getCode());
                    break;
                default:
                    \Bit0\Web\HTTPHeader::Status(500);
                    break;
            }
            
            $this->m_Buffer->Clean();
            echo '<pre>';
            var_dump(
                $name,
                $exception->getMessage(),
                $exception->getTrace()[0],
                $exception->getTrace()
            );
            echo '</pre>';
            $this->m_Buffer->Close();
            \Bit0\Core\Context::GetInstance()->Logger->Error($name.': '.$exception->getMessage());
            \Bit0\Core\Context::GetInstance()->Logger->Error($exception->getTrace());
        }
        
        public function ErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
            unset($errcontext['GLOBAL'], $errcontext['_GET'], $errcontext['_POST'], $errcontext['_COOKIE']);
            unset($errcontext['_FILES'], $errcontext['_ENV'], $errcontext['_REQUEST'], $errcontext['_SERVER']);
            
            $l = error_reporting();
            if ( $l & $errno ) {
               
                $exit = false;
                switch ( $errno ) {
                    case E_USER_ERROR:
                        $type = 'Fatal Error';
                        $exit = true;
                    break;
                    case E_USER_WARNING:
                    case E_WARNING:
                        $type = 'Warning';
                    break;
                    case E_USER_NOTICE:
                    case E_NOTICE:
                    case @E_STRICT:
                        $type = 'Notice';
                    break;
                    case @E_RECOVERABLE_ERROR:
                        $type = 'Catchable';
                    break;
                    default:
                        $type = 'Unknown Error' ;
                        $exit = true;
                    break;
                }
               
                $exception = new \ErrorException($type.': '.$errstr, 0, $errno, $errfile, $errline);
               
                if ( $exit ) {
                    $this->ExceptionHandler($exception);
                    exit();
                }
                else
                    throw $exception;
            }
            return false;
        }        
    }
}