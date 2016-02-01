<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class incidencia extends eEntity {

    public $id;
    public $fk_entidad;
    public $nombre;
    public $descripcion;
    public $cod_incidencia;
    public $referencia_1;
    public $referencia_2;
    public $fecha_limite;
    public $fecha_resolucion;
    public $tipo;
    public $tipo_valor;
    public $fk_usuario_creador;
    public $fk_usuario_asignado;
    public $fk_cliente;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;
    public $token_visita;

    public $num_codigo; //No se guarda en bbdd. Solo la utilizamos para actualizar la numeracion en r_usu_emp


    public function getPK() {
        return array ("id");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at", "num_codigo");
    }

    public function getTableName() {
        return "incidencia";
    }

}