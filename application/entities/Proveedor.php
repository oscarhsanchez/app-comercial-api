<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Proveedor extends eEntity {

    public $pk_proveedor;
    public $fk_entidad;
    public $fk_forma_pago;
    public $fk_condicion_pago;
    public $cod_proveedor;
    public $nombre_comercial;
    public $raz_social;
    public $nif;
    public $direccion;
    public $poblacion;
    public $codpostal;
    public $telefono_fijo;
    public $telefono_movil;
    public $fax;
    public $mail;
    public $web;
    public $persona_contacto;
    public $telefono_contacto;
    public $mail_contacto;
    public $cargo_contacto;
    public $dia_pago;
    public $observaciones;
    public $created_at;
    public $updated_at;
    public $tipo_iva;
    public $estado;
    public $token;
    public $fk_provincia_entidad;
    public $fk_pais_entidad;
    public $valoracion_media;
    public $logo;
    public $pedido_minimo;


    public function getPK() {
        return array ("pk_proveedor");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "proveedores";
    }

}