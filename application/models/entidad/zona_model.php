<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ZONA);
require_once(APPPATH.ENTITY_SUBZONA);


class zona_model extends CI_Model {

    /**
     * Devuelve un listado con todas las zonas de una entidad  en base
     * a la fecha de ultima actualizacion.
     *
     * @param $pkEntidad
     * @param $updateDateTime
     * * @param $state
     *
     * @return Array Zonas
     */
    function getZonasByEntidadAndLastUpdate($pkEntidad, $updateDateTime, $state) {
        $this->db->select('*');
        $this->db->from('cliente_zonas');
        $this->db->where('fk_entidad', $pkEntidad);
        $this->db->where('updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('estado >= ', $state, false);

        $query = $this->db->get();

        $result = $query->result('zona');
        return $result;
    }


    /**
     * Devuelve un listado con todas las subzonas de una entidad
     * en base a la fecha de ultima actualizacion
     *
     * @param $pkEntidad
     * @param $updateDateTime
     * @param $state
     *
     * @return Array Subzonas
     */
    function getSubzonasByEntidadAndLastUpdate($pkEntidad, $updateDateTime, $state) {

        $this->db->select('cliente_subzonas.*');
        $this->db->from('cliente_subzonas');
        $this->db->join('cliente_zonas','cliente_zonas.pk_cliente_zona = cliente_subzonas.fk_cliente_zona');
        $this->db->where('cliente_subzonas.fk_entidad', $pkEntidad);
        $this->db->where('cliente_subzonas.updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('cliente_subzonas.estado >= ', $state, false);

        $query = $this->db->get();

        $result = $query->result('subzona');
        return $result;
    }

    /**
     * Devuelve un listado con todas las zonas de una entidad y una delegacion en base
     * a la fecha de ultima actualizacion.
     *
     * @param $pkEntidad
     * @param $pkDelegacion
     * @param $updateDateTime
     * * @param $state
     *
     * @return Array Zonas
     */
    function getZonasByEntidadAndDelegacionAndLastUpdate($pkEntidad, $pkDelegacion, $updateDateTime, $state) {
        $this->db->select('*');
        $this->db->from('cliente_zonas');
        $this->db->where('fk_entidad', $pkEntidad);
        $this->db->where('fk_delegacion', $pkDelegacion);
        $this->db->where('updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('estado >= ', $state, false);

        $query = $this->db->get();

        $result = $query->result('zona');
        return $result;
    }

    /**
     * Devuelve un listado con todas las subzonas de una entidad y una delegacion
     * en base a la fecha de ultima actualizacion
     *
     * @param $pkEntidad
     * @param $pkDelegacion
     * @param $updateDateTime
     * @param $state
     *
     * @return Array Subzonas
     */
    function getSubzonasByEntidadAndDelegacionAndLastUpdate($pkEntidad, $pkDelegacion, $updateDateTime, $state) {

        $this->db->select('cliente_subzonas.*');
        $this->db->from('cliente_subzonas');
        $this->db->join('cliente_zonas','cliente_zonas.pk_cliente_zona = cliente_subzonas.fk_cliente_zona');
        $this->db->where('cliente_subzonas.fk_entidad', $pkEntidad);
        $this->db->where('cliente_zonas.fk_delegacion', $pkDelegacion);
        $this->db->where('cliente_subzonas.updated_at > ', "'".$updateDateTime."'", false);
        $this->db->where('cliente_subzonas.estado >= ', $state, false);

        $query = $this->db->get();

        $result = $query->result('subzona');
        return $result;
    }



}