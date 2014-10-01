<?php
namespace Bit0\Security
{
    /**
     * Guid short summary.
     *
     * Guid description.
     *
     * @version 1.0
     * @author Jain
     */
    class Guid
    {
        private $hash;
        
        public function __construct($salt = '') {
            $uid = uniqid($salt, true);
            $data  = $uid;
            $data .= $salt;
            $data .= microtime();
            $data .= rand(0, rand());
            $data .= rand(0, rand());
            $data .= rand(0, rand());
            $this->hash = strtoupper(hash('ripemd128', $uid . sha1($data)));
        }
        
        public function ToString($format = 'D')
        {
            switch ($format)
            {
                case 'N':
                    return $this->hash; 
                    break;
                case 'D':
                    return sprintf(
                        "%s-%s-%s-%s-%s", 
                        substr($this->hash,  0,  8),
                        substr($this->hash,  8,  4),
                        substr($this->hash, 12,  4),
                        substr($this->hash, 16,  4),
                        substr($this->hash, 20, 12));;
                    break;
                case 'B':
                default:
                    return "{" . $this->ToString() . "}";
                    break;
                case 'P':
                    return "(" . $this->ToString() . ")";
                    break;
            }
        }
        
        public function __toString() {
            return $this->ToString('D');
        }
    }
}