<?php
namespace Bit0\Core {
  /**
   * Class Context
   * @package Bit0\Core
   *
   * @property \Bit0\DataSource\AdapterBase $Database
   * @property \Bit0\Web\Router $Router
   * @property \Bit0\Exceptions\ExceptionHandling $ExceptionHandler
   * @property Buffer $Buffer
   * @property \Bit0\Security\Auth\Providers\DatabaseProvider $User
   * @property \Bit0\IO\Log $Logger
   *
   */
    class Context {
        private static $m_Instance;
        public static function &GetInstance($vendor = null)
        {
            if (!self::$m_Instance)
            {
                self::$m_Instance = new Context();
            }

            if (isset($vendor))
                self::$m_Instance->VendorPath = $vendor;

            return self::$m_Instance;
        }

        private $m_Paths = array();

        public $Name = 'Bit0';

        public $LivePath;
        public $RealPath;
        public $VendorPath;
        public $Domain;
        public $Title;

        public $LogLevel;
        public $Logger;

        public $Database;
        public $User;

        public $Buffer;
        public $ExceptionHandler;
        public $Router;
        

        public function __construct()
        {
            // populate
            $root = explode('/index.php', $_SERVER['PHP_SELF']);

            $this->LivePath = (strlen($root[1]) == 0) ? $root[0] : $root[0].'/index.php';
            if (strlen($this->LivePath) < 1)
                $this->LivePath = '/';
            $this->RealPath = realpath('.');
            $this->Domain = $_SERVER["HTTP_HOST"];
            if ($_SERVER["SERVER_PORT"] != 80)
                $this->Domain .=':'.$_SERVER["SERVER_PORT"];

            $this->VendorPath = realpath(str_replace('phar://', '', dirname(dirname(dirname(__DIR__)))));

            // setup class autoloading
            $this->AddPath(dirname(dirname(__DIR__)));
            spl_autoload_register(array($this, 'Autoload'));

            \Bit0\Web\HTTPHeader::Remove('x-powered-by');
            IoC::GetInstance();
        }

        public function Autoload($class)
        {
            if (!class_exists($class, FALSE)) {
                $path = preg_replace('#\\\|_(?!.*\\\)#',DIRECTORY_SEPARATOR, $class).'.php';
                $file = stream_resolve_include_path($path);

                if ($file !== false)
                    require_once($file);
            }
        }

        public function AddPath($path)
        {
            //$path = realpath($path);
            if (is_string($path) && file_exists($path))
            {
                $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
                $this->m_Paths[] = $path;

                set_include_path(implode(PATH_SEPARATOR, $this->m_Paths));
            }
            else
                throw new \Bit0\Exceptions\PathException('AddPath: Bad argument, needs to a string and a vaild path');
        }

        public function AddPhar($phar)
        {
            $path = 'phar://' . $this->VendorPath . DIRECTORY_SEPARATOR . $phar . '.phar';

            if (is_string($path) && file_exists($path))
                $this->AddPath($path);
            else
                throw new \Bit0\Exceptions\PathException('AddPhar: Bad argument, needs to a string and a valid path');
        }

        public function SetupLogger()
        {
            if ($this->LogLevel > 0)
                $this->Logger = new \Bit0\IO\Log($this->Title, true);
        }

        public function InitWebContext()
        {
            \session_start();
            $this->Buffer = new Buffer();
            //$this->ExceptionHandler = new \Bit0\Exceptions\ExceptionHandling($this->Buffer);
            $this->Router = new \Bit0\Web\Router();
            //$this->AddPhar('h2o');
        }
    }
}
