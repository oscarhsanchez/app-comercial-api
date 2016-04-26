<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_MEDIO);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "medios"
 * @Entity "Medio"
 * @Country true
 * @Autoincrement false;
 *
 */
class medio_model extends generic_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('medios/subtipo_medio_model');

    }

    function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {
        $extended = null;
        if (isset($get_vars["extended"])) {
            $extended = $get_vars["extended"];
            unset($get_vars["extended"]);
        }

        $result = parent::getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

        if ($extended) {
            $mediosArr = array();

            foreach ($result AS $medio) {
                $subtipo = $this->subtipo_medio_model->getBy("pk_subtipo", $medio->fk_subtipo, $countryId);
                $medio->subtipo = $subtipo;

                $mediosArr[] = $medio;    
            }

            $result["result"] = $mediosArr;

            return $result;

        } else
            return $result;

    }
	
}

?>