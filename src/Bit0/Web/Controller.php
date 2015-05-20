<?php
namespace Bit0\Web {
  /**
   * Controller short summary.
   *
   * Controller description.
   *
   * @version 1.0
   * @author Jain
   */
  abstract class Controller {
    protected $m_Parameters;
    protected $m_Model = array();
    protected $_App;
    protected $_User;

    public function __construct( $params ) {
      $this->_App  = \Bit0\Core\Context::GetInstance();
      $this->_User = $this->_App->User;

      $this->m_Parameters = $params;

      $this->m_Model['l10n']       = array();
      $this->m_Model['Controller'] = $params['Controller'];
      $this->m_Model['Action']     = $params['View'];
      $this->m_Model['AppTitle']   = $this->_App->Title;
      $this->m_Model['Title']      = $params['Controller'] . ' | ' . $this->_App->Title;
      $this->m_Model['PageId']     = "{$params['Controller']}-{$params['View']}";
      $this->m_Model['AppRoot']    = $this->LivePath( '~' );
      $this->m_Model['ActionPath'] = $this->LivePath( "~{$params['Slug']}" );
      $this->m_Model['PageTitle']  = $this->m_Model['Action'];
    }

    public function Redirect( $path, $code = 302 ) {
      return Router::Redirect( $path, $code );
    }

    public static function LivePath( $path, $absolute = false ) {
      return Router::LivePath( $path, $absolute );
    }

    public static function RealPath( $path ) {
      return Router::RealPath( $path );
    }

    public function View( array $model ) {
      $view = sprintf( '%s/%s.html', $this->m_Parameters['Controller'], $this->m_Parameters['View'] );
      $path = realpath( sprintf( './%s/Views/', $this->m_Parameters['Namespace'] ) ) . '/';

      $template = new \H2o( $view, array(
        'searchpath' => $path,
        'cache_dir'  => $this->_App->RealPath . '/tmp/h20_cache'
      ) );

      new h2o\Filters();

      echo $template->render( $model );

      return null;
    }
  }
}