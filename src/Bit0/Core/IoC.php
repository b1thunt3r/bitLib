<?php
namespace Bit0\Core {


    class IoC
    {
        private static $m_Instance;
        public static function &GetInstance()
        {
            if (!self::$m_Instance)
            {
                self::$m_Instance = new IoC();
            }

            return self::$m_Instance;
        }

        private $Registry = array();

        public function __set($name, \Closure $closer) {
            $this->Register($name, $closer);
        }

        public function __get($name) {
            return $this->Resolve($name);
        }

        public function Register($name, \Closure $closure) {
            $this->Registry[$name] = $closure;
        }

        public function Resolve($name) {
            if ($this->IsRegistered($name)) {
                $name = $this->Registry[$name];
                return $name();
            }

            throw new \Bit0\Exceptions\NoMethodException("{$name} is not a registered method in IoC factory.");
        }

        public function IsRegistered($name) {
            return array_key_exists($name, $this->Registry);
        }

    }
}