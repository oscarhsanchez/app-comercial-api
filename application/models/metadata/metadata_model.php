<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "metadata_repository"
 * @Entity "MetadataRepository"
 * @Country true
 * @Autoincrement true;
 *
 */
class metadata_model extends generic_Model {


    function getQuery() {

        $this->db->select("pk_metadata_repository, metadata_instance.fk_pais, FIELD, IFNULL(string_value, IFNULL(integer_value, double_value)) AS VALUE, metadata_instance.updated_at, metadata_instance.created_at, metadata_instance.estado", false);
        $this->db->from('metadata_repository');
        $this->db->join('metadata_structure', 'metadata_repository.fk_pais = metadata_structure.fk_pais AND metadata_repository.pk_metadata_repository = metadata_structure.fk_metadata_repository AND metadata_structure.estado > 0');
        $this->db->join('metadata_instance', 'metadata_instance.fk_pais = metadata_repository.fk_pais AND metadata_repository.pk_metadata_repository = metadata_instance.fk_metadata_repository AND metadata_instance.estado > 0');
        $this->db->join('metadata_integer_value', "metadata_structure.pk_metadata_structure = metadata_integer_value.fk_metadata_structure AND metadata_integer_value.fk_pais = metadata_repository.fk_pais AND metadata_instance.pk_metadata_instance = metadata_integer_value.fk_metadata_instance AND metadata_integer_value.estado > 0", "left", false);
        $this->db->join('metadata_string_value', "metadata_structure.pk_metadata_structure = metadata_string_value.fk_metadata_structure AND metadata_string_value.fk_pais = metadata_repository.fk_pais AND metadata_instance.pk_metadata_instance = metadata_string_value.fk_metadata_instance AND metadata_string_value.estado > 0", "left", false);
        $this->db->join('metadata_double_value', "metadata_structure.pk_metadata_structure = metadata_double_value.fk_metadata_structure AND metadata_double_value.fk_pais = metadata_repository.fk_pais AND metadata_instance.pk_metadata_instance = metadata_double_value.fk_metadata_instance AND metadata_double_value.estado > 0", "left", false);

    }

    function getStructure($repositoryId, $countryId) {
        $this->db->where("fk_metadata_repository", $repositoryId);
        $this->db->where("fk_pais", $countryId);
        $query = $this->db->get("metadata_structure");

        return $query->result("MetadataStructure");
    }

    function getAllObjects($repository, $get_vars, $countryId, $offset, $limit, $sort, $pagination) {
        $this->getQuery();
        $this->db->where("metadata_repository.fk_pais", $countryId);
        $this->db->where("metadata_repository.name", $repository);
        $this->db->where("metadata_repository.estado > 0", false);


    }

}

?>