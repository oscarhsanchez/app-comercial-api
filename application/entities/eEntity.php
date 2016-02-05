<?php

abstract class eEntity {

	
	
	abstract function getPK();
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbddriu
	abstract function unSetProperties();

	abstract function getTableName();

	function __construct()
    {
    	
    }

    function _save($ignoreNulls, $isPkAutoNumeric, $omittedFields = null) {
    	if ($isPkAutoNumeric) return $this->_saveWithAutoNumeric($ignoreNulls, $omittedFields);
    	else return $this->_saveWithOutAutoNumeric($ignoreNulls, $omittedFields);
    }

    function _saveWithOutAutoNumeric($ignoreNulls, $omittedFields = null) {
    	$CI =& get_instance();
    	$CI->load->database();

    	$sql = "INSERT INTO " . $this->getTableName() . " (";

    	$vars = "";
    	$upd = "";

    	$prop = get_object_vars($this);
    	unset($prop['CI']); //Elimanos la propiedad CI que no pertenece a la bbdd
    	//Eliminamos los campos que no sean necesarios guardar
		$unSetProperties = $this->unSetProperties();

		foreach ($unSetProperties as $value) {
			unset($prop[$value]);
		}

        if ($omittedFields && is_array($omittedFields)) {
            foreach ($omittedFields as $value) {
                unset($prop[$value]);
            }
        }

    	foreach ($prop as $key => $value) {
			if ((!$ignoreNulls || ($ignoreNulls && $value != null)) && (property_exists(get_class($this), $key)) ) {
				$sql = $sql . $key . ",";
				$vars = $vars . "?,";
				if (!in_array($key, $this->getPK())) $upd = $upd . $key . "=VALUES(" . $key . "), "; 
			} else {
				unset($prop[$key]);
			}		
		}

		$sql = substr($sql, 0, -1);
		$vars = substr($vars, 0, -1);
		$upd = substr($upd, 0, -2);

		$sql = $sql . ") VALUES (" . $vars . ") ON DUPLICATE KEY UPDATE " . $upd;

		$res = $CI->db->query($sql, $prop);
		return $res;
    }

	function _saveWithAutoNumeric($ignoreNulls, $omittedFields = null) {

		$CI =& get_instance();
    	$CI->load->database();
    	
		$pkCondition = "";
		$isUpdate = true;

		$prop = get_object_vars($this);

		//Comprobamos si es edicion o insercion
		$pk = $this->getPK();

		foreach ($pk as $value) {
            //La pk como es autonumrica no puede ser 0
			if ($prop[$value] === null || $prop[$value] === 0) {
				$isUpdate = false;
			} else {
				$pkCondition = $pkCondition . $value . " = " . $prop[$value] . " AND ";				
			}
		}

		//Eliminamos el ultimos AND de la condicion de la clave primaria
		if ($pkCondition != "") {
			$pkCondition = substr($pkCondition, 0, -4);
		}

		//Eliminamos los campos que no sean necesarios guardar
		$unSetProperties = $this->unSetProperties();

		foreach ($unSetProperties as $value) {
			unset($prop[$value]);
		}
		unset($prop['CI']); //Elimanos la propiedad CI que no pertenece a la bbdd

        if ($omittedFields && is_array($omittedFields)) {
            foreach ($omittedFields as $value) {
                unset($prop[$value]);
            }
        }

		//echo $pkCondition;

		foreach ($prop as $key => $value) {
			if ((!$ignoreNulls || ($ignoreNulls && $value != null)) && (property_exists(get_class($this), $key)) ) {
				$CI->db->set($key, $value);
			}			
		}

		if ($isUpdate) {
			$CI->db->where($pkCondition);
			return $CI->db->update($this->getTableName());
		} else {
			$CI->db->insert($this->getTableName());
			$insId = $CI->db->insert_id();

			return $insId;
		}

	}

	public function set($data) {
        foreach ($data AS $key => $value) if (property_exists($this, $key)) $this->{$key} = $value;        
    }


}


?>