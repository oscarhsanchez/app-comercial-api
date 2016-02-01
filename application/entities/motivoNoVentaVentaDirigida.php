<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class motivoNoVentaVentaDirigida extends eEntity {

    public $id;
    public $fk_pedido_cab;
    public $fk_mot_no_venta;
    public $fk_entidad;
    public $fk_usuario;
    public $fk_articulo;
    public $fk_subfamilia;
    public $fk_familia;
    public $fk_grupo;
    public $fk_agrupacion;
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
        return "mot_no_venta_vd";
    }

}