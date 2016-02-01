<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class tarifa_delegacion extends eEntity {

    public $id;
    public $fk_entidad;
    public $fk_delegacion;
    public $fk_tarifa;
    public $prioridad;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "r_del_tar";
    }

}