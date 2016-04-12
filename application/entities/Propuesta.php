<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Propuesta extends eEntity {

    public $pk_propuesta;
    public $fk_pais;
    public $fk_empresa;
    public $fk_cliente;
    public $fk_categoria_propuesta;
    public $unidad_negocio;
    public $anio;
    public $fecha_inicio;
    public $fecha_fin;
    public $catorcena;
    public $codigo_user;
    public $comision_user;
    public $fk_agencia;
    public $comision_agencia;
    public $observaciones;
    public $status;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    /**
     * @ORM\Relation ["PropuestaDetalle", "array"]
     */
    public $detalle;


	public function getPK() {
		return "pk_propuesta";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at", "detalle");
	}

	public function getTableName() {
		return "propuestas";
	}

}

?>