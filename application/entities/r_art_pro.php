<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_art_pro extends eEntity {

    public $id_r_art_pro;
    public $fk_entidad;
    public $fk_articulo;
    public $fk_proveedor;
    public $precio_coste;
    public $iva;
    public $created_at;
    public $updated_at;
    public $estado;
    public $token;
    public $cod_art_prov;

    public function getPK() {
        return array ("id_r_art_pro");
    }

    public function setPK() {
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "r_art_pro";
    }

}

?>