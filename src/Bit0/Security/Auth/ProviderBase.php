<?php
namespace Bit0\Security\Auth
{
    /**
     * UserHandler short summary.
     *
     * UserHandler description.
     *
     * @version 1.0
     * @author Jain
     */
    abstract class ProviderBase
    {
        protected $m_Object = null;
        protected $m_Session = null;
        
        public static function Get($id)
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public static function GetByCookie()
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public static function GetBySession()
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public static function Create($username, $password)
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public static function Validate($username, $password)
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        abstract public function &GetObject();
        abstract public function Update();
        abstract public function Delete();
        abstract public function ChangePassword($password);
        abstract public function Login();
        abstract public function Logout();
    }
}
