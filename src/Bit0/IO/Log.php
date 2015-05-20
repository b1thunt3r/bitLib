<?php
namespace Bit0\IO {
  use Bit0\Exceptions\CreateFileException;

  /**
   * Log short summary.
   *
   * Log description.
   *
   * @version 1.0
   * @author Jain
   */
  class Log {
    private $m_File;
    private $m_Level = E_ALL;

    public function __construct( $filename ) {
      $app = \Bit0\Core\Context::GetInstance();

      $this->m_Level = $app->LogLevel;

      $date         = date( 'Ymd-H' );
      $this->m_File = new File( "{$app->RealPath}/tmp/logs/{$filename}_{$date}.log", true );
    }

    private function Log( $text, $level ) {
      $this->m_File->Write( sprintf( "[%s][%s][%s] %s\r\n",
        date( 'c' ), $level, $_SERVER["REMOTE_ADDR"], $text ), 'a+' );
    }

    public function Error( $text ) {
      if ( $this->m_Level >= E_ERROR ) {
        $this->Log( $text, 'ERROR' );
      }
    }

    public function Warning( $text ) {
      if ( $this->m_Level >= E_WARNING ) {
        $this->Log( $text, 'WARNING' );
      }
    }

    public function Notice( $text ) {
      if ( $this->m_Level >= E_NOTICE ) {
        $this->Log( $text, 'NOTICE' );
      }
    }
  }
}