<?php
namespace Bit0\DataSource\Adapters\MySQLi
{
    use Bit0\DataSource\AdapterBase;
    use Bit0\DataSource\ModelBase;
    use Bit0\DataSource\ModelInfo;
    use Bit0\DataSource\DBException;

    class MySQLiAdapter extends AdapterBase
    {
        public function __construct($host, $username, $password, $database, $prefix = '', $port = 3306)
        {
            $this->m_Prefix = $prefix;
            $this->m_DBObject = new \mysqli($host, $username, $password, $database);
            $this->m_DBObject->set_charset("utf8");
        }

        public function __destruct()
        {
            $this->m_DBObject->close();
        }

        public function Save(ModelBase $model, $updateRelations = false)
        {
            if ($model->GetId() == null)
                return $this->Create($model, $updateRelations);
            else if($model->GetId() != null)
                return $this->Update($model, $updateRelations);
        }

        public function SaveAll(array $models)
        {
            $this->m_DBObject->autocommit(false);

            foreach ($models as $model)
            {
                $this->Save($model);
            }

            $this->m_DBObject->commit();
            $this->m_DBObject->autocommit(true);
        }


        protected function Create(ModelBase $model, $updateRelations = false)
        {
            $info = $model->GetInfo();
            $fields = array();
            $values = array();

            foreach($info->FieldList as $PropertyName => $field)
            {
                if ($info->IdFieldName != $field->FieldName)
                {
                    $property = $model->$PropertyName;
                    $fields[] = "`{$field->FieldName}`";

                    if ($field->Relation == null)
                        $values[] = $this->EscapeString($property, true);
                    else if ($field->Relation != null) {
                        if ($updateRelations)
                            $values[] = $property !== null ? $this->Save($property, $updateRelations) : 'NULL';
                        else
                            $values[] = isset($property) ? $property->GetId() : 'NULL';
                    }
                }
            }

            $query = sprintf("INSERT INTO `%s%s` (%s) VALUES(%s);",
                $this->m_Prefix, $info->TableName, implode(', ', $fields), implode(', ', $values));

            if(!$result = $this->Execute($query))
            {
                throw new \Bit0\DataSource\DBException($this->m_DBObject->error, $this->m_DBObject->errno);
                return -1;
            }
            else
                return $this->m_DBObject->insert_id;
        }

        protected function Update(ModelBase $model, $updateRelations = false)
        {
            $info = $model->GetInfo();
            $values = array();

            foreach($info->FieldList as $PropertyName => $field)
            {
                $property = $model->$PropertyName;
                
                if ($info->IdFieldName != $field->FieldName && $property !== null)
                {
                    if ($field->Relation == null)
                        $values[] = "`{$field->FieldName}` = {$this->EscapeString($property, true)}";
                    else if ($field->Relation != null)
                        if ($updateRelations)
                            $values[] = "`{$field->FieldName}` = {$this->Save($property, $updateRelations)}";
                        else
                            $values[] = "`{$field->FieldName}` = {$property->GetId()}";
                }
            }

            $query = sprintf("UPDATE `%s%s` SET %s WHERE `%s` = %s",
                $this->m_Prefix, $info->TableName, 
                implode(', ', $values), $info->IdFieldName, $model->GetId());

            if($this->Execute($query))
                return $model->GetId();
            else
                return -1;
        }

        public function CreateQuery(ModelInfo $modelInfo)
        {
            return new MySQLiQuery($modelInfo, $this);
        }

        public function Delete(ModelBase $model, $withRelated = false)
        {
            $info = $model->GetInfo();
            foreach($info->FieldList as $PropertyName => $field)
            {
                if ($field->Relation != null && $withRelated)
                {
                    $this->Delete($model->$PropertyName, $withRelated);
                }
            }

            $query = sprintf("DELETE FROM `%s%s` WHERE `%s` = %s",
                $this->m_Prefix, $info->TableName, $info->IdFieldName, $model->GetId());

            $this->Execute($query);
        }

        public function EscapeString($value, $quote = false)
        {
            $value = trim($value);
            /*str_replace(
            array('\x00', '\n', '\r', '\\', '\'', '"', '\x1a'),
            array('\\x00', '\\n', '\\r', '\\\\', '\\\'', '\"', '\\x1a'),
            htmlspecialchars(trim($str)));*/
            if (is_string($value))
            {
                $value = $this->m_DBObject->real_escape_string($value);
                return $quote ? "'{$value}'" : $value;
            }
            return $value;
        }

        public function Execute($query)
        {
            \Bit0\Core\Context::GetInstance()->Logger->Notice('Q: '.$query);
            $result = $this->m_DBObject->query($query);
            if ($result)
                \Bit0\Core\Context::GetInstance()->Logger->Notice("\tSuccess");
            return $result;
        }
    }
}