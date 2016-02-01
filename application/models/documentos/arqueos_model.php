<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT);


class arqueos_model extends CI_Model {

	function getCashCountByPk($cashCountPk) {
		//CABECERA
		$this->db->where('id_arqueo_caja', $cashCountPk);		
		$query = $this->db->get('arqueos_caja_cab');
		
		$cashCount = $query->row(0, 'arqueo');

		//LINEAS
		if ($cashCount) {
			$this->db->where('id_arqueo_caja_lin', $cashCountPk);
			$query = $this->db->get('arqueos_caja_lin');
			$cashCountLines = $query->result('cashCountLine');

			$cashCount->cashCountLines = $cashCountLines;
		}

		return $cashCount;
	}

	function getCashCountByToken($cashCountToken, $fk_entidad) {
		//CABECERA
		$this->db->where('token', $cashCountToken);
		$this->db->where('fk_entidad', $fk_entidad);		
		$query = $this->db->get('arqueos_caja_cab');
		
		$cashCount = $query->row(0, 'arqueo');

		//LINEAS
		if ($cashCount) {
			$this->db->where('id_arqueo_caja_lin', $cashCount->id_arqueo_caja);
			$query = $this->db->get('arqueos_caja_lin');
			$cashCountLines = $query->result('cashCountLine');

			$cashCount->cashCountLines = $cashCountLines;
		}

		return $cashCount;
	}

	function saveCashCount($cashCount) {
		$this->load->model("log_model");
		if (!isset($cashCount->token)) {
			$cashCount->token = getToken();
		}
		
		$cashCountPk = $cashCount->_save(false, true);
		//COmprobamos si tiene PK y sino se la asignamos, ya que significa que era nuevo (Es Autonumerico).
		if (!isset($cashCount->id_arqueo_caja)) $cashCount->id_arqueo_caja = $cashCountPk;

		if ($cashCountPk) {
			if (isset($cashCount->cashCountLines)) {				
				$cashCountLines = $cashCount->cashCountLines;
				foreach ($cashCountLines as $line) {
					$line->id_arqueo_caja = $cashCount->id_arqueo_caja;	
					
					if ($line->id_arqueo_caja_lin == null) {
						$query = new stdClass();
						$this->db->where('token', $line->token);
						$query = $this->db->get("arqueos_caja_lin");
						$existingLine = $query->row();						
						if ($existingLine) $line->id_arqueo_caja_lin = $existingLine->id_arqueo_caja_lin;
					}									
					if (!isset($line->token)) {
						$line->token = getToken();
					}
					$res = $line->_save(false, true);

					if (!$res) throw new APIexception("Error on arqueos_model->createCashCount. Unable to create CashCount Line", ERROR_SAVING_DATA, serialize($cashCount));
				}
			}					
			return true;
		} else {
			throw new APIexception("Error on arqueos_model->createCashCount. Unable to create CashCount", ERROR_SAVING_DATA, serialize($cashCount));
		}	
	}
	
}

?>