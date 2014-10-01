<?php
namespace Bit0\Web {
    /**
     * HTTPHeader short summary.
     *
     * HTTPHeader description.
     *
     * @version 1.0
     * @author Jain
     */
    class HTTPHeader
    {
        public static function Status($code = 200)
        {
            $codes = array(
                200 => 'OK',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Others',
                304 => 'Not Modified',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                503 => 'Service Unavailable',
            );

            if (!array_key_exists($code, $codes))
                $code = 200;

            self::Add("{$_SERVER['SERVER_PROTOCOL']} {$code} {$codes[$code]}");
        }

        public static function ETag($etag)
        {
            self::Add('ETag', $etag);
        }

        public static function ContentLength($length)
        {
            self::Add('Content-Length', $length);
        }

        public static function ContentType($type)
        {
            self::Add('Content-Type', $type);
        }

        public static function LastModified($date)
        {
            self::Add('Last-Modified', $date);
        }

        public static function Expires($date)
        {
            self::Add('Expires', $date);
        }

        public static function CacheControl($maxAge, $revalidate = false)
        {
            $revalidate = $revalidate ? 'must-revalidate' : '';
            self::Add('Cache-Control', "maxage={$maxAge}, {$revalidate}");
        }

        public static function Pragma($pragma)
        {
            self::Add('Pragma', $pragma);
        }

        public static function ContentDisposition($filename)
        {
            self::Add('Content-Disposition', "attachment; filename={$filename}");
        }

        public static function AcceptRanges($type = 'bytes')
        {
            self::Add('Accept-Ranges', $type);
        }

        public static function KeepAlive($timeout = 5, $max = 99)
        {
            self::Add('Keep-Alive', "timeout={$timeout}, max={$max}");
        }

        public static function Connection($keepAlive = true)
        {
            if ($keepAlive)
                self::Add('Connection', 'Keep-Alive');
        }

        public static function Location($url)
        {
            self::Add('Location', $url);
        }

        public static function Add($header, $value = null)
        {
            if (isset($value))
                header("{$header}: {$value}");
            else
                header($header);
        }

        public static function Remove($header)
        {
            header_remove($header);
        }
    }
}