<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class FacturaDetalle extends eEntity {

    public $pk_factura_detalle;
    public $fk_factura;
    public $fk_pais;
    public $fk_empresa;
    public $serie;
    public $id_factura;
    public $fecha;
    public $id_propuesta;
    public $id_orden_facturacion;
    public $fk_plaza;
    public $concepto;
    public $unidad;
    public $cantidad;
    public $precio_renta;
    public $importe;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;


    public function getPK() {
        return "pk_factura_detalle";
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "facturas_detalle";
    }

} 