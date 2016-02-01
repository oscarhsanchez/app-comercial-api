<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_PROMOCION);
require_once(APPPATH.ENTITY_GRUPO_REGLA);
require_once(APPPATH.ENTITY_REGLA);
require_once(APPPATH.ENTITY_REGLA_PARAMETRO);
require_once(APPPATH.ENTITY_REGLA_VALOR);


class promocion_model extends CI_Model {

    /**
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $delegacionPk, $state, $lastTimeStamp) {
        $q = "SELECT 
                promocion.id AS id_promo, fk_entidad, promocion.titulo AS titulo_promo, promocion.descripcion AS descripcion_promo,  promocion.imagen, codigo_campana, fecha_inicio, fecha_fin, obligatoria, acumulable, multiplicable, promocion.estado AS estado_promo, promocion.token AS token_promo, promocion.tipo_web, promocion.fk_proveedor,
                grupo_reglas.id AS id_grupo, grupo_reglas.titulo AS titulo_grupo, codigo_grupo, grupo_reglas.estado AS estado_grupo, grupo_reglas.token AS token_grupo,
                regla.id AS id_regla, regla.promocion_id, grupo_reglas_id, codigo_regla, regla.titulo, regla.descripcion, tipo_regla, subtipo_regla, regla.estado AS estado_regla, excluir, regla.token AS token_regla,
                regla_parametro.id AS id_param, regla_id, nombre_parametro, regla_parametro.estado AS estado_param, regla_parametro.token AS token_param,
                regla_valor.id AS id_valor, regla_parametro_id, valor1, tipo1, valor2, tipo2, regla_valor.estado AS estado_valor, regla_valor.token AS token_valor
                FROM promocion
                LEFT JOIN regla ON promocion.id = regla.promocion_id AND regla.estado >= '".$state."'
                LEFT JOIN grupo_reglas ON promocion.id = grupo_reglas.promocion_id AND grupo_reglas.estado >= '".$state."'
                LEFT JOIN regla_parametro ON regla.id = regla_parametro.regla_id AND regla_parametro.estado >= '".$state."'
                LEFT JOIN regla_valor ON regla_parametro.id = regla_valor.regla_parametro_id AND regla_valor.estado >= '".$state."'
                JOIN (
                
                    SELECT DISTINCT promocion.id FROM promocion
                    /* DELEGACION */
                    LEFT JOIN regla AS rdel ON promocion.id = rdel.promocion_id AND rdel.tipo_regla = 1 AND rdel.subtipo_regla = 101 AND rdel.estado >= '".$state."' AND rdel.excluir = 0
                    LEFT JOIN grupo_reglas ON promocion.id = grupo_reglas.promocion_id AND grupo_reglas.estado >= '".$state."'
                    LEFT JOIN regla_parametro rdelparam ON rdel.id = rdelparam.regla_id AND rdelparam.estado >= '".$state."'
                    LEFT JOIN regla_valor rdelvalor ON rdelparam.id = rdelvalor.regla_parametro_id AND rdelvalor.estado >= '".$state."'
                    /* CLIENTE */
                    LEFT JOIN regla AS rcli ON promocion.id = rcli.promocion_id AND rcli.tipo_regla = 1 AND rcli.subtipo_regla = 103 AND rcli.estado >= 0 AND rcli.excluir = 0
                    LEFT JOIN regla_parametro rcliparam ON rcli.id = rcliparam.regla_id AND rcliparam.estado >= '".$state."'
                    LEFT JOIN regla_valor rclivalor ON rcliparam.id = rclivalor.regla_parametro_id AND rclivalor.estado >= '".$state."'
                    LEFT JOIN clientes ON clientes.pk_cliente = rclivalor.valor1 AND clientes.estado > 0
                    LEFT JOIN r_usu_cli ON clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado > 0
                    LEFT JOIN visita ON clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
                    LEFT JOIN albaranes_cab ON clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
                
                    WHERE promocion.fk_entidad = ".$entityId." AND fecha_fin > CURDATE() AND
                    (promocion.updated_at > '".$lastTimeStamp."' OR rdel.updated_at > '".$lastTimeStamp."' OR grupo_reglas.updated_at > '".$lastTimeStamp."' OR rdelparam.updated_at > '".$lastTimeStamp."' OR rdelvalor.updated_at > '".$lastTimeStamp."' OR rcli.updated_at > '".$lastTimeStamp."' OR rcliparam.updated_at > '".$lastTimeStamp."' OR rclivalor.updated_at > '".$lastTimeStamp."' OR r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' ) AND
                    ( (rdel.id IS NULL AND rcli.id IS NULL) OR (rdelvalor.valor1 = '".$delegacionPk."' AND rcli.id IS NULL) OR (rcli.id IS NOT NULL AND clientes.pk_cliente IS NOT NULL AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR albaranes_cab.pk_albaran IS NOT NULL)) )
                
                ) AS PROMO_VALIDAS ON PROMO_VALIDAS.id = promocion.id
                WHERE promocion.estado = 2 OR $state = 0
                ORDER BY id_promo, id_grupo, id_regla, id_param, id_valor
          ";

        return $q;
    }

    private function getClientQuery($entityId, $clientePk, $lineaMercadoPk, $delegacionPk, $state, $lastTimeStamp) {
        $q = "SELECT promocion.id AS id_promo, promocion.fk_entidad, promocion.titulo AS titulo_promo, promocion.descripcion AS descripcion_promo, promocion.imagen, codigo_campana, fecha_inicio, fecha_fin, obligatoria, acumulable, multiplicable, promocion.estado AS estado_promo, promocion.token AS token_promo,
                grupo_reglas.id AS id_grupo, grupo_reglas.titulo AS titulo_grupo, codigo_grupo, grupo_reglas.estado AS estado_grupo, grupo_reglas.token AS token_grupo,
                regla.id AS id_regla, regla.promocion_id, grupo_reglas_id, codigo_regla, regla.titulo, regla.descripcion, tipo_regla, subtipo_regla, regla.estado AS estado_regla, excluir, regla.token AS token_regla,
                regla_parametro.id AS id_param, regla_id, nombre_parametro, regla_parametro.estado AS estado_param, regla_parametro.token AS token_param,
                regla_valor.id AS id_valor, regla_parametro_id, valor1, tipo1, valor2, tipo2, regla_valor.estado AS estado_valor, regla_valor.token AS token_valor
                FROM promocion
                LEFT JOIN regla ON promocion.id = regla.promocion_id AND regla.estado >= $state
                LEFT JOIN grupo_reglas ON promocion.id = grupo_reglas.promocion_id AND grupo_reglas.estado >= $state
                LEFT JOIN regla_parametro ON regla.id = regla_parametro.regla_id AND regla_parametro.estado >= $state
                LEFT JOIN regla_valor ON regla_parametro.id = regla_valor.regla_parametro_id AND regla_valor.estado >= $state
                LEFT JOIN clientes ON clientes.fk_entidad = promocion.fk_entidad AND clientes.pk_cliente = '$clientePk' AND clientes.estado > 0
                JOIN (  
                              SELECT promocion_id, COUNT(*) AS TotReglas  FROM regla 
                              WHERE regla.tipo_regla = '1'  AND regla.subtipo_regla IN ('101', '102', '103') AND regla.estado = 1 
                              GROUP BY promocion_id 
                )  TOT ON TOT.promocion_id = promocion.id 
                JOIN (  
                              SELECT promocion_id, COUNT(*) AS TotReglasCumplidas  FROM regla 
                              JOIN regla_parametro PARAM ON PARAM.regla_id = regla.id 
                              JOIN regla_valor VALORES ON PARAM.id = VALORES.regla_parametro_id 
                              WHERE excluir = 0 AND valor1 IN ('$lineaMercadoPk', '$delegacionPk', '$clientePk')
                              GROUP BY promocion_id               
                        UNION
                          SELECT promocion_id, COUNT(*) AS TotReglasCumplidas  FROM regla 
                              JOIN regla_parametro PARAM ON PARAM.regla_id = regla.id 
                              JOIN regla_valor VALORES ON PARAM.id = VALORES.regla_parametro_id AND valor1 IN ('$lineaMercadoPk', '$delegacionPk', '$clientePk')
                              WHERE excluir = 1 AND valor1 IS NULL 
                              GROUP BY promocion_id               
                ) TOTCUMP ON TOTCUMP.promocion_id= promocion.id 
                WHERE promocion.fk_entidad = $entityId AND TotReglas = TotReglasCumplidas AND (promocion.estado = 2 OR $state = 0)
                AND fecha_fin > CURDATE()
                AND (promocion.updated_at > '$lastTimeStamp' OR regla.updated_at > '$lastTimeStamp' OR grupo_reglas.updated_at > '$lastTimeStamp' OR regla_parametro.updated_at > '$lastTimeStamp' OR regla_valor.updated_at > '$lastTimeStamp' OR clientes.updated_at > '$lastTimeStamp' ) 

          ";

        return $q;
    }

    /**
     * Funcion que devuelve las promociones filtradas por las reglas de acceso de cliente y delegacion
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los registros a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $delegacionPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array($promocion)
     *
     */
    function getMultipartCached($userPk, $delegacionPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $promociones = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $delegacionPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $promocionesRs = $query->result();

            $lastPromo = 0;
            $lastGrupo = 0;
            $lastRegla = 0;
            $lastParam = 0;
            $promociones = array();
            $grupos = array();
            $reglas = array();
            $parametros = array();
            $valores = array();
            $param = new regla_parametro();
            $regla = new regla();
            for ($i=0; $i<count($promocionesRs); $i++) {

                if ($promocionesRs[$i]->id_param != $lastParam) {
                    //Añadimos el parametro al Array
                    if ($lastParam != 0) {
                        $parametros[] = $param;
                        $regla->parametros = $parametros;

                        $valores = array();
                    }
                    $param = new regla_parametro();
                    $param->set($promocionesRs[$i]);
                    $param->id = $promocionesRs[$i]->id_param;
                    $param->estado = $promocionesRs[$i]->estado_param;
                    $param->token = $promocionesRs[$i]->token_param;

                    $lastParam = $promocionesRs[$i]->id_param;
                }

                $valor = new regla_valor();
                $valor->set($promocionesRs[$i]);
                $valor->id = $promocionesRs[$i]->id_valor;
                $valor->estado = $promocionesRs[$i]->estado_valor;
                $valor->token = $promocionesRs[$i]->token_valor;

                $valores[] = $valor;
                $param->valores = new stdClass();
                $param->valores = $valores;

                if ($promocionesRs[$i]->id_regla != $lastRegla) {
                    //Añadimos la regla al Array
                    if ($lastRegla != 0) {
                        $reglas[] = $regla;
                        $promocion->reglas = $reglas;
                        $parametros = array();
                    }
                    $regla = new regla();
                    $regla->set($promocionesRs[$i]);
                    $regla->id = $promocionesRs[$i]->id_regla;
                    $regla->estado = $promocionesRs[$i]->estado_regla;
                    $regla->token = $promocionesRs[$i]->token_regla;

                    $lastRegla = $promocionesRs[$i]->id_regla;
                }

                if ($promocionesRs[$i]->id_grupo != null && $promocionesRs[$i]->id_grupo != $lastGrupo) {
                    //Añadimos el grupo al Array
                    if ($lastGrupo != 0) {
                        $grupos[] = $grupo;
                        $promocion->grupos = $grupos;
                    }
                    $grupo = new grupo_regla();
                    $grupo->set($promocionesRs[$i]);
                    $grupo->id = $promocionesRs[$i]->id_grupo;
                    $grupo->titulo = $promocionesRs[$i]->titulo_grupo;
                    $grupo->estado = $promocionesRs[$i]->estado_grupo;
                    $grupo->token = $promocionesRs[$i]->token_grupo;

                    $lastGrupo = $promocionesRs[$i]->id_grupo;
                }

                if ($promocionesRs[$i]->id_promo != $lastPromo) {
                    //Añadimoa la promocion al Array
                    if ($lastPromo != 0) {
                        $promociones[] = $promocion;

                        $grupos = array();
                        $reglas = array();

                    }
                    $promocion = new promocion();
                    $promocion->set($promocionesRs[$i]);
                    $promocion->id = $promocionesRs[$i]->id_promo;
                    $promocion->titulo = $promocionesRs[$i]->titulo_promo;
                    $promocion->descripcion = $promocionesRs[$i]->descripcion_promo;
                    $promocion->estado = $promocionesRs[$i]->estado_promo;
                    $promocion->token = $promocionesRs[$i]->token_promo;

                    $lastPromo = $promocionesRs[$i]->id_promo;
                }

            }
            if ($lastParam != 0) {
                $param->valores = $valores;
                $parametros[] = $param;
            }
            if ($lastRegla != 0) {
                $regla->parametros = $parametros;
                $reglas[] = $regla;
            }
            if ($lastGrupo != 0) {
                $grupos[] = $grupo;
            }

            if ($lastPromo != 0) {
                $promocion->grupos = $grupos;
                $promocion->reglas = $reglas;
                $promociones[] = $promocion;
            }

            $rowcount = sizeof($promociones);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($promocionesRs, $pagination->pageSize);

                $promociones = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "promociones" => $promociones?$promociones:array());

    }

    /**
     * Funcion que devuelve las promociones de un cliente filtradas por las reglas de acceso de cliente y delegacion
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los registros a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $clientePk
     * @param $delegacionPk
     * @param $lineaMercadoPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array($promocion)
     *
     */
    function getClientMultipartCached($entityId, $clientePk, $lineaMercadoPk, $pagination, $delegacionPk, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $promociones = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getClientQuery($entityId, $clientePk, $lineaMercadoPk, $delegacionPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $promocionesRs = $query->result();

            $lastPromo = 0;
            $lastGrupo = 0;
            $lastRegla = 0;
            $lastParam = 0;
            $promociones = array();
            $grupos = array();
            $reglas = array();
            $parametros = array();
            $valores = array();
            $param = new regla_parametro();
            $regla = new regla();
            for ($i=0; $i<count($promocionesRs); $i++) {

                if ($promocionesRs[$i]->id_param != $lastParam) {
                    //Añadimos el parametro al Array
                    if ($lastParam != 0) {
                        $parametros[] = $param;
                        $regla->parametros = $parametros;

                        $valores = array();
                    }
                    $param = new regla_parametro();
                    $param->set($promocionesRs[$i]);
                    $param->id = $promocionesRs[$i]->id_param;
                    $param->estado = $promocionesRs[$i]->estado_param;
                    $param->token = $promocionesRs[$i]->token_param;

                    $lastParam = $promocionesRs[$i]->id_param;
                }

                $valor = new regla_valor();
                $valor->set($promocionesRs[$i]);
                $valor->id = $promocionesRs[$i]->id_valor;
                $valor->estado = $promocionesRs[$i]->estado_valor;
                $valor->token = $promocionesRs[$i]->token_valor;

                $valores[] = $valor;
                $param->valores = new stdClass();
                $param->valores = $valores;

                if ($promocionesRs[$i]->id_regla != $lastRegla) {
                    //Añadimos la regla al Array
                    if ($lastRegla != 0) {
                        $reglas[] = $regla;
                        $promocion->reglas = $reglas;
                        $parametros = array();
                    }
                    $regla = new regla();
                    $regla->set($promocionesRs[$i]);
                    $regla->id = $promocionesRs[$i]->id_regla;
                    $regla->estado = $promocionesRs[$i]->estado_regla;
                    $regla->token = $promocionesRs[$i]->token_regla;

                    $lastRegla = $promocionesRs[$i]->id_regla;
                }

                if ($promocionesRs[$i]->id_grupo != null && $promocionesRs[$i]->id_grupo != $lastGrupo) {
                    //Añadimos el grupo al Array
                    if ($lastGrupo != 0) {
                        $grupos[] = $grupo;
                        $promocion->grupos = $grupos;
                    }
                    $grupo = new grupo_regla();
                    $grupo->set($promocionesRs[$i]);
                    $grupo->id = $promocionesRs[$i]->id_grupo;
                    $grupo->titulo = $promocionesRs[$i]->titulo_grupo;
                    $grupo->estado = $promocionesRs[$i]->estado_grupo;
                    $grupo->token = $promocionesRs[$i]->token_grupo;

                    $lastGrupo = $promocionesRs[$i]->id_grupo;
                }

                if ($promocionesRs[$i]->id_promo != $lastPromo) {
                    //Añadimoa la promocion al Array
                    if ($lastPromo != 0) {
                        $promociones[] = $promocion;

                        $grupos = array();
                        $reglas = array();

                    }
                    $promocion = new promocion();
                    $promocion->set($promocionesRs[$i]);
                    $promocion->id = $promocionesRs[$i]->id_promo;
                    $promocion->titulo = $promocionesRs[$i]->titulo_promo;
                    $promocion->descripcion = $promocionesRs[$i]->descripcion_promo;
                    $promocion->estado = $promocionesRs[$i]->estado_promo;
                    $promocion->token = $promocionesRs[$i]->token_promo;

                    $lastPromo = $promocionesRs[$i]->id_promo;
                }

            }
            if ($lastParam != 0) {
                $param->valores = $valores;
                $parametros[] = $param;
            }
            if ($lastRegla != 0) {
                $regla->parametros = $parametros;
                $reglas[] = $regla;
            }
            if ($lastGrupo != 0) {
                $grupos[] = $grupo;
            }

            if ($lastPromo != 0) {
                $promocion->grupos = $grupos;
                $promocion->reglas = $reglas;
                $promociones[] = $promocion;
            }

            $rowcount = sizeof($promociones);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($promocionesRs, $pagination->pageSize);

                $promociones = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "promociones" => $promociones?$promociones:array());

    }

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'id';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('promocion');
        $this->db->where('promocion.fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

}

?>