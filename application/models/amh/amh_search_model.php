<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE_AMH);


class amh_search_model extends CI_Model {

    function getQuery() {

        $query = "
            SELECT DISTINCT clientes.pk_cliente, clientes.fk_entidad, clientes.cod_cliente, nombre_comercial, fk_linea_mercado, lineas_mercado.name AS nombre_lm, cliente_subzonas.pk_cliente_subzona, cliente_subzonas.name AS nombre_subzona, cliente_zonas.pk_cliente_zona, cliente_zonas.name AS nombre_zona, IFNULL(fact_inter, 0) AS fact, IFNULL(CLIENTESTOT.dropsize,0) AS dropsizeCalculado, clientes.token
            FROM clientes
            LEFT JOIN cliente_subzonas ON pk_cliente_subzona = fk_cliente_subzona
            LEFT JOIN cliente_zonas ON pk_cliente_zona = fk_cliente_zona
            LEFT JOIN lineas_mercado ON pk_linea_mercado = fk_linea_mercado
            LEFT JOIN r_usu_cli ON pk_cliente = fk_cliente
            LEFT JOIN (
                SELECT fk_cliente, fk_entidad, ROUND(SUM(base_imponible_tot-IFNULL(imp_desc_tot,0)-IFNULL(desc_promocion_cab,0)),2) AS fact_inter, ROUND(SUM(base_imponible_tot-IFNULL(imp_desc_tot,0)-IFNULL(desc_promocion_cab,0))/COUNT(*),2) AS dropsize FROM  facturas_cab
                WHERE fecha BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -1 YEAR) AND CURRENT_DATE()
                GROUP BY fk_cliente, fk_entidad
            ) CLIENTESTOT ON CLIENTESTOT.fk_cliente = clientes.pk_cliente AND CLIENTESTOT.fk_entidad = clientes.fk_entidad
        "." ";

        return $query;

    }


    function advanceSearch($pIsOperadorAnd=1, $pPkEntidad, $pPkVendedor, $pPkRepartidor, $pPkCliente, $pCodPostal, $pPkProvincia, $pBoolAsignacionGenerica, $pLongitud, $pLatitud, $pRadio, $pPkClienteCondEspecial, $pPkClienteFacturacion, $pkZona, $pPkSubzona, $pLineaMercado, $pFacturacionInicial, $pFacturacionFinal, $pDropsizeInicial, $pDropsizeFinal, $pTipoFrecuencia, $pRepetirCada, $pDiasMes, $pDiaSemanaMes, $pHoraVisitaInicial, $pHoraVisitaFinal, $pDia_1=0, $pDia_2=0, $pDia_3=0, $pDia_4=0, $pDia_5=0, $pDia_6=0, $pDia_7=0, $pHoraRepartoInicial, $pHoraRepartoFinal ) {

        $query = $this->getQuery();
        $query .= "WHERE clientes.fk_entidad = " . $pPkEntidad . ' AND ( ';

        $operador = " OR ";
        if ($pIsOperadorAnd == 1) $operador = " AND ";

        //Filtros
        $firstFilter = 1;

        if ($pPkCliente) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "clientes.pk_cliente = '".$pPkCliente . "'";
            $firstFilter = 0;
        }
        if ($pCodPostal) {
            if ($firstFilter == 0) $query .= $operador;
            $pCodPostal == "none" ? $query .= "clientes.codPostal IS NULL" : $query .= "clientes.codPostal = '".$pCodPostal . "'";
            $firstFilter = 0;
        }
        if ($pPkVendedor) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkVendedor == "none" ? $query .= "r_usu_cli.fk_usuario_vendedor IS NULL" : $query .= "r_usu_cli.fk_usuario_vendedor = '".$pPkVendedor . "'";
            $firstFilter = 0;
        }
        if ($pPkRepartidor) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkRepartidor == "none" ? $query .= "r_usu_cli.fk_usuario_repartidor IS NULL" : $query .= "r_usu_cli.fk_usuario_repartidor = '".$pPkRepartidor . "'";
            $firstFilter = 0;
        }
        if ($pPkProvincia) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkProvincia == "none" ? $query .= "clientes.fk_provincia_entidad IS NULL" : $query .= "clientes.fk_provincia_entidad = '".$pPkProvincia . "'";
            $firstFilter = 0;
        }
        if ($pBoolAsignacionGenerica) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "clientes.bool_asignacion_generica = ".$pBoolAsignacionGenerica;
            $firstFilter = 0;
        }
        if ($pLongitud && $pLatitud && $pRadio) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "calcDistance(".$pLatitud.", ".$pLongitud.", latitud, longitud) < ".$pRadio;
            $firstFilter = 0;
        }
        if ($pPkClienteCondEspecial) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkClienteCondEspecial == "none" ? $query .= "clientes.fk_cliente_cond_esp IS NULL" : $query .= "clientes.fk_cliente_cond_esp = '".$pPkClienteCondEspecial . "'";
            $firstFilter = 0;
        }
        if ($pPkClienteFacturacion) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkClienteFacturacion == "none" ? $query .= "clientes.fk_cliente_facturacion IS NULL" : $query .= "clientes.fk_cliente_facturacion = '".$pPkClienteFacturacion . "'";
            $firstFilter = 0;
        }
        if ($pkZona) {
            if ($firstFilter == 0) $query .= $operador;
            $pkZona == "none" ? $query .= "pk_cliente_zona IS NULL" : $query .= "pk_cliente_zona = '".$pkZona . "'";
            $firstFilter = 0;
        }
        if ($pPkSubzona) {
            if ($firstFilter == 0) $query .= $operador;
            $pPkSubzona == "none" ? $query .= "pk_cliente_subzona IS NULL" : $query .= "pk_cliente_subzona = '".$pPkSubzona . "'";
            $firstFilter = 0;
        }
        if ($pLineaMercado) {
            if ($firstFilter == 0) $query .= $operador;
            $pLineaMercado == "none" ? $query .= "clientes.fk_linea_mercado IS NULL" : $query .= "clientes.fk_linea_mercado = '".$pLineaMercado . "'";
            $firstFilter = 0;
        }
        if ($pFacturacionInicial && $pFacturacionFinal) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "IFNULL(fact_inter, 0) BETWEEN ".$pFacturacionInicial . " AND " . $pFacturacionFinal;
            $firstFilter = 0;
        }
        if ($pDropsizeInicial && $pDropsizeFinal) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "IFNULL(CLIENTESTOT.dropsize,0) BETWEEN ".$pDropsizeInicial . " AND " . $pDropsizeFinal;
            $firstFilter = 0;
        }
        if (is_numeric($pTipoFrecuencia) && ($pTipoFrecuencia == 0 || $pTipoFrecuencia == 1) && !($pDia_1 || $pDia_2 || $pDia_3 || $pDia_4 || $pDia_5 || $pDia_6 || $pDia_7)) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "r_usu_cli.tipo_frecuencia = ".$pTipoFrecuencia;
            $firstFilter = 0;
        }
        if ($pRepetirCada) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "r_usu_cli.repetir_cada = ".$pRepetirCada;
            $firstFilter = 0;
        }
        if ($pDiasMes) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "(tipo_mensual = 0 AND r_usu_cli.values_mes IN (".$pDiasMes."))";
            $firstFilter = 0;
        }
        if ($pDiaSemanaMes) {
            if (!$pDia_1) $pDia_1 = 0;
            if (!$pDia_2) $pDia_2 = 0;
            if (!$pDia_3) $pDia_3 = 0;
            if (!$pDia_4) $pDia_4 = 0;
            if (!$pDia_5) $pDia_5 = 0;
            if (!$pDia_6) $pDia_6 = 0;
            if (!$pDia_7) $pDia_7 = 0;

            if ($firstFilter == 0) $query .= $operador;
            $query .= "(tipo_mensual = 1 AND r_usu_cli.values_mes = ".$pDiaSemanaMes." AND ((dia_1 = 1 AND 1=".$pDia_1.") OR (dia_2 = 1 AND 1=".$pDia_2.") OR (dia_3 = 1 AND 1=".$pDia_3.") OR (dia_4 = 1 AND 1=".$pDia_4.") OR (dia_5 = 1 AND 1=".$pDia_5.") OR (dia_6 = 1 AND 1=".$pDia_6.") OR (dia_7 = 1 AND 1=".$pDia_7.") ))";
            $firstFilter = 0;
        }
        if ($pHoraVisitaInicial && $pHoraVisitaFinal) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "r_usu_cli.hora BETWEEN '".$pHoraVisitaInicial . "' AND '" . $pHoraVisitaFinal ."' ";
            $firstFilter = 0;
        }
        if (is_numeric($pTipoFrecuencia) && $pTipoFrecuencia == 0 && ($pDia_1 || $pDia_2 || $pDia_3 || $pDia_4 || $pDia_5 || $pDia_6 || $pDia_7)) {
            if (!$pDia_1) $pDia_1 = 0;
            if (!$pDia_2) $pDia_2 = 0;
            if (!$pDia_3) $pDia_3 = 0;
            if (!$pDia_4) $pDia_4 = 0;
            if (!$pDia_5) $pDia_5 = 0;
            if (!$pDia_6) $pDia_6 = 0;
            if (!$pDia_7) $pDia_7 = 0;

            if ($firstFilter == 0) $query .= $operador;
            $query .= "(r_usu_cli.tipo_frecuencia = 0 AND ((dia_1 = 1 AND 1=".$pDia_1.") OR (dia_2 = 1 AND 1=".$pDia_2.") OR (dia_3 = 1 AND 1=".$pDia_3.") OR (dia_4 = 1 AND 1=".$pDia_4.") OR (dia_5 = 1 AND 1=".$pDia_5.") OR (dia_6 = 1 AND 1=".$pDia_6.") OR (dia_7 = 1 AND 1=".$pDia_7.") ))";
            $firstFilter = 0;
        }
        if ($pHoraRepartoInicial && $pHoraRepartoFinal) {
            if ($firstFilter == 0) $query .= $operador;
            $query .= "r_usu_cli.hora_reparto BETWEEN '".$pHoraRepartoInicial . "' AND '" . $pHoraRepartoFinal ."' ";
            $firstFilter = 0;
        }

        $query .= ') LIMIT 500';

        $query = $this->db->query($query);

        $clientes = $query->result('cliente_amh');

        return $clientes;

    }


}