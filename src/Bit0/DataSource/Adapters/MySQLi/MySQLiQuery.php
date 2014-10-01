<?php
namespace Bit0\DataSource\Adapters\MySQLi
{
    use Bit0\DataSource\QueryBase;
    use Bit0\DataSource\QueryConstants;
    use Bit0\DataSource\ModelInfo;

    class MySQLiQuery extends QueryBase
    {
        public function AddFilterRaw($field, $value, $filter = QueryConstants::Equal, 
            $logical = QueryConstants::LogicalAnd)
        {
            $value = $this->m_DBConn->EscapeString($value);
            if (is_string($value))
                $q = '`%2$s` %1$s \'%3$s\'';
            else
                $q = '`%2$s` %1$s %3$s';
            switch ($filter)
            {
                case QueryConstants::LessThanEqual:
                    $q = sprintf($q, '<=', $field, $value);
                    break;
                case QueryConstants::LessThan:
                    $q = sprintf($q, '<', $field, $value);
                    break;
                default:
                case QueryConstants::Equal:
                    $q = sprintf($q, '=', $field, $value);
                    break;
                case QueryConstants::NotEqual:
                    $q = sprintf($q, '!=', $field, $value);
                    break;
                case QueryConstants::GreaterThan:
                    $q = sprintf($q, '>', $field, $value);
                    break;
                case QueryConstants::GreaterThanEqual:
                    $q = sprintf($q, '>=', $field, $value);
                    break;
                case QueryConstants::Like:
                    $q = sprintf($q, 'LIKE', $field, $value);
                    break;
                case QueryConstants::RegExp:
                    $q = sprintf($q, 'REGEXP', $field, $value);
                    break;
            }
            
            if(strlen($this->m_Where) == 0)
            {
                $this->m_Where = "WHERE {$q}";
            }
            else
            {
                switch ($logical)
                {
                    case QueryConstants::LogicalOr:
                        $this->m_Where .= " OR {$q}";
                        break;
                    default:
                        $this->m_Where .= " AND {$q}";
                        break;
                }
                
            }
            
            return $this;
        }
        
        public function AddSortRaw($field, $sort = QueryConstants::Ascending)
        {
            switch ($sort)
            {
                case QueryConstants::Descending:
                    $q = "`{$field}` DESC";
                    break;
                case QueryConstants::Ascending:
                default:
                    $q = "`{$field}` ASC";
                    break;
            }
            
            if(strlen($this->m_Sort) == 0)
            {
                $this->m_Sort = "ORDER BY {$q}";
            }
            else
            {
                $this->m_Sort = ", {$q}";
            }
            
            return $this;
        }
                
        public function GetEntities($count = 100, $offset = 0)
        {
            $query = "SELECT * FROM `{$this->m_DBConn->GetPrefix()}{$this->m_Model->TableName}` {$this->m_Where} {$this->m_Sort} LIMIT {$offset}, {$count};";
            $result = $this->m_DBConn->Execute($query);
            
            $res = array();
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $fields = array();
                    foreach($this->m_Model->FieldList as $PropertyName => $field)
                    {
                        $fields[$field->FieldName] = array($PropertyName, $field->Relation != null);
                    }
                    
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    
                    foreach($rows as $row)
                    {
                        $o = new $this->m_Model->ClassName;
                        foreach ($row as $col => $value)
                        {
                            if (!$fields[$col][1])
                                $o->$fields[$col][0] = $value;
                            else
                            {                            
                                $o->$fields[$col][0] = 
                                    $this->m_DBConn->CreateQuery($this->m_Model->FieldList[$fields[$col][0]]->Relation)
                                                        ->AddFilterRaw($this->m_Model->FieldList[$fields[$col][0]]->RelationFieldName, $value)
                                                        ->GetEntity();
                            }
                        }
                        
                        $res[] = $o;
                    }
                    
                    $result->close();
                }
            }
            
            $this->_App->Logger->Notice("\tResult: ".count($res));
            
            return $res;
        }
    }
}