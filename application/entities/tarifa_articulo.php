<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class tarifa_articulo extends eEntity {

    public $id_r_art_tar;
    public $fk_entidad;
    public $fk_articulo;
    public $fk_tarifa;
    public $precio;
    public $iva;
    public $re;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id_r_art_tar");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "r_art_tar";
    }

}