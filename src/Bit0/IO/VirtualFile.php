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
    class VirtualFile
    {
        private $m_Filename;
        
        public function __construct($filename)
        {
            $this->m_Filename = $filename;
        }
        
        public function DownloadFile($data, $mime = 'application/octet-stream')
        {
            //HTTPHeader::AcceptRanges();
            //HTTPHeader::KeepAlive();
            //HTTPHeader::Connection();
            
            $length = strlen($data);
            $lastmodified = gmdate('D, d M Y H:i:s', time()) . ' GMT';
            $etag = sha1($lastmodified.$length);
            
            HTTPHeader::ETag($etag);
            HTTPHeader::ContentLength($length);
            HTTPHeader::ContentType($mime);
            HTTPHeader::LastModified($lastmodified);
            HTTPHeader::ContentDisposition($this->m_Filename);
                
            echo $data;
        }
    }
}