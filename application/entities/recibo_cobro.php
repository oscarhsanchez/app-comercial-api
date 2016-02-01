<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class recibo_cobro extends eEntity {

    public $pk_recibo_cobro;
    public $fk_forma_pago;
    public $fk_entidad;
    public $fk_factura_cliente;
    public $fecha;
    public $total;
    public $estado_recibo;
    public $anotaciones;
    public $fk_usuario_entidad;
    public $fk_cliente; //Campo exclusivo del WS para enviarselo a eWay
    public $token_visita;
    public $fecha_cobro;
    public $fecha_vencimiento;
    public $varios1;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_recibo_cobro");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("fk_cliente", "created_at", "update_at");
    }

    public function getTableName() {
        return "recibos_cobro";
    }

}