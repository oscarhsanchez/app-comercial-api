<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ingreso extends eEntity {
	
	public $pk_otrosingr;
	public $fk_entidad;	
	public $fk_cliente;
	public $fk_usuario;
	public $serie;	
	public $anio;	
	public $fk_serie_entidad;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fk_forma_pago;
	public $fk_condicion_pago;
	public $id_tipo_ingreso;	
	public $id_cuenta_pgc;		
	public $cod_otroingr;		
	public $cod_cliente;	
	public $cod_usuario_entidad;	
	public $num_serie;	
	public $cod_delegacion;	
	public $cod_terminal_tpv;	
	public $cod_forma_pago;	
	public $cod_condicion_pago;		
	public $bool_actualiza_numeracion;	
	public $bool_recalcular;	
	public $fecha;	
	public $hora;	
	public $raz_social;	
	public $nif;	
	public $direccion;	
	public $poblacion;	
	public $provincia;	
	public $codpostal;	
	public $base_imponible_tot;	
	public $imp_iva_tot;	
	public $imp_re_tot;	
	public $imp_total;	
	public $imp_desc_tot;	
	public $imp_retencion_tot;	
	public $observaciones;	
	public $period_tipo_frecuencia;	
	public $repetir_cada;	
	public $period_tipo_mensual;	
	public $period_values_mes;	
	public $period_dia_1;	
	public $period_dia_2;	
	public $period_dia_3;	
	public $period_dia_4;	
	public $period_dia_5;
	public $period_dia_6;
	public $period_dia_7;
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
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;


	public function getPK() {
		return array ("pk_otrosingr");
	}

	public function setPK() {
		if (isset($this->cod_otroingr) && isset($this->fk_entidad)) $this->pk_otrosingr = $this->cod_otroingr . "_" . $this->fk_entidad;
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "ingresos_cab";
	}

}