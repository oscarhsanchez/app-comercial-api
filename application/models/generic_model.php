<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PAIS);
require_once(APPPATH.ENTITY_PROVINCIA);


class generic_model extends CI_Model {

    /**
     * Devuelve un listado con todos los paises
     *
     * @return Array Paises
     */
    function getAllPaises($updateDateTime, $entityId, $state) {
        $this->db->where('updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('estado >= ', $state, false);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('pais_entidad');

        $result = $query->result('pais');
        return $result;
    }

    /**
     * Devuelve un listado con todos los paises
     *
     * @return Array Paises
     */
    function getPaisesByEntidad($entityId) {
        $this->db->where('estado > 0');
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('pais_entidad');

        $result = $query->result('pais');
        return $result;
    }

    /**
     * Devuelve un listado con todas las provincias
     *
     * @return Array Provincias
     */
    function getAllProvincias($updateDateTime, $entityId, $state) {
        $this->db->where('updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('estado >= ', $state, false);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('provincia_entidad');

        $result = $query->result('provincia');
        return $result;
    }

    /**
     * Devuelve un listado con todas las provincias
     *
     * @return Array Provincias
     */
    function getProvinciasByEntidad($entityId) {
        $this->db->where('estado > 0');
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('provincia_entidad');

        $result = $query->result('provincia');
        return $result;
    }



}