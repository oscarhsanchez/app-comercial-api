<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Factura extends eEntity {

    public $pk_factura;
    public $serie;
    public $id_factura;
    public $fecha;
    public $estatus;
    public $unidad_negocio;
    public $tipo_documento;
    public $fk_pais;
    public $fk_empresa;
    public $fk_agencia;
    public $fk_cliente;
    public $fk_facturar;
    public $codigo_user;
    public $moneda;
    public $dias_credito;
    public $tipo_cambio;
    public $porcentaje_impuesto;
    public $id_propuesta;
    public $id_orden_facturacion;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;

    /**
     * @ORM\Relation ["FacturaDetalle", "array"]
     */
    public $detalle;


	public function getPK() {
		return "pk_factura";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at", "detalle");
	}

	public function getTableName() {
		return "facturas";
	}

}

?>