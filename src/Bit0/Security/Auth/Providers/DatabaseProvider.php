<?php
namespace Bit0\Security\Auth\Providers
{
    /**
     * Database short summary.
     *
     * Database description.
     *
     * @version 1.0
     * @author Jain
     */
    abstract class DatabaseProvider extends \Bit0\Security\Auth\ProviderBase
    {
        public function &GetObject()
        {
            if ($this->m_Object != null)
                return $this->m_Object;
            
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public function __construct($userObject)
        {
            $this->m_Object = $userObject;
        }
        
        public function Delete()
        {
            $this->m_Object->Delete();
        }
        
        public function Update()
        {
            $id = $this->m_Object->Save();
            
            if ($id != null)
                return self::Get($id);
            
            return null;
        }
    }
}