<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_ped_pro extends eEntity {

    public $id;
    public $fk_pedido;
    public $fk_promocion;
    public $fk_motivo_promocion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("id");
    }

    public function setPK() {
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "r_ped_pro";
    }

}

?>