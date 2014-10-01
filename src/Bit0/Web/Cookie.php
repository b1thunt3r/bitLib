<?php
namespace Bit0\Web
{
    /**
     * Cookie short summary.
     *
     * Cookie description.
     *
     * @version 1.0
     * @author Jain
     */
    class Cookie
    {
        public static function Create($name, $value = 'NoData', $expire = null)
        {
            if ($expire == null)
                $expire = time() + 60*60*24*365;
            
            $_APP = \Bit0\Core\Context::GetInstance();
            
            if(setcookie($name, $value, $expire, '/'))
            {
                $cookie = self::Get($name);
                return $cookie;
            }
            
            return null;
        }
        
        public static function Get($name)
        {
            if(isset($_COOKIE[$name]))
                return new Cookie($name);
            
            return null;
        }
        
        private $m_Name;
        
        public function __construct($name)
        {
            $this->m_Name = $name;
        }
        
        public function Delete()
        {
            $this->Update($this->m_Name, '', 1);
        }
        
        public function Update($value, $expire)
        {
            setcookie($this->m_Name, $value, $expire);
        }
        
        public function Value()
        {
            return $_COOKIE[$this->m_Name];
        }
    }
}