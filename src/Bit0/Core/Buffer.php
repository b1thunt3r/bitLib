<?php
namespace Bit0\Core
{
    /**
     * Buffer short summary.
     *
     * Buffer description.
     *
     * @version 1.0
     * @author Jain
     */
    class Buffer
    {
        private $m_OldContent;
        private $m_Content;
        
        public function __construct()
        {
            $this->m_OldContent = ob_get_clean();
            ob_start();
        }
        
        public function GetContent()
        {
            return ob_get_contents();
        }
        
        public function Clean()
        {
            return ob_get_clean();
        }
        
        public function Flush()
        {
            return ob_get_flush();
        }
        
        public function Level()
        {
            return ob_get_level();
        }
        
        public function Status()
        {
            return ob_get_status();
        }
        
        public function Length()
        {
            return ob_get_length();
        }
        
        public function Close($flush = true)
        {
            if($this->Length() > 0)
            {
                if ($flush)
                    ob_end_flush();
                else
                    ob_end_clean();
            }
        }
    }
}