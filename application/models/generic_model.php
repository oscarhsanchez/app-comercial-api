<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_APIEXCEPTION);
require_once(APPPATH.READER_LIB);

class generic_model extends CI_Model {

    private $entity_properties;
    private $entity_properties_name;
    private $table;
    private $entity;
    private $requires_country;
    private $class;
    private $autoincrement;

    function __construct() {
        parent::__construct();

        $this->entity_properties = array();
        $this->entity_properties_name = array();

        $params = array(get_called_class());
        $reader = new Reader($params);

        $this->table = $reader->getParameter("Table");
        $this->entity = $reader->getParameter("Entity");
        $this->requires_country = $reader->getParameter("Country");
        $autoincrement = $reader->getParameter("Autoincrement");

        $this->autoincrement = $autoincrement == "true" ? 1 : 0;

        if ($this->entity) {
            $this->class = new \ReflectionClass($this->entity);
            $properties = $this->class->getProperties();
            foreach ($properties as $property) {
                $name = $property->getName();
                $this->entity_properties_name[] = $name;
                $this->entity_properties[] = $property;
            }

        }

    }

    /**
     * Devuelve los articulos de una entidad
     *
     * @param $countryId
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @param $sort (Opcional)
     * @param $pagination (Opcional)
     *
     * @return pagination, array
     */
    function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {

        if (isset($pagination->active) && $pagination->active && isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $result = unserialize($this->esocialmemcache->get($key));
            if (!$result) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {

            if ($this->requires_country && $countryId)
                $this->db->where($this->table . '.fk_pais', $countryId);

            if ($get_vars && is_array($get_vars)) {

                $keys = array_keys($get_vars);
                foreach($keys as $key){
                    if ($key != "offset" && $key != "limit" && $key != "sort" && $key != "pagination") {
                        if (is_array($this->entity_properties_name) && in_array($key, $this->entity_properties_name)) {
                            $value = html_entity_decode($get_vars[$key]);
                            if (startsWith($value, "(") && endsWith($value, ")")) {
                                $arr = explode(",", get_string_between($value, "(", ")"));
                                $this->db->where_in($key, $arr);
                            }
                            elseif (startsWith($value, "%[") && endsWith($value, "]%"))
                                $this->db->like($key, str_replace("%[", "", str_replace("]%", "", $value)), 'both');
                            elseif (startsWith($value, "%[") && endsWith($value, "]"))
                                $this->db->like($key, str_replace("%[", "", str_replace("]", "", $value)), 'before');
                            elseif (startsWith($value, "[") && endsWith($value, "]%"))
                                $this->db->like($key, str_replace("[", "", str_replace("]%", "", $value)), 'after');
                            elseif (startsWith($value, ">[") && endsWith($value, "]"))
                                $this->db->where($key . " >", str_replace(">[", "", str_replace("]", "", $value)));
                            elseif (startsWith($value, ">=[") && endsWith($value, "]"))
                                $this->db->where($key . " >=", str_replace(">=[", "", str_replace("]", "", $value)));
                            elseif (startsWith($value, "<[") && endsWith($value, "]"))
                                $this->db->where($key . " <", str_replace("<[", "", str_replace("]", "", $value)));
                            elseif (startsWith($value, "<=[") && endsWith($value, "]"))
                                $this->db->where($key . " <=", str_replace("<=[", "", str_replace("]", "", $value)));
                            else
                                $this->db->where($key, $value);
                        } else
                            throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $key);
                    }
                }

            }


            $offset = intval($offset);
            if (is_int($offset) && $limit)
                $this->db->limit($limit, $offset);

            if ($sort) {
                $sortArr = explode(",", get_string_between($sort, "[", "]"));
                foreach ($sortArr as $sort) {
                    if (endsWith(strtolower($sort), "_desc")) {
                        if (is_array($this->entity_properties_name) && !in_array(str_replace("_desc", "",strtolower(trim($sort))), $this->entity_properties_name))
                            throw new APIexception("Property not defined on Entity (Order by)", INVALID_PROPERTY_NAME, $key);
                        $this->db->order_by(str_replace("_desc", "",strtolower(trim($sort))), "desc");
                    } else {
                        if (is_array($this->entity_properties_name) && !in_array(str_replace("_asc", "",strtolower(trim($sort))), $this->entity_properties_name))
                            throw new APIexception("Property not defined on Entity (Order by)", INVALID_PROPERTY_NAME, $key);
                        $this->db->order_by(str_replace("_asc", "",strtolower(trim($sort))), "asc");
                    }
                }
            }

            $query = $this->db->get($this->table);
            if ($this->entity)
                $result = $query->result($this->entity);
            else
                $result = $query->result();

            //Particionamos la peticion si se solicita
            if (isset($pagination->active) && $pagination->active) {
                $rowcount = sizeof($result);
                if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE)
                    throw new APIexception("Exceeded max pagination size", ERROR_MAX_PAGINATION_SIZE, serialize($pagination));;

                $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
                $pagination->page = 0;

                if ($rowcount > $pagination->pageSize) {
                    $chunk_result = array_chunk($result, $pagination->pageSize);

                    $result = $chunk_result[0];

                    $fecha = new DateTime();
                    $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                    for ($i=1; $i < sizeof($chunk_result); $i++) {
                        $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                    }

                }

            }

        }

        return array("pagination" => $pagination, "result" => $result?$result:array());
    }

    function update($get_vars, $put_vars, $countryId) {

        if ($this->requires_country && $countryId)
            $this->db->where('fk_pais', $countryId);

        $keys = array_keys($get_vars);
        foreach($keys as $key){

            if (is_array($this->entity_properties_name) && in_array($key, $this->entity_properties_name)) {
                $value = html_entity_decode($get_vars[$key]);
                if (startsWith($value, "(") && endsWith($value, ")")) {
                    $arr = explode(",", get_string_between($value, "(", ")"));
                    $this->db->where_in($key, $arr);
                }
                elseif (startsWith($value, "%[") && endsWith($value, "]%"))
                    $this->db->like($key, str_replace("%[", "", str_replace("]%", "", $value)), 'both');
                elseif (startsWith($value, "%["))
                    $this->db->like($key, str_replace("%[", "", $value), 'before');
                elseif (endsWith($value, "]%"))
                    $this->db->like($key, str_replace("]%", "", $value), 'after');
                else
                    $this->db->where($key, $value);
            } else
                throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $key);

        }

        $data = array();

        $keys = array_keys($put_vars);
        foreach($keys as $key){

            if (is_array($this->entity_properties_name) && in_array($key, $this->entity_properties_name)) {
                $data[$key] = $value = $put_vars[$key];
            }

        }

        return $this->db->update($this->table, $data);


    }

    function create($entity, $array, $countryId) {

        if (!$entity && !$array)
            throw new APIexception("Missing mandatory parameter", INVALID_NUMBER_OF_PARAMS, "");

        if ($this->requires_country) {
            if ($countryId)
                $this->db->set("fk_pais", $countryId);
            else {
                throw new APIexception("Missing mandatory countryId", INVALID_NUMBER_OF_PARAMS, "");
            }
        }

        //Comprobamos loa campos
        if ($entity) {
            $properties = get_object_vars($entity);
            foreach ($properties as $name => $value) {
                if (!in_array($name, $this->entity_properties_name))
                    throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $name);
            }
        } else {
            foreach ($array as $entity) {
                $properties = get_object_vars($entity);
                foreach ($properties as $name => $value) {
                    if (!in_array($name, $this->entity_properties_name))
                        throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $name);
                }
            }
        }



        if ($array) {
            $this->db->trans_start();
            foreach ($array as $entity) {
                $dbEntity = null;
                if (isset($entity->token))
                    $dbEntity = $this->getByToken($entity->token, $countryId);


                $instance = $this->class->newInstanceArgs();
                $instance->set($entity);

                if ($dbEntity) {
                    $pk = $instance->getPK();
                    $instance->$pk = $dbEntity->$pk;
                }


                if (!isset($instance->token)) {
                    $instance->token = getToken();
                }

                $resSave = $instance->_save(false, $this->autoincrement, null);
                if (!$resSave)
                    return false;
            }
            $this->db->trans_complete();
            return true;
        }
        else {
            $dbEntity = null;
            if (isset($entity->token))
                $dbEntity = $this->getByToken($entity->token, $countryId);


            $instance = $this->class->newInstanceArgs();
            $instance->set($entity);

            if ($dbEntity) {
                $pk = $instance->getPK();
                $instance->$pk = $dbEntity->$pk;
            }


            if (!isset($instance->token)) {
                $instance->token = getToken();
            }

            return $instance->_save(false, $this->autoincrement, null);
        }


        /*if ($array)
            return $this->db->insert_batch($this->table, $array);
        else
            return $this->db->insert($this->table, $entity);*/

    }

    function delete($get_vars, $countryId) {
        if ($this->requires_country && $countryId)
            $this->db->where('fk_pais', $countryId);

        $keys = array_keys($get_vars);
        foreach($keys as $key){

            if (is_array($this->entity_properties_name) && in_array($key, $this->entity_properties_name)) {
                $value = html_entity_decode($get_vars[$key]);
                if (startsWith($value, "(") && endsWith($value, ")")) {
                    $arr = explode(",", get_string_between($value, "(", ")"));
                    $this->db->where_in($key, $arr);
                }
                elseif (startsWith($value, "%[") && endsWith($value, "]%"))
                    $this->db->like($key, str_replace("%[", "", str_replace("]%", "", $value)), 'both');
                elseif (startsWith($value, "%["))
                    $this->db->like($key, str_replace("%[", "", $value), 'before');
                elseif (endsWith($value, "]%"))
                    $this->db->like($key, str_replace("]%", "", $value), 'after');
                else
                    $this->db->where($key, $value);
            } else
                throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $key);

        }

        $data = array();
        $data["estado"] = 0;

        return $this->db->update($this->table, $data);


    }

    function getByToken($token, $countryId) {
        if ($this->requires_country && $countryId)
            $this->db->where('fk_pais', $countryId);

        $this->db->where("token", $token);

        $query = $this->db->get($this->table);
        if ($this->entity)
            return $query->row($this->entity);
        else
            return $query->row();


    }

    function getBy($fieldName, $value, $countryId) {
        if ($this->requires_country && $countryId)
            $this->db->where('fk_pais', $countryId);

        if (is_array($this->entity_properties_name) && in_array($fieldName, $this->entity_properties_name))
            $this->db->where($fieldName, $value);
        else
            throw new APIexception("Property not defined on Entity", INVALID_PROPERTY_NAME, $fieldName);


        $query = $this->db->get($this->table);
        if ($this->entity)
            return $query->row($this->entity);
        else
            return $query->row();


    }

}