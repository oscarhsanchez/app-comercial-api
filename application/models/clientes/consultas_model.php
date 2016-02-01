<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_R_USU_CLI);


class consultas_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
     ----------------------------------------*/
    private function getTotalPedidosQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT IFNULL(SUM(total_lin), 0) AS total FROM pedidos_cab cab
              JOIN pedidos_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_pedido = lin.fk_pedido_cab AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        return $q;


    }

    private function getTotalAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        return $q;


    }

    private function getTotalFacturasQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT IFNULL(SUM(total_lin), 0) AS total FROM facturas_cab cab
              JOIN facturas_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_factura = lin.fk_factura AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        return $q;


    }

    private function getTotalAlbaranesByMonthQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT YEAR(cab.fecha) AS anio, MONTH(cab.fecha) AS mes, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY YEAR(cab.fecha), MONTH(cab.fecha) ";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getConsumoSubfamiliasByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT sub.pk_art_subfamilias, sub.cod_subfamilia, sub.descripcion, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY sub.pk_art_subfamilias, sub.cod_subfamilia, sub.descripcion ";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getTopArticulosByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT art.pk_articulo, art.cod_articulo, art.descripcion, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY art.pk_articulo, art.cod_articulo, art.descripcion ";
        $q .= " ORDER BY total DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getConsumoFamiliasByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT fam.pk_art_familias, fam.cod_familia, fam.descripcion, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";
        $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY fam.pk_art_familias, fam.cod_familia, fam.descripcion ";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getConsumoGruposByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT gru.pk_art_grupos, gru.cod_grupo, gru.descripcion, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";
        $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";
        $q .= " JOIN art_grupos gru ON gru.fk_entidad = cab.fk_entidad AND fam.fk_grupo = gru.pk_art_grupos";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY gru.pk_art_grupos, gru.cod_grupo, gru.descripcion ";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getConsumoMarcasByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT mar.pk_marca_articulo, mar.codigo, mar.descripcion, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";



        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY mar.pk_marca_articulo, mar.codigo, mar.descripcion ";
        $q .= " ORDER BY total DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getConsumoProveedoresByAlbaranQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT pro.pk_proveedor, pro.cod_proveedor, pro.nombre_comercial, IFNULL(SUM(total_lin), 0) AS total FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";
        $q .= " JOIN proveedores pro ON pro.fk_entidad = cab.fk_entidad AND rpro.fk_proveedor = pro.pk_proveedor";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->fechaIni)
                $q .= " AND cab.fecha >= '$filtro->fechaIni' ";

            if ($filtro->fechaFin)
                $q .= " AND cab.fecha <= '$filtro->fechaFin' ";

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->meses) {
                $firstValue = 1;
                $q .= " AND MONTH(cab.fecha) IN (";
                foreach ($filtro->meses as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }


            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY pro.pk_proveedor, pro.cod_proveedor, pro.nombre_comercial ";
        $q .= " ORDER BY total DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getComparativaConsumoByAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) AS total, IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS total_anterior, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) - IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS diferencia FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " ORDER BY diferencia DESC";

        return $q;


    }

    private function getComparativaConsumoMensualByAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT MONTH(fecha) AS mes, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) AS total, IFNULL(SUM(CASE WHEN YEAR(fecha) <> YEAR(NOW()) THEN total_lin END), 0) AS total_anterior, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) - IFNULL(SUM(CASE WHEN YEAR(fecha) <> YEAR(NOW()) THEN total_lin END), 0) AS diferencia FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        if ($filtro && ($filtro->marcas || $filtro->grupos || $filtro->subfamilias || $filtro->familias))
            $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY MONTH(fecha) ";
        $q .= " ORDER BY diferencia DESC";

        return $q;


    }

    private function getComparativaConsumoArticulosByAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT pk_articulo, art.cod_articulo, art.descripcion, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) AS total, IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS total_anterior, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) - IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS diferencia FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";

        if ($filtro && ($filtro->grupos || $filtro->familias))
            $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY pk_articulo, art.cod_articulo, art.descripcion ";
        $q .= " ORDER BY diferencia DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getComparativaConsumoSubfamiliasByAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT pk_art_subfamilias, sub.cod_subfamilia, sub.descripcion, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) AS total, IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS total_anterior, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) - IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS diferencia FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";

        if ($filtro && ($filtro->grupos))
            $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY pk_art_subfamilias, sub.cod_subfamilia, sub.descripcion ";
        $q .= " ORDER BY diferencia DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }

    private function getComparativaConsumoFamiliasByAlbaranesQuery($entityId, $clientePk, $filtro) {

        $q = "SELECT pk_art_familias, fam.cod_familia, fam.descripcion, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) AS total, IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS total_anterior, IFNULL(SUM(CASE WHEN YEAR(fecha) = YEAR(NOW()) THEN total_lin END), 0) - IFNULL(SUM(CASE WHEN ( YEAR(fecha) <> YEAR(NOW()) AND MONTH(fecha) <= MONTH(NOW()) AND (DAY(fecha) <= DAY(NOW()) OR (DAY(fecha) > DAY(NOW()) AND MONTH(fecha) < MONTH(NOW()) ) ) ) THEN total_lin END), 0) AS diferencia FROM albaranes_cab cab
              JOIN albaranes_lin lin ON cab.fk_entidad = lin.fk_entidad AND cab.pk_albaran = lin.fk_albaran_cab AND lin.estado > 0 ";

        $q .= " JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND art.pk_articulo = lin.fk_articulo";
        $q .= " JOIN art_subfamilias sub ON sub.fk_entidad = cab.fk_entidad AND art.fk_subfamilia = sub.pk_art_subfamilias";
        $q .= " JOIN art_familias fam ON fam.fk_entidad = cab.fk_entidad AND sub.fk_familia = fam.pk_art_familias";

        if ($filtro && ($filtro->marcas))
            $q .= " JOIN marca_articulo mar ON mar.fk_entidad = cab.fk_entidad AND art.fk_marca_articulo = mar.pk_marca_articulo";

        if ($filtro && ($filtro->proveedores))
            $q .= " JOIN r_art_pro rpro ON rpro.fk_entidad = cab.fk_entidad AND rpro.fk_articulo = lin.fk_articulo";

        $q .= " WHERE cab.fk_entidad = $entityId AND fk_cliente = '$clientePk' ";

        if ($filtro) {

            if ($filtro->anios) {
                $firstValue = 1;
                $q .= " AND YEAR(cab.fecha) IN (";
                foreach ($filtro->anios as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->proveedores){
                $firstValue = 1;
                $q .= " AND rpro.fk_proveedor IN (";
                foreach ($filtro->proveedores as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->marcas) {
                $firstValue = 1;
                $q .= " AND fk_marca_articulo IN (";
                foreach ($filtro->marcas as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->articulos) {
                $firstValue = 1;
                $q .= " AND lin.fk_articulo IN (";
                foreach ($filtro->articulos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->grupos) {
                $firstValue = 1;
                $q .= " AND fam.fk_grupo IN (";
                foreach ($filtro->grupos as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->familias) {
                $firstValue = 1;
                $q .= " AND sub.fk_familia IN (";
                foreach ($filtro->familias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }

            if ($filtro->subfamilias) {
                $firstValue = 1;
                $q .= " AND art.fk_subfamilia IN (";
                foreach ($filtro->subfamilias as $value) {

                    if ($firstValue) {
                        $q .= "'".$value."'";
                        $firstValue = 0;
                    } else {
                        $q .= ",'".$value."'";
                    }

                }
                $q .= ")";
            }
        }

        $q .= " GROUP BY pk_art_familias, fam.cod_familia, fam.descripcion ";
        $q .= " ORDER BY diferencia DESC";

        if ($filtro->limit)
            $q .= " LIMIT $filtro->limit";

        return $q;


    }



    /**
     * Devuelve el total de pedidos para el filtro indicado.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getTotalPedidos($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getTotalPedidosQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->row(0);
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "total" => $result->total);

    }

    /**
     * Devuelve el total de albaranes para el filtro indicado.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getTotalAlbaranes($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getTotalAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->row(0);
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "total" => $result->total);

    }

    /**
     * Devuelve el total de facturas para el filtro indicado.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getTotalFacturas($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getTotalFacturasQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->row(0);
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "total" => $result->total);

    }

    /**
     * Devuelve el total de albaranes para el filtro indicado agrupado por mes y año.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getTotalAlbaranesByMonth($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getTotalAlbaranesByMonthQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumoMensual" => $result);

    }

    /**
     * Devuelve el consumo por articulo.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getTopArticulosByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getTopArticulosByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "topArticulos" => $result);

    }

    /**
     * Devuelve el consumo por subfamilias.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getConsumoSubfamiliasByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getConsumoSubfamiliasByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo por familias.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getConsumoFamiliasByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getConsumoFamiliasByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo por grupos.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getConsumoGruposByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getConsumoGruposByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo por marcas.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getConsumoMarcasByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getConsumoMarcasByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo por proveedores.
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getConsumoProveedoresByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getConsumoProveedoresByAlbaranQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getComprativaConsumoByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getComparativaConsumoByAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->row(0);
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por mes.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getComprativaConsumoMensualByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getComparativaConsumoMensualByAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por articulo.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getComprativaConsumoArticulosByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getComparativaConsumoArticulosByAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por subfamilia.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getComprativaConsumoSubfamiliasByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getComparativaConsumoSubfamiliasByAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por familia.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $entityId
     * @param $clientePk
     * @param $filtro --> $fechaIni, $fechaFin, $anios, $meses, $proveedores, $marcas, $articulos, $grupos, $familias, $subfamilias, $limit
     * @return $total
     *
     */
    function getComprativaConsumoFamiliasByAlbaran($entityId, $clientePk, $filtro) {

        $this->load->library('esocialmemcache');

        $query = $this->getComparativaConsumoFamiliasByAlbaranesQuery($entityId, $clientePk, $filtro);

        $qHash = getTokenFromString($query);
        $cache = $this->esocialmemcache->get($qHash);

        if ($cache) {
            $result = unserialize($cache);
        } else {
            $query = $this->db->query($query);
            $result = $query->result();
            $this->esocialmemcache->add($qHash, serialize($result), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);

        }

        return array( "consumos" => $result);

    }


}

?>