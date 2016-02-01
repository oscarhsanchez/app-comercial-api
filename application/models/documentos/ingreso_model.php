<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_REVENUE);
require_once(APPPATH.ENTITY_REVENUE_LINE);
require_once(APPPATH.ENTITY_EXCEPTION);

class ingreso_model extends CI_Model {

	private function getRevenueQuery() {
		$this->db->select('ingresos_cab.*', false);
		$this->db->from('ingresos_cab');		
	}

	function getRevenueByPk($revenuePk) {

		//CABECERA
		$this->db->where('ingresos_cab.pk_otrosingr', $revenuePk);
		$this->getRevenueQuery();		
		$query = $this->db->get();
		
		$revenue = $query->row(0, 'ingreso');

		//LINEAS
		if ($revenue) {
			$this->db->where('fk_otroingr_cab', $revenuePk);
			$query = $this->db->get('ingresos_lin');
			$revenueLines = $query->result('ingresoLine');

			$revenue->revenueLines = $revenueLines;
		}

		return $revenue;

	}

	function getRevenueCodPk($revenueCod, $entityId) {

		//CABECERA
		$this->db->where('ingresos_cab.pk_otrosingr', $revenueCod);
		$this->db->where('ingresos_cab.fk_entidad', $entityId);
		$this->getRevenueQuery();		
		$query = $this->db->get();
		
		$revenue = $query->row(0, 'ingreso');

		//LINEAS
		if ($revenue) {
			$this->db->where('fk_otroingr_cab', $revenuePk);
			$query = $this->db->get('ingresos_lin');
			$revenueLines = $query->result('ingresoLine');

			$revenue->revenueLines = $revenueLines;
		}

		return $revenue;

	}

	function getRevenueByPkFromIndex($revenuePk) {
		$this->load->library('esocialrestclient');
        $this->esocialrestclient->setHost(SOLR_SERVER);
        $uri = "/revenue/select?qt=standard&wt=json&indent=on&q=*:*&fq=pk_otrosingr:".$revenuePk."&start=0";
        $response = $this->esocialrestclient->get($uri);

        $bodyResponse = json_decode($response["body"]);
        $jsonResponse = $bodyResponse->response;

        if (isset($jsonResponse->numFound) && $jsonResponse->numFound > 0) {

        	$docs = $jsonResponse->docs;
        	if (sizeof($docs) > 0) {
        		$revenue = new ingreso();
        		$revenue->set($docs[0]); 
        		
        		if (isset($docs[0]->revenue_lines)) {                      
				    $revenueLines = $docs[0]->revenue_lines; 

				    $lines = array();
				    foreach ($revenueLines as $line) {			    	
				    	$revenueLine = new ingresoLine();
				    	$lineObj = json_decode($line);	
				        $revenueLine->set($lineObj);	
				        $lines[] = $revenueLine;
				    }       

				    $revenue->revenueLines = $lines;
				}
			    return $revenue;
			    

        	} else {
        		return null;
        	}
	       

        } else {
        	return null;
        }        

	}

	function getRevenueByToken($token, $entityId) {

		//CABECERA
		$this->db->where('ingresos_cab.token', $token);
		$this->getRevenueQuery();
		$query = $this->db->get();

		$revenue = $query->row(0, 'ingreso');

		//LINEAS
		if ($revenue) {
			$this->db->where('fk_otroingr_cab', $revenue->pk_otrosingr);
			$query = $this->db->get('ingresos_lin');
			$revenueLines = $query->result('ingresoLine');

			$revenue->revenueLines = $revenueLines;
		}

		return $revenue;

	}

	function getRevenueByCodFromIndex($revenueCod, $entityId) {
		$this->load->library('esocialrestclient');
        $this->esocialrestclient->setHost(SOLR_SERVER);
        $uri = "/revenue/select?qt=standard&wt=json&indent=on&q:*:*&fq=cod_otroingr:".$revenueCod."&fk_entidad:".$entityId."&start=0";
        $response = $this->esocialrestclient->get($uri);

        $bodyResponse = json_decode($response["body"]);
        $jsonResponse = $bodyResponse->response;

        if (isset($jsonResponse->numFound) && $jsonResponse->numFound > 0) {

        	$docs = $jsonResponse->docs;
        	if (sizeof($docs) > 0) {
        		$revenue = new ingreso();
        		$revenue->set($docs[0]); 

        		if (isset($docs[0]->revenue_lines)) {                      
				    $revenueLines = $docs[0]->revenue_lines; 

				    $lines = array();
				    foreach ($revenueLines as $line) {			    	
				    	$revenueLine = new ingresoLine();
				    	$lineObj = json_decode($line);			        	        
				        $revenueLine->set($lineObj);	
				        $lines[] = $revenueLine;
				    }       

				    $revenue->revenueLines = $lines;
				}

				return $revenue;
			    

        	} else {
        		return null;
        	}
	       

        } else {
        	return null;
        }
	}

	function getRevenueByTokenFromIndex($token, $entityId) {
		$this->load->library('esocialrestclient');
        $this->esocialrestclient->setHost(SOLR_SERVER);
        $uri = "/revenue/select?qt=standard&wt=json&indent=on&q:*:*&fq=token:".$token."&fk_entidad:".$entityId."&start=0";
        $response = $this->esocialrestclient->get($uri);

        $bodyResponse = json_decode($response["body"]);
        $jsonResponse = $bodyResponse->response;

        if (isset($jsonResponse->numFound) && $jsonResponse->numFound > 0) {

        	$docs = $jsonResponse->docs;
        	if (sizeof($docs) > 0) {
        		$revenue = new ingreso();
        		$revenue->set($docs[0]); 

        		if (isset($docs[0]->revenue_lines)) {                      
				    $revenueLines = $docs[0]->revenue_lines; 

				    $lines = array();
				    foreach ($revenueLines as $line) {			    	
				    	$revenueLine = new ingresoLine();
				    	$lineObj = json_decode($line);			        	        
				        $revenueLine->set($lineObj);	
				        $lines[] = $revenueLine;
				    }       

				    $revenue->revenueLines = $lines;
				}

				return $revenue;
			    

        	} else {
        		return null;
        	}
	       

        } else {
        	return null;
        }
	}

	function getFilteredRevenues($entityId, $filters, $pagination, $order) {
		$this->db->start_cache();
		$this->db->where('ingresos_cab.fk_entidad', $entityId);
		if (isset($filters)) {

			foreach ($filters as $filter) {
				$type = $filter->type == '=' ? '' : $filter->type;
				if ($filter->type == '<' || $filter->type == '>' || $filter->type == 'like' || $filter->type == '=') $field = $filter->field . $type;
				$value = $filter->type == 'like' ? "'%" . $filter->value . "'%" : "'" . $filter->value . "'";

				if ($filter->type == 'between') {
					$field = $filter->field . " >= ";
					$this->db->where("ingresos_cab.".$field, $value, false);
					$field = $filter->field . " <= ";
					$value2 = $filter->value2;
					$this->db->where("ingresos_cab.".$field, $value2, false);

				} else {
					$this->db->where("ingresos_cab.".$field, $value, false);
				}
				
			}

		}

		$this->db->stop_cache();

		if (!isset($pagination->progresive) && isset($order)) {
			foreach ($order as $ord) {
				$this->db->order_by("ingresos_cab.".$ord->field, $ord->type);
			}
		}
		

		if (isset($pagination->progresive)) {
			$this->db->limit($pagination->progresive->limit, 0);
			$this->db->where('ingresos_cab.pk_otrosingr >', "'".$pagination->progresive->lastId."'", false);
			$this->db->order_by("created_at", "desc");
			
		} else if (isset($pagination->multipart) && $pagination->multipart->cache && isset($pagination->multipart->cache_token)) {
						
			$this->load->library('esocialmemcache');
			$key = $pagination->multipart->cache_token . "-" . $pagination->multipart->page;
     		$revenues = unserialize($this->esocialmemcache->get($key));
            if (!$revenues) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
     		return array("filters" => $filters, "pagination" => $pagination, "order" => $order, "revenues" => $revenues);

		} else if (isset($pagination->multipart) && !$pagination->multipart->cache) {
					
			$rowcount = $this->db->count_all_results('ingresos_cab');
			$pagination->multipart->totalPages = ceil($rowcount / $pagination->multipart->pageSize);

			$this->db->limit($pagination->multipart->pageSize, $pagination->multipart->pageSize * $pagination->multipart->page);

		}

		$this->getRevenueQuery();
		$query = $this->db->get();

		$revenues = $query->result('ingreso');
		
		$this->db->flush_cache();

		//LINEAS
		foreach ($revenues as $revenue) {
			$this->db->where('ingresos_lin.fk_otroingr_cab', $revenue->pk_otrosingr);
			$query = $this->db->get('ingresos_lin');
			$revenueLines = $query->result('ingresoLine');

			$revenue->revenueLines = $revenueLines;
		}

		if (isset($pagination->multipart) && $pagination->multipart->cache) {
			$this->load->library('esocialmemcache');	
			$rowcount = sizeof($revenues);
			
			$pagination->multipart->totalPages = ceil($rowcount / $pagination->multipart->pageSize);

			if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;
			
			if ($rowcount > $pagination->multipart->pageSize) {
				$chunk_revenues = array_chunk($revenues, $pagination->multipart->pageSize);
				
				$revenues = $chunk_revenues[0];

				$fecha = new DateTime();
				$cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());
				$pagination->multipart->cache_token = $cache_token;

				for ($i=1; $i < sizeof($chunk_revenues); $i++) {
					$this->esocialmemcache->add($cache_token . "-" . $i, serialize($chunk_revenues[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
				}

			}

		}
		

		return array("filters" => $filters, "pagination" => $pagination, "order" => $order, "revenues" => $revenues);

		
	}

	function getFilteredRevenuesFromIndex($entityId, $filters, $pagination, $order) {
		try {

			$this->load->library('esocialrestclient');
		    $this->esocialrestclient->setHost(SOLR_SERVER);

			$uri = "/revenue/select?qt=standard&wt=json&indent=on&q=*:*";

			$_filters = "fq=fk_entidad:".$entityId;
			if (isset($filters)) {
				foreach ($filters as $filter) {
					$_filter = "fq=" . $filter->field . ":";

					if ($filter->type == "=") {
						$_filter = $_filter . '"' . $filter->value . '"';	
					} else if ($filter->type == "like") {
						$_filter = $_filter . '*' . $filter->value . '*';
					} else if ($filter->type == ">") {
						$_filter = $_filter . urlencode('[' . $filter->value . ' TO *]');
					} else if ($filter->type == "<") {
						$_filter = $_filter . urlencode('[* TO ' . $filter->value . ']');
					} else if ($filter->type == "between") {
						$_filter = $_filter . urlencode('[' . $filter->value .' TO ' . $filter->value2 . ']');
					}

					if (strlen($_filters) > 0) $_filters = $_filters . '&';
					$_filters = $_filters . $_filter;

				}
			}

			$_sort = "";
			if (!isset($pagination->progresive) && isset($order)) {
				foreach ($order as $ord) {
					if (strlen($_sort) > 0) $_sort = $_sort . ',';
					$_sort = $_sort . urlencode($ord->field . ' ' . $ord->type);
				}
			}
			
			$_start = "";
			$_rows = "";
			if (isset($pagination->progresive)) {			
				$_rows = $pagination->progresive->limit;
				$_filter = "fq=pk_otrosingr" . ":" . urlencode("[" . ($pagination->progresive->lastId) . " TO *]");
				if (strlen($_filters) > 0) $_filters = $_filters . '&';
				$_filters = $_filters . $_filter;

				if (strlen($_sort) > 0) $_sort = $_sort . ',';
				$_sort = $_sort . urlencode('created_at desc');
							
			} else if (isset($pagination->multipart) && $pagination->multipart->cache && isset($pagination->multipart->cache_token)) {
							$this->load->library('esocialmemcache');
				$key = $pagination->multipart->cache_token . "-" . $pagination->multipart->page;
	     		$revenues = unserialize($this->esocialmemcache->get($key)); 
	     		return array("filters" => $filters, "pagination" => $pagination, "order" => $order, "revenues" => $revenues);

			} else if (isset($pagination->multipart) && !$pagination->multipart->cache) {
				$uri_count = $uri;
				if (strlen($_filters) > 0) $uri_count = $uri_count . "&" . $_filters;			
		        $response = $this->esocialrestclient->get($uri_count);
		        $bodyResponse = json_decode($response["body"]);
		        $jsonResponse = $bodyResponse->response;
				
				$rowcount = $jsonResponse->numFound;
				$pagination->multipart->totalPages = ceil($rowcount / $pagination->multipart->pageSize);


				$_rows = $pagination->multipart->pageSize;
				$_start = $pagination->multipart->pageSize * $pagination->multipart->page;		

			}

			if (strlen($_filters) > 0) $uri = $uri . "&" . $_filters;
			if (strlen($_sort) > 0) $uri = $uri . "&sort=" . $_sort;
			if (strlen($_start) > 0) $uri = $uri . "&start=" . $_start;
			$uri = strlen($_rows) > 0 ? $uri . "&rows=" . $_rows : $uri . "&rows=50000";
			

		    $response = $this->esocialrestclient->get($uri);
		    $bodyResponse = json_decode($response["body"]);
		    $jsonResponse = $bodyResponse->response;

		    $revenues = array();

		    if (isset($jsonResponse->numFound) && $jsonResponse->numFound > 0) {

	        	$docs = $jsonResponse->docs;
	        	foreach ($docs as $doc) {
	        	
	        		$revenue = new ingreso();
	        		$revenue->set($doc);
	        		if (isset($doc->revenue_lines)) {
	        			$revenueLines = $doc->revenue_lines; 

					    $lines = array();
					    foreach ($revenueLines as $line) {			    	
					    	$revenueLine = new ingresoLine();
					    	$lineObj = json_decode($line);			        	        
					        $revenueLine->set($lineObj);	
					        $lines[] = $revenueLine;
					    }       

					    $revenue->revenueLines = $lines;
	        		}                       
				    

				    $revenues[] = $revenue;

				}
			}
			

			if (isset($pagination->multipart) && $pagination->multipart->cache) {
				$this->load->library('esocialmemcache');	
				$rowcount = sizeof($revenues);
				
				$pagination->multipart->totalPages = ceil($rowcount / $pagination->multipart->pageSize);

				if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;
				
				if ($rowcount > $pagination->multipart->pageSize) {
					$chunk_revenues = array_chunk($revenues, $pagination->multipart->pageSize);
					
					$revenues = $chunk_revenues[0];

					$fecha = new DateTime();
					$cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());
					$pagination->multipart->cache_token = $cache_token;

					for ($i=1; $i < sizeof($chunk_revenues); $i++) {
						$this->esocialmemcache->add($cache_token . "-" . $i, serialize($chunk_revenues[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
					}

				}

			}

			//Elimnamos el primer registro del array si es progresivo para que sea mayor que y no igual
			if (isset($pagination->progresive)) {
				if (isset($pagination->progresive->lastId) && !empty($pagination->progresive->lastId)) unset($revenues[0]);
			}
			

			return array("filters" => $filters, "pagination" => $pagination, "order" => $order, "revenues" => $revenues);

		} catch (\Exception $e) {
            $this->log_model->log($e);
            return null;
            
        }

	}


	function createRevenue($revenue) {
		//$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
		$this->load->model("log_model");

		if (!isset($revenue->token)) {
			$revenue->token = getToken();
		}

		if (!isset($revenue->pk_otrosingr)) {
			$revenue->setPk();
		}

		$revenuePk = $revenue->_save(false, false);		
		if ($revenuePk > 0) {
			if (isset($revenue->revenueLines)) {
				$revenueLines = $revenue->revenueLines;				
				foreach ($revenueLines as $line) {
					$line->fk_otroingr_cab = $revenue->pk_otrosingr;
                    $line->fk_entidad = $revenue->fk_entidad;
					if (!isset($line->token)) {
						$line->token = getToken();
					}
					$res = $line->_save(false, true);
					if (!$res) throw new APIexception("Error on ingreso_model->createrevenue. Unable to create ingreso", ERROR_SAVING_DATA, serialize($revenue));
				}
			}
		//	$this->db->trans_complete();			
			return true;
		} else {
			throw new APIexception("Error on ingreso_model->createrevenue. Unable to create ingreso", ERROR_SAVING_DATA, serialize($revenue));
		}	

	}

	function updateRevenue($revenue) {
		//$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
		$this->load->model("log_model");
		if (!isset($revenue->token)) {
			$revenue->token = getToken();
		}

		$result = $revenue->_save(false, false);

		if ($result) {
			if (isset($revenue->revenueLines)) {
				$revenueLines = $revenue->revenueLines;
				foreach ($revenueLines as $line) {
					$line->fk_otroingr_cab = $revenue->pk_otrosingr;
                    $line->fk_entidad = $revenue->fk_entidad;
					//Nos aseguramos que los Tokens no existen
					if ($line->id_ingreso_lin == null) {
						$query = new stdClass();
						$this->db->where('token', $line->token);
						$query = $this->db->get("ingresos_lin");
						$revenueLine = $query->row();						
						if ($revenueLine) $line->id_ingreso_lin = $revenueLine->id_ingreso_lin;
					}
					if (!isset($line->token)) {
						$line->token = getToken();
					}
					$res = $line->_save(false, true);
					if (!$res) throw new APIexception("Error on ingreso_model->updaterevenue. Unable to update ingreso", ERROR_SAVING_DATA, serialize($revenue));
				}
			}
			//$this->db->trans_complete();			
			return true;
		} else {
			throw new APIexception("Error on ingreso_model->updaterevenue. Unable to update ingreso", ERROR_SAVING_DATA, serialize($revenue));
		}	

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
            $return = 'pk_otrosingr';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('ingresos_cab');
        $this->db->where('fk_entidad', $entityId);

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