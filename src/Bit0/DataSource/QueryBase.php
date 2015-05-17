<?php
namespace Bit0\DataSource {
  /**
   * Query short summary.
   *
   * Query description.
   *
   * @version 1.0
   * @author Jain
   */
  abstract class QueryBase {
    protected $m_Model;
    protected $m_DBConn;
    protected $m_Where;
    protected $m_Sort;
    protected $_App;

    public function __construct( ModelInfo $modelInfo, AdapterBase &$dbconn ) {
      $this->m_Model  = $modelInfo;
      $this->m_DBConn = $dbconn;
      $this->_App     = \Bit0\Core\Context::GetInstance();
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param $value
     * @param int $filter
     *
     * @return QueryBase
     */
    public function AndFilter( FieldInfo $fieldInfo, $value, $filter ) {
      return $this->AddFilterRaw( $fieldInfo->FieldName, $value, $filter, QueryConstants::LogicalAnd );
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param $value
     * @param int $filter
     *
     * @return QueryBase
     */
    public function OrFilter( FieldInfo $fieldInfo, $value, $filter ) {
      return $this->AddFilterRaw( $fieldInfo->FieldName, $value, $filter, QueryConstants::LogicalOr );
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param $value
     *
     * @return QueryBase
     */
    public function AndEqual( FieldInfo $fieldInfo, $value ) {
      return $this->AddFilterRaw( $fieldInfo->FieldName, $value, QueryConstants::Equal, QueryConstants::LogicalAnd );
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param $value
     *
     * @return QueryBase
     */
    public function OrEqual( FieldInfo $fieldInfo, $value ) {
      return $this->AddFilterRaw( $fieldInfo->FieldName, $value, QueryConstants::Equal, QueryConstants::LogicalOr );
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param $value
     * @param int $filter
     * @param int $logical
     *
     * @return QueryBase
     */
    public function AddFilter(
      FieldInfo $fieldInfo, $value, $filter = QueryConstants::Equal,
      $logical = QueryConstants::LogicalAnd
    ) {
      return $this->AddFilterRaw( $fieldInfo->FieldName, $value, $filter, $logical );
    }

    /**
     * @param string $field
     * @param $value
     * @param int $filter
     * @param int $logical
     *
     * @return QueryBase
     */
    abstract public function AddFilterRaw(
      $field, $value, $filter = QueryConstants::Equal,
      $logical = QueryConstants::LogicalAnd
    );

    /**
     * @param FieldInfo $fieldInfo
     *
     * @return QueryBase
     */
    public function OrderBy( FieldInfo $fieldInfo ) {
      return $this->AddSortRaw( $fieldInfo->FieldName, QueryConstants::Ascending );
    }

    /**
     * @param FieldInfo $fieldInfo
     *
     * @return QueryBase
     */
    public function OrderByDescending( FieldInfo $fieldInfo ) {
      return $this->AddSortRaw( $fieldInfo->FieldName, QueryConstants::Descending );
    }

    /**
     * @param FieldInfo $fieldInfo
     * @param int $sort
     *
     * @return QueryBase
     */
    public function AddSort( FieldInfo $fieldInfo, $sort = QueryConstants::Ascending ) {
      return $this->AddSortRaw( $fieldInfo->FieldName, $sort );
    }

    /**
     * @param string $field
     * @param int $sort
     *
     * @return QueryBase
     */
    abstract public function AddSortRaw( $field, $sort = QueryConstants::Ascending );

    public function GetEntity() {
      $res = $this->GetEntities( 1 );
      if ( isset( $res[0] ) ) {
        return $res[0];
      }

      return null;
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return array
     */
    abstract public function GetEntities( $count = 100, $offset = 0 );
  }

  class QueryConstants {
    const LessThanEqual = - 2;
    const LessThan = - 1;
    const Equal = 0;
    const NotEqual = 3;
    const GreaterThan = 1;
    const GreaterThanEqual = 2;
    const Like = 4;
    const RegExp = 5;

    const Ascending = 11;
    const Descending = 12;

    const LogicalAnd = 21;
    const LogicalOr = 22;
  }
}