<?php
namespace Bit0\Security\Auth
{
    /**
     * Session short summary.
     *
     * Session description.
     *
     * @version 1.0
     * @author Jain
     */
    abstract class Session
    {
        protected $m_Object;
        protected $m_DB;
        
        public static function Get($id)
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public static function Create($id)
        {
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public function __construct(\Bit0\DataSource\ModelBase $sessionObject)
        {
            $this->m_Object = $sessionObject;
            $this->m_DB = \Bit0\Core\Context::GetInstance()->Database;
        }
        
        public function &GetObject()
        {
            if ($this->m_Object != null)
                return $this->m_Object;
            
            throw new \Bit0\Exceptions\NoMethodException();
        }
        
        public function Delete()
        {
            $this->m_DB->Delete($this->m_Object);
        }
        
        public function Update()
        {
            $id = $this->m_DB->Database->Save($this->m_Object);
            
            if ($id != null)
                return self::Get($id);
            
            return null;
        }
    }
}