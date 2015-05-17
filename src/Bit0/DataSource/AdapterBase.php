<?php
namespace Bit0\DataSource {

  abstract class AdapterBase {
    protected $m_Prefix = '';
    protected $m_DBObject = null;

    abstract public function __construct( $host, $username, $password, $database, $prefix = '' );

    abstract public function __destruct();

    public function GetPrefix() {
      return $this->m_Prefix;
    }

    abstract public function EscapeString( $string );

    abstract public function Save( ModelBase $model, $updateRelations = false );

    abstract public function SaveAll( array $models );

    abstract protected function Create( ModelBase $model, $updateRelations = false );

    abstract protected function Update( ModelBase $model, $updateRelations = false );

    public function GetEntities( ModelInfo $modelInfo, $count = 100, $offset = 0 ) {
      return $this->CreateQuery( $modelInfo )->GetEntities( $count, $offset );
    }

    public function GetEntityById( ModelInfo $modelInfo, $id ) {
      return $this->CreateQuery( $modelInfo )->AddFilterRaw( $modelInfo->IdFieldName, $id )->GetEntity();
    }

    /**
     * @param ModelInfo $modelInfo
     *
     * @return QueryBase
     */
    abstract public function CreateQuery( ModelInfo $modelInfo );

    abstract public function Delete( ModelBase $model );

    abstract public function Execute( $query );
  }
}