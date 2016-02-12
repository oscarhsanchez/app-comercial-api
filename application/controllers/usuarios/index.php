<?php

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);


class index extends generic_controller
{


	function __construct()
    {
    	parent::__construct();
        $this->load->model("usuarios/user_model");
    }

    /**
     * Example Like : field1 = [%search%] & field2 = [%search & field3 = search%]
     *
     * @header Authorization
     *
     * @param offset (Opcional)
     * @param limit (Opcional)
     * @param sort (Opcional) : [field1_ASC, field2_DESC]
     * @param pagination (Opcional): {"active":1, "pageSize": 200, "page":0, "totalPages":null, "cache_token":null}
     *
     * @RequiresPermission ["users", "R"]
     *
     */
    public function index_get()
    {
        $this->checkSecurity(__FUNCTION__);

        $offset = $this->get("offset");
        $limit = $this->get("limit");
        $sort = $this->get("sort");
        $pagination = json_decode($this->get('pagination'));

        try {
            $result = $this->user_model->getAll($this->get(), $this->session->fk_pais, $offset, $limit, $sort, $pagination);
            if ($result["pagination"])
                $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'users' => $result["result"]), 200);
            else
                $this->response(array('result' => 'OK', 'users' => $result["result"]), 200);
        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }
    }

    /**
     *
     * @RequiresPermission ["users", "C"]
     *
     */
    function index_post()
    {
        $data = array('returned: '. $this->post('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["users", "U"]
     *
     */
    function index_put()
    {
        $data = array('returned: '. $this->put('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["users", "D"]
     *
     */
    function index_delete()
    {
        $data = array('returned: '. $this->delete('id'));
        $this->response($data);
    }


}

?>