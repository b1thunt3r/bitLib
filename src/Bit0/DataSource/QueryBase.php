<?php
namespace Bit0\DataSource
{
    /**
     * Query short summary.
     *
     * Query description.
     *
     * @version 1.0
     * @author Jain
     */
    abstract class QueryBase
    {
        protected $m_Model;
        protected $m_DBConn;
        protected $m_Where;
        protected $m_Sort;
        protected $_App;
        
        public function __construct(ModelInfo $modelInfo, AdapterBase &$dbconn)
        {
            $this->m_Model = $modelInfo;
            $this->m_DBConn = $dbconn;
            $this->_App = \Bit0\Core\Context::GetInstance();
        }
        
        public function AddFilter(FieldInfo $fieldInfo, $value, $filter = QueryConstants::Equal, 
            $logical = QueryConstants::LogicalAnd)
        {
            return $this->AddFilterRaw($fieldInfo->FieldName, $value, $filter, $logical);
        }
        abstract public function AddFilterRaw($field, $value, $filter = QueryConstants::Equal, 
            $logical = QueryConstants::LogicalAnd);
        
        public function AddSort(FieldInfo $fieldInfo, $sort = QueryConstants::Ascending)
        {
            return $this->AddSortRaw($fieldInfo->FieldName, $sort);
        }
        abstract public function AddSortRaw($field, $sort = QueryConstants::Ascending);
        
        public function GetEntity() {
            $res = $this->GetEntities(1);
            if (isset($res[0]))
                return $res[0];
            
            return null;
        }
        abstract public function GetEntities($count = 100, $offset = 0);
    }
    
    class QueryConstants
    {
        const LessThanEqual = -2;
        const LessThan = -1;
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