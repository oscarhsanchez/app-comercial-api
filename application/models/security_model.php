<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class security_model extends CI_Model {

	function hasPermission($requiredPermissions, $submodule, $userId, $roles) {

        foreach ($roles as $role) {

            $q = "SELECT * FROM security_submodule_permission sec
                    JOIN security_submodule sub on sub.id = sec.fk_security_submodule
                    JOIN role ON sec.fk_role = role.id
                    WHERE sub.code = '$submodule' AND (role.code = '$role' OR fk_user = $userId)";

            $query = $this->db->query($q);

            $result = $query->row();

            if ($result) {
                $permissions = explode(",", $result->permissions);

                $hasRequiredPermissions = true;
                foreach ($requiredPermissions AS $permission ) {
                    if (!in_array($permission, $permissions))
                        $hasRequiredPermissions = false;
                }

                if ($hasRequiredPermissions)
                    return true;
            }

        }

        return false;


	}

}

?>