<?php
namespace Bit0\IO
{
    use Bit0\Web\HTTPHeader;
    /**
     * FileInfo short summary.
     *
     * FileInfo description.
     *
     * @version 1.0
     * @author Jain
     */
    class File
    {
        private $m_MimeList = array(
            'css' => 'text/css',
            'js' => 'text/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'ico' => 'image/x-icon',
        );
        
        private $m_FilePath;
        private $m_Basename;
        private $m_Dirname;
        private $m_Filename;
        private $m_Extension;
        private $m_Length;
        private $m_MimeType;
        private $m_LastModified;
        private $m_ETag;
        private $m_Handler = false;
        private $m_Stat;
        private $m_ExpireSeconds;
        private $m_Expires;
        
        public function __construct($filepath, $create = false)
        {
            $this->m_ExpireSeconds = 60*60*24*12;
            $this->m_Expires = gmdate('D, d M Y H:i:s', time()+$this->GetExpireSeconds()) . ' GMT';


            if(file_exists($filepath) && is_file($filepath))
            {
                $this->Info($filepath);
            }
            else if ($create)
            {
                if (touch($filepath)) {
                    $this->Info($filepath);
                } else {
                    throw new \Bit0\Exceptions\PathException('File Not Found', 404);
                }
            }
            else
            {
                throw new \Bit0\Exceptions\PathException('File Not Found', 404);
            }
        }
        
        private function Info($filepath)
        {
            $file = pathinfo($filepath);
            
            $this->m_Extension = $file['extension'];
            $this->m_Basename = $file['basename'];
            $this->m_Dirname = $file['dirname'];
            $this->m_Filename = $file['filename'];
            $this->m_FilePath = $filepath;
            
            $this->Open();
            $this->m_Stat = array_slice(fstat($this->m_Handler), 13);
            $this->Close();
            
            $this->m_Length = $this->m_Stat['size'];
            $this->m_LastModified = gmdate('D, d M Y H:i:s', $this->m_Stat['mtime']) . ' GMT';
            $this->m_ETag = sha1($this->m_LastModified.$this->m_Length.serialize($this->m_Stat));
            
            if (array_key_exists($this->m_Extension, $this->m_MimeList))
                $this->m_MimeType = $this->m_MimeList[$this->m_Extension];
            else
                $this->m_MimeType = 'application/octet-stream';
        }
        
        public function GetMimeType()
        {
            return $this->m_MimeType;
        }
        
        public function GetFilename()
        {
            return $this->m_Filename;
        }
        
        public function GetBasename()
        {
            return $this->m_Basename;
        }
        
        public function GetExtension()
        {
            return $this->m_Extension;
        }
        
        public function GetDirname()
        {
            return $this->m_Dirname;
        }
        
        public function GetLength()
        {
            return $this->m_Length;
        }
        
        public function GetLastModified()
        {
            return $this->m_LastModified;
        }
        
        public function GetFilePath()
        {
            return $this->m_FilePath;
        }
        
        public function GetETag()
        {
            return $this->m_ETag;
        }
        
        public function GetExpireSeconds()
        {
            return $this->m_ExpireSeconds;
        }
        
        public function GetExpires( )
        {
            return $this->m_Expires;
        }
        
        
        public function DownloadFile($force = false)
        {
            //HTTPHeader::AcceptRanges();
            //HTTPHeader::KeepAlive();
            //HTTPHeader::Connection();
            
            HTTPHeader::ETag($this->GetETag());
            HTTPHeader::LastModified($this->GetLastModified());
            HTTPHeader::Expires($this->GetExpires());
            HTTPHeader::CacheControl($this->GetExpireSeconds(), true);
            HTTPHeader::Pragma("public");
            
            if ($this->IsModified())
            {
                HTTPHeader::ContentType($this->GetMimeType());
                HTTPHeader::ContentLength($this->GetLength());
                if ($force)
                    HTTPHeader::ContentDisposition($this->GetBasename());
                
                echo $this->Read();
                exit();
            }
            else
            {
                HTTPHeader::Status(304);
            }
        }
        
        public function IsModified()
        {
            
            if(empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) || empty($_SERVER['HTTP_IF_NONE_MATCH']))
                return true;
            return !(($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $this->GetLastModified()) 
                && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->GetETag()));
        }
        
        private function Open($mode = 'r')
        {
            $this->m_Handler = fopen($this->m_FilePath, $mode);
        }
        
        private function Close()
        {
            fclose($this->m_Handler);
        }
        
        public function Read($buffer = 8192, $mode = 'r')
        {
            $this->Open($mode);
            $contents = '';
            while (!feof($this->m_Handler)) {
                $contents .= fread($this->m_Handler, $buffer);
            }
            $this->Close();
            
            return $contents;
        }
        
        public function Write($buffer, $mode = 'w')
        {
            $this->Open($mode);
            $stat = (fwrite($this->m_Handler, $buffer, strlen($buffer)) === false);
            $this->Close();
            
            return !$stat;
        }
        
        public function Seek($offset = 0)
        {
            fseek($this->m_Handler, $offset);
        }
        
        public function Cursor()
        {
            return ftell($this->m_Handler);
        }
        
        public function Stat()
        {
            return $this->m_Stat;
        }
    }
}