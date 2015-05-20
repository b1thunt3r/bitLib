<?php
namespace Bit0\Web {
  use Bit0\Exceptions\PathException;

  /**
   * Router short summary.
   *
   * Router description.
   *
   * @version 1.0
   * @author Jain
   */
  class Router {
    protected $m_Param = array();
    protected $m_Slug = '/';
    protected $_App;
    protected $m_ScecurePath = array();
    protected $m_AuthPath = '~/Auth/Login';

    public function __construct() {
      $this->_App = \Bit0\Core\Context::GetInstance();

      $this->InitParam();
    }

    private function InitParam() {
      // Harvest request from URI
      $uri = $_SERVER["REQUEST_URI"];
      if ( $this->_App->LivePath != '/' ) {
        $uri = str_replace( $this->_App->LivePath, '', $_SERVER["REQUEST_URI"] );
      }

      $uri          = preg_match( "/^(.*)\?/", $uri, $uripart ) ? $uripart[1] : $uri;
      $this->m_Slug = $uri = ( substr( $uri, - 1 ) == '/' ) ? substr( $uri, 0, - 1 ) : $uri;

      // Split the reqest in manageble parts
      foreach ( explode( "/", $uri ) as $value ) {
        if ( $value != "" && $value != "index.php" ) {
          $this->m_Param[] = $value;
        }
      }
    }

    public function GetPath() {
      return array( $this->m_Slug, $this->m_Param );
    }

    public static function Redirect( $path, $code = 200 ) {
      HTTPHeader::Status( $code );
      HTTPHeader::Location( self::LivePath( $path ) );
      exit();
    }

    public static function LivePath( $path, $absolute = false ) {
      $_App = \Bit0\Core\Context::GetInstance();
      $live = $_App->LivePath;
      if ( $live == '/' ) {
        $live = '';
      }

      if ( $absolute ) {
        return 'http://' . $_SERVER['HTTP_HOST'] . preg_replace( "/^~/", $live, $path );
      } else {
        return preg_replace( "/^~/", $live, $path );
      }
    }

    public static function RealPath( $path ) {
      return realpath( preg_replace( "/^~/", \Bit0\Core\Context::GetInstance()->RealPath, $path ) );
    }

    public function AddSecurePath( $path ) {
      $this->m_ScecurePath[ $path ] = $path;
    }

    public function SetAuthPath( $path = '~/Auth/Login' ) {
      $this->m_AuthPath = $path . '?ReturnURL=';
    }

    /**
     * Summary of Route
     *
     * @param $namespace
     *
     * @throws \Exception
     * @throws \ReflectionException
     * @internal param $suffix
     *
     */
    public function Route( $namespace ) {
      $url = &$this->m_Param;

      $params['Namespace'] = $namespace;
      $params['Slug']      = $this->m_Slug;
      $params['SlugParts'] = $this->m_Param;

      $url[0] = $params['Controller'] = isset( $url[0] ) ? $url[0] : 'Home';
      $url[1] = $params['View'] = isset( $url[1] ) ? $url[1] : 'Index';

      if (
        isset( $this->m_ScecurePath[ '~/' . $url[0] ] ) ||
        isset( $this->m_ScecurePath[ '~/' . $url[0] . '/' . $url[1] ] )
      ) {
        if ( $this->_App->User == null ) {
          $url = $this->m_Slug . '?' . http_build_query( $_GET );
          $this->Redirect( $this->m_AuthPath . urlencode( $url ), 401 );
        }
      }

      try {
        $this->_App->Logger->Notice( "{$_SERVER['REQUEST_METHOD']}: {$url[0]}/{$url[1]}" );
        switch ( $_SERVER['REQUEST_METHOD'] ) {
          case 'GET':
            if ( count( $_GET ) > 0 ) {
              $this->_App->Logger->Notice( 'GET Data:' . var_export( $_GET, true ) );
            }
            break;
          case 'POST':
            if ( count( $_POST ) > 0 ) {
              if ( isset( $_POST['password'] ) ) {
                $pass = $_POST['password'];
                unset( $_POST['password'] );
              }
              $this->_App->Logger->Notice( 'POST Data:' . var_export( $_POST, true ) );
              if ( isset( $pass ) ) {
                $_POST['password'] = $pass;
              }
            }
            break;
        }

        $rObj = new \ReflectionClass( "\\{$namespace}\\Controllers\\{$url[0]}Controller" );
        if ( $rObj->hasMethod( $url[1] ) ) {
          $controller = $rObj->newInstance( $params );
          $rObj->getMethod( $url[1] )->invokeArgs( $controller, array_slice( $url, 2 ) );
        } else if ( $rObj->hasMethod( 'Handle' ) ) {
          $controller = $rObj->newInstance( $params );
          $rObj->getMethod( 'Handle' )->invoke( $controller, array( $this->m_Slug, $this->m_Param ) );
        } else {
          throw new \Bit0\Exceptions\PathException( "No action: {$url[1]}", 404 );
        }
      } catch ( \ReflectionException $ex ) {
        switch ( $ex->getCode() ) {
          case - 1:
            throw new \Bit0\Exceptions\PathException( "No controller: {$url[0]}", 404, $ex );
            break;
          default:
            throw $ex;
            break;
        }
      }
    }
  }
}