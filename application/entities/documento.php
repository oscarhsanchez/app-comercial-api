<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class documento extends eEntity {

	public function _calculate($lines) {
		$CI =& get_instance();
		$CI->load->model("cliente_model");
		$CI->load->model("entity_model");
		$CI->load->model("articulo_model");

		//Obtenemos el cliente
		if (isset($this->id_cliente)) {
			$client = $CI->cliente_model->getClientById($this->id_cliente);
		}

		$entity = $CI->entity_model->getEntityById($this->id_entidad);

		$arrIva = array();
		$arrRe = array();
		foreach ($lines as $line) {			

			//Obtenemos el articulos
			if (isset($line->id_articulo)) {
				$product = $CI->articulo_model->getProdcutById($line->id_articulo);
			}
			//Obetenemos el iva
			if ($product && $product->iva) {
				$line->iva = $product->iva;
			} else if ($entity && $entity->iva_x_defecto) {
				$line->iva = $entity->iva_x_defecto;
			}
			//Obetenemos el re
			if ($product && $product->re) {
				$line->re = $product->re;
			} else if ($entity && $entity->re_x_defecto) {
				$line->re = $entity->re_x_defecto;
			}
			
			//Obtenemos el precio del articulos
			
			//Obtenemos los descuentos

			//Hacemos los calculos de las lineas			
			if (isset($line->descuento)) {
				$line->imp_descuento = $line->imp_descuento * $line->descuento / 100;
			} else {
				$line->descuento = 0;
				$line->imp_descuento = 0;
			}
			$line->base_imponible = ($line->cantidad * $line->precio) - $line->imp_descuento;
			
			if ($cliente) {
				if (isset($cliente->tipo_iva) && $cliente->tipo_iva == 0) {
					$line->iva = 0;
					$line->imp_iva = 0;
					$line->re = 0;
					$line->imp_re = 0;
				} else if (isset($cliente->tipo_iva) && $cliente->tipo_iva == 1) {
					$line->imp_iva = $line->base_imponible * $line->iva / 100;
					$line->re = 0;
					$line->imp_re = 0;
				} else if (isset($cliente->tipo_iva) && $cliente->tipo_iva == 2) {
					$line->imp_iva = $line->base_imponible * $line->iva / 100;
					$line->imp_re = $line->base_imponible * $line->re / 100;
				} else {
					$line->imp_iva = $line->base_imponible * $line->iva / 100;
					$line->re = 0;
					$line->imp_re = 0;
				}
				
			} else {
				$line->imp_iva = $line->base_imponible * $line->iva / 100;
				$line->re = 0;
				$line->imp_re = 0;
			}
			
            if (isset($arrIva[$line->iva]))
            {
                $arrIva[$line->iva] = $arrIva[$line->iva] + $line->imp_iva;
            }
            else
            {
                $arrIva[$line->iva] = $line->imp_iva;
            }

            if (isset($arrIva[$line->re]))
            {
                $arrIva[$line->re] = $arrIva[$line->re] + $line->imp_re;
            }
            else
            {
                $arrIva[$line->re] = $line->imp_re;
            }

            //Calculamos la retencion
            $imp_retencion = 0;            
            if (isset($line->retencion)) {
            	if ($entity && $entity->irpf_x_defecto) {
	            	$line->imp_retencion = $entity->irpf_x_defecto;
	            }
            	$line->imp_retencion = $line->base_imponible * $line->retencion / 100;
            	$imp_retencion = $line->imp_retencion;
            }

            $line->total_lin = $line->base_imponible + $line->imp_iva + $line->imp_re - $imp_retencion;
		}

		$this->base_imponible_tot;
		$this->imp_desc_tot;
		$this->imp_iva_tot;
		$this->imp_re_tot;
		$this->imp_retencion_tot;
		$this->imp_total;

	}





}