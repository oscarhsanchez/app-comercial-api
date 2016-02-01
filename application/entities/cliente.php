<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cliente extends eEntity {

	public $pk_cliente;
	public $fk_entidad;
	public $fk_delegacion;
	public $fk_cliente_subzona;
	public $fk_linea_mercado;
	public $fk_forma_pago;
    public $fk_cliente_cond_esp;
	public $fk_provincia_entidad;
	public $fk_pais_entidad;
	public $cod_cliente;		
	public $bool_es_captacion;
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
	public $dia_pago;	
	public $observaciones;	
	public $created_at;
	public $updated_at;
	public $tipo_iva;
    public $credito_maximo;
    public $fecha_baja;
	public $estacionalidad_periodo1_desde;
	public $estacionalidad_periodo1_hasta;
	public $estacionalidad_periodo2_desde;
	public $estacionalidad_periodo2_hasta;
	public $bool_asignacion_generica;
    public $bool_albaran_valorado;
    public $bool_facturacion_final_mes;
    public $hora_apertura;
    public $hora_cierre;
    public $horario_entrega_inicial;
    public $horario_entrega_final;
	public $varios1;
	public $varios2;
	public $varios3;
	public $varios4;
	public $varios5;
	public $varios6;
	public $varios7;
	public $varios8;
	public $varios9;
	public $varios10;
    public $puntos;
    public $token_tpv;
    public $longitud;
    public $latitud;
	public $estado;
	public $token;

    //Datos de facturacion.
    public $fk_cliente_facturacion;
    public $raz_social_facturacion;
    public $nif_facturacion;
    public $direccion_facturacion;
    public $poblacion_facturacion;
    public $codpostal_facturacion;
    public $fk_provincia_facturacion;
    public $fk_pais_facturacion;

    //Datos de Entrga
    public $direccion_entrega;
    public $poblacion_entrega;
    public $codpostal_entrega;
    public $fk_provincia_entrega;



		

	public function getPK() {
		return array ("pk_cliente");
	}
	public function setPK() {
		if (isset($this->cod_cliente) && isset($this->fk_entidad)) $this->pk_cliente = $this->cod_cliente . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at", "num_codigo", "fk_cliente_facturacion", "raz_social_facturacion", "nif_facturacion", "direccion_facturacion", "poblacion_facturacion", "codpostal_facturacion", "fk_provincia_facturacion", "fk_pais_facturacion");
	}

	public function getTableName() {
		return "clientes";
	}

}

?>