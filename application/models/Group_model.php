<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Group_model extends CI_Model {

    // Récupérer tous les groupes
    public function get_all_groups() {
        return $this->db->get('groups')->result_array();
    }

    // Récupérer un groupe par ID avec ses permissions
    public function get_group($id) {
        $group = $this->db->get_where('groups', ['id' => $id])->row_array();
        if (!$group) return null;

        $permissions = $this->db
            ->select('permission_name')
            ->from('group_permissions')
            ->where('group_id', $id)
            ->get()
            ->result_array();

        $group['permissions'] = array_column($permissions, 'permission_name');
        return $group;
    }

    // Créer un groupe + ses permissions
    public function create_group($name, $description, $permissions = []) {
        $this->db->insert('groups', [
            'name' => $name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $group_id = $this->db->insert_id();

        $this->set_group_permissions($group_id, $permissions);
        return $group_id;
    }

    // Mettre à jour un groupe + ses permissions
    public function update_group($id, $name, $description, $permissions = []) {
        $this->db->where('id', $id)
                 ->update('groups', [
                     'name' => $name,
                     'description' => $description
                 ]);

        $this->set_group_permissions($id, $permissions);
        return true;
    }

    // Supprimer un groupe + ses permissions
    public function delete_group($id) {
        $this->db->where('group_id', $id)->delete('group_permissions');
        $this->db->where('id', $id)->delete('groups');
        return true;
    }

    // Définir les permissions d’un groupe
    private function set_group_permissions($group_id, $permissions) {
        $this->db->where('group_id', $group_id)->delete('group_permissions');

        if (!empty($permissions)) {
            $data = [];
            foreach ($permissions as $perm) {
                $data[] = [
                    'group_id' => $group_id,
                    'permission_name' => $perm
                ];
            }
            $this->db->insert_batch('group_permissions', $data);
        }
    }
}
