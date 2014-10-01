<?php
namespace Bit0\DataSource
{
    /**
     * Model short summary.
     *
     * Model description.
     *
     * @version 1.0
     * @author Jain
     */
    abstract class ModelBase
    {
        public function GetId()
        {
            $idP = static::GetInfo()->IdPropertyName;
            return $this->{$idP};
        }
        public static function GetInfo()
        {
            return new ModelInfo(null, null);
        }

        public function Save($updateRelations = false)
        {
            return \Bit0\Core\Context::GetInstance()->Database->Save($this, $updateRelations);
        }

        public function Delete()
        {
            return \Bit0\Core\Context::GetInstance()->Database->Delete($this);
        }

        public static function FindByFieldRaw($field, $value)
        {
            return \Bit0\Core\Context::GetInstance()->Database->CreateQuery(static::GetInfo())
                 ->AddFilterRaw($field, $value)->GetEntity();
        }

        public static function Find()
        {
            return \Bit0\Core\Context::GetInstance()->Database->CreateQuery(static::GetInfo());
        }

        public static function FindAll($count = 100, $offset = 0)
        {
            return \Bit0\Core\Context::GetInstance()->Database->CreateQuery(static::GetInfo())
                ->GetEntities($count, $offset);
        }

        public static function FindByID($id)
        {
            $info = static::GetInfo();
            return \Bit0\Core\Context::GetInstance()->Database->CreateQuery($info)
                 ->AddFilterRaw($info->IdFieldName, $id)->GetEntity();
        }

        public static function FindByField($fieldInfo, $value)
        {
            return \Bit0\Core\Context::GetInstance()->Database->CreateQuery(static::GetInfo())
                 ->AddFilter($fieldInfo, $value)->GetEntity();
        }
    }

    class ModelInfo
    {
        public $ClassName = '';
        public $TableName = '';
        public $FieldList = array();
        public $IdFieldName = '';
        public $IdPropertyName = '';

        public function __construct($class, $table, $idField = 'id', $idProperty = 'Id')
        {
            $this->ClassName = $class;
            $this->TableName = $table;
            $this->IdFieldName = $idField;
            $this->IdPropertyName = $idProperty;
        }
    }

    class FieldInfo
    {
        public $PropertyName = '';
        public $FieldName = '';
        public $Relation = null;
        public $RelationFieldName = '';
        public $Value = null;

        public function __construct($property, $field, ModelInfo $relation = null, $relationField = 'id')
        {
            $this->FieldName = $field;
            $this->PropertyName = $property;
            $this->Relation = $relation;
            $this->RelationFieldName = $relationField;
        }
    }
}