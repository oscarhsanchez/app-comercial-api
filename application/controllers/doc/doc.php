<?php defined('BASEPATH') OR exit('No direct script access allowed');

class doc extends CI_Controller {
	
		
	function __construct()
	{
		parent::__construct();
		       
	}
    
    public function index()	{  
        $data["url"] = "http://wikking.rbconsulting.es";

        $ws = $this->uri->segment(4, "login"); 
        $method = $this->uri->segment(5, "phoneRegister");

        //echo "WS: $ws , Method: $method";

        $data["header"] = $this->load->view('doc/header', null, true);
        //LOAD SIDE BAR
        if ($ws == "login") 
        	$data["sidebar"] = $this->load->view('doc/sidebarLogin', null, true); 
        if ($ws == "recask") 
        	$data["sidebar"] = $this->load->view('doc/sidebarPlace', null, true); 
        if ($ws == "usuarios")
        	$data["sidebar"] = $this->load->view('doc/sidebarSpotIn', null, true); 
        
        //$data["footer"] = $this->load->view('footer', $datos_vista, true); 
                    
        $this->load->view('doc/'.$method,$data);
    }
}