<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_usu_cap extends eEntity {

    public $pk_usuario_cliente;
    public $fk_entidad;
    public $fk_cliente;
    public $fk_usuario_vendedor;
    public $probabilidad;
    public $fecha_desde;
    public $fecha_hasta;
    public $created_at;
    public $updated_at;
    public $token;

    public $num_codigo; //No se guarda en bbdd. Solo la utilizamos para actualizar la numeracion en r_usu_emp. Para captacion

    public function getPK() {
        return array ("pk_usuario_cliente");
    }

    public function setPK() {
        //Autonumerico
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at", "num_codigo");
    }

    public function getTableName() {
        return "r_usu_cap";
    }

}

?>