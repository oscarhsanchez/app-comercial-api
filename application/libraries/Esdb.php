<?php

class Esdb {

    private $qEntityTables;
    private $qEntities;
    private $qPkValues;

    private $limit;
    private $offset;
    private $main_table;

    function __construct()
    {
        $this->ci =& get_instance();
        $this->qEntityTables = array();
        $this->qEntities = array();
        $this->qPkValues = array();
    }

    function select($fields, $entity=null) {
        $fieldsArr = explode(",", $fields);
        $class = new \ReflectionClass($entity);
        $obj = $class->newInstance();
        $properties = $class->getProperties();

        $this->qEntityTables[] = $obj->getTableName();
        end($this->qEntityTables);
        $index = key($this->qEntityTables);

        $this->qEntities[$entity] = $index;

        foreach ($properties as $property) {
            $name = $property->getName();

            $params = array($entity, $name, 'property');
            $entityReader = new Reader($params);

            $relation = $entityReader->getParameter("ORM\Relation");

            if (!$relation && ($fields == "*" || in_array($name, $fieldsArr) || $name == $obj->getPK())) {
                $this->ci->db->select($obj->getTableName() . "." . $name . " AS " . $name . "_t" . $index);
            }
        }

        return $this->ci->db;
    }

    function from($table) {
        $this->main_table = $table;
        return $this->ci->db;
    }

    function join($table, $on, $type='inner') {
        return $this->ci->db->join($table, $on, $type);
    }

    function where($param1, $param2=null, $param3) {
        return $this->ci->db->where($param1, $param2, $param3);
    }

    function limit($limit, $offset=0) {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    function result($entity) {

        if ($this->limit)
            $this->ci->db->from("(select * from $this->main_table limit $this->limit offset $this->offset) AS $this->main_table");
        else
            $this->ci->db->from($this->main_table);

        $objArr = array();
        if (!$entity)
            throw new APIexception("Missing mandatory parameter: entity.", INVALID_NUMBER_OF_PARAMS, null);

        $class = new \ReflectionClass($entity);

        $query = $this->ci->db->get();
        $result = $query->result();

        $object = null;
        $pk = "";
        $index = 0;
        //Recorremos las diferentes lineas
        foreach ($result as $row) {
            if ($object == null || !isset($this->qPkValues[$pk]) || $this->qPkValues[$object->getPK()] != $row->{$pk."_t".$index} ) {
                if ($object != null) $objArr[] = $object;
                $object = $class->newInstance();
                $object = $this->parse($row, $object);

                $pk = $object->getPK();
                $index = $this->qEntities[$class->getName()];
            } else
                $object = $this->parse($row, $object);

        }
        if ($object != null) $objArr[] = $object;

        return $objArr;
    }

    private function parse($row, $object) {
        $class = get_class($object);
        if (isset($this->qEntities[$class])) {
            $index = $this->qEntities[$class];
            $pk = $object->getPK();

            if (!isset($this->qPkValues[$pk]) || $this->qPkValues[$pk] != $row->{$pk."_t".$index} ) {
                $object = $this->setEntity($row, $object);
                $this->qPkValues[$pk] = $row->{$pk."_t".$index};
            }

            $prop = get_object_vars($object);

            foreach ($prop as $key => $value) {

                $params = array($class, $key, 'property');
                $entityReader = new Reader($params);

                $relation = $entityReader->getParameter("ORM\Relation");
                if ($relation) {
                    if (is_array($relation) && isset($relation[1]) && $relation[1] == "array" && !is_array($object->{$key}))
                        $object->{$key} = array();

                    $child_class = new \ReflectionClass($relation[0]);
                    $child_object = $child_class->newInstance();

                    //Si no se han seleccionado los campos del select de la entidad no agregamos el articulo
                    if (isset($this->qEntities[$child_class->getName()])) {
                        $child_index = $this->qEntities[$child_class->getName()];
                        $child_pk = $child_object->getPK();

                        if ($object->{$key} != null && isset($this->qPkValues[$child_pk]) && $this->qPkValues[$child_pk] == $row->{$child_pk."_t".$child_index} ) {

                            if (is_array($relation) && isset($relation[1]) && $relation[1] == "array")
                                $child_object = array_pop($object->{$key});
                            else
                                $child_object = $object->{$key};

                        }

                        $child_object = $this->parse($row, $child_object);

                        if ($child_object != null) {
                            if (is_array($relation) && isset($relation[1]) && $relation[1] == "array")
                                $object->{$key}[] = $child_object;
                            else
                                $object->{$key} = $child_object;
                        }

                    }

                }

            }
        }
        if ($row->{$pk."_t".$index} != null)
            return $object;
        else
            return null;
    }

    private function setEntity($row, $object) {
        $class = get_class($object);
        $index = $this->qEntities[$class];

        foreach ($row AS $key => $value) if (property_exists($class, str_replace("_t".$index, "", $key))) $object->{str_replace("_t".$index, "", $key)} = $value;

        return $object;
    }


}

?>