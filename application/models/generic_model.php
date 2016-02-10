<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_APIEXCEPTION);

class generic_model extends CI_Model {

    private $entity_properties;
    private $entity_properties_name;
    private $table;
    private $entity;
    private $requires_country;

    function __construct() {
        parent::__construct();

        $this->entity_properties = array();
        $this->entity_properties_name = array();

        $params = array(get_called_class());
        $this->load->library('Reader', $params, 'ModelReader');

        $this->table = $this->ModelReader->getParameter("Table");
        $this->entity = $this->ModelReader->getParameter("Entity");
        $this->requires_country = $this->ModelReader->getParameter("Country");

        if ($this->entity) {
            $class = new \ReflectionClass($this->entity);
            $properties = $class->getProperties();
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
     * @param $entityId
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @param $subfamiliaPk (Opcional)
     * @param $desc (Opcional)
     * @param $codigo (Opcional)
     * @param $ean (Opcional)
     * @return array
     */
    function getAll($get_vars, $offset, $limit, $countryId=0) {

        if ($this->requires_country && $countryId)
            $this->db->where('fk_pais', $countryId);

        if ($get_vars && is_array($get_vars)) {
            foreach ($get_vars as $var) {
                $keys = array_keys($get_vars);
                foreach($keys as $key){
                    if ($key != "offset" && $key != "limit") {
                        if (is_array($this->entity_properties_name) && in_array($key, $this->entity_properties_name)) {
                            $value = html_entity_decode($get_vars[$key]);
                            echo $get_vars[$key];
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
                            throw new APIexception("Property not defined on Entity", ERROR_GETTING_INFO, $key);
                    }
                }
            }
        }

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get($this->table);
        if ($this->entity)
            $result = $query->result($this->entity);
        else
            $result = $query->result();

        return $result;

    }


}