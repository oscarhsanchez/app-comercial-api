<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_SERIE);


class serie_model extends CI_Model {

	function getSeriesByEntity($entityId) {
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('estado','1');
		$query = $this->db->get('series'); 

		$series = $query->result('serie');
		
		return $series;		
	}

	function getSeriesByEntityYear($entityId, $year) {
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('anio', $year);
		$this->db->where('estado','1');
		$query = $this->db->get('series'); 

		$series = $query->result('serie');
		
		return $series;		
	}

	function getSerieBySerieYearEntity($entityId, $serie, $year) {
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
		$this->db->where('estado','1');
		$query = $this->db->get('series'); 

		$serie = $query->result('serie');
		
		return $serie;		
	}

	function getSerieByUserAsigned($entityId, $userId) {
		$this->db->select('series.*', false);
		$this->db->from('series');
		$this->db->join('r_usu_emp AS entityUser', 'series.serie = entityUser.serie_id AND series.anio = entityUser.serie_anio AND series.fk_entidad = entityUser.fk_serie_entidad');
		$this->db->where('id_usuario', $userId);
		$this->db->where('series.fk_entidad', $entityId);		
		$query = $this->db->get(); 

		$serie = $query->row(0, 'serie');
		
		return $serie;		
	}

	function updateBudgetNum($entityId, $serie, $year, $num) {
		$q = new stdClass();
		$q->num_presu = $num;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_presu < ', $num, false);

		return $this->db->update('series', $q);
	}

	function updateInvoiceNum($entityId, $serie, $year, $num) {
		$q = new stdClass();
		$q->num_factura = $num;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_factura < ', $num, false);

		return $this->db->update('series', $q);
	}

	function updateWaybillNum($entityId, $serie, $year, $num) {
		$q = new stdClass();
		$q->num_albaran = $num;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_albaran < ', $num, false);

		return $this->db->update('series', $q);
	}

	function updateOrderNum($entityId, $serie, $year, $num) {
		$q = new stdClass();
		$q->num_pedido = $num;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_pedido < ', $num, false);

		return $this->db->update('series', $q);
	}

	function updateRevenueNum($entityId, $serie, $year, $num) {
		$q = new stdClass();
		$q->num_otros_ingr = $num;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_otros_ingr < ', $num, false);

		return $this->db->update('series', $q);
	}

	function setDefaultSerie($entityId, $serie, $year) {
		//Ponemos todas la series de la entidad como predeterminada
		$q = new stdClass();
		$q->bool_predeterminada = 0;				
		$this->db->where('fk_entidad', $entityId);		
		$this->db->update('series', $q);

		//Marcamos como predeterminada la indicada
		$q = new stdClass();
		$q->bool_predeterminada = 1;				
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('serie', $serie);
		$this->db->where('anio', $year);

		return $this->db->update('series', $q);
	}

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'serie';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('series');
        $this->db->where('fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }


}

?>