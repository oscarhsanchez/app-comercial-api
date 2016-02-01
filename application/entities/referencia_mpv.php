<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class referencia_mpv extends eEntity {

    public $id;
    public $fk_cliente;
    public $cod_referencia_mpv;
    public $matricula;
    public $nombre;
    public $vencimiento;
    public $asignados;
    public $fk_delegacion_stock; //$delegacionStock_id;
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
        return "referencia_mpv";
    }

}