<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

    // Ajouter un nouvel utilisateur avec ses permissions
    public function add_user($data) {
        if (!isset($data['basic']) || !is_array($data['basic'])) {
            log_message('error', 'add_user() - Données de base manquantes ou invalides');
            return false;
        }

        $userins = $data['basic'];

        // Supprimer u_id s’il est présent pour éviter une insertion invalide
        unset($userins['u_id']);

        // Sécuriser le mot de passe
        if (!empty($userins['u_password'])) {
            $userins['u_password'] = md5($userins['u_password']);
        }

        // Ajouter champs par défaut
        $userins['u_created_date'] = date('Y-m-d H:i:s');
        $userins['u_isactive'] = 1;

        // Insertion dans login
        $insert = $this->db->insert('login', $userins);
        if (!$insert) {
            log_message('error', 'User_model::add_user - Échec de l’insertion utilisateur');
            return false;
        }

        $uid = $this->db->insert_id();

        // Ajout des rôles si fournis
        if (!empty($data['permissions']) && is_array($data['permissions'])) {
            $role = $data['permissions'];
            $role['lr_u_id'] = $uid;
            $this->db->insert('login_roles', $role);
        }

        return true;
    }

    // Récupérer tous les utilisateurs
    public function getall_user() {
        return $this->db->select('*')
                        ->from('login')
                        ->order_by('u_id','desc')
                        ->get()
                        ->result_array();
    }

    // Détails d’un utilisateur par ID
    public function get_userdetails($u_id) {
        $this->db->select("*");
        $this->db->from('login');
        $this->db->join('login_roles', 'login.u_id=login_roles.lr_u_id');
        $this->db->where('login.u_id', $u_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    // Mise à jour d’un utilisateur
    public function update_user($data) {
        $userup = $data['basic'];

        // Mise à jour du mot de passe si fourni
        if (isset($userup['u_password']) && !empty($userup['u_password'])) {
            $userup['u_password'] = md5($userup['u_password']);
        } else {
            unset($userup['u_password']); // Ne pas écraser l'ancien mot de passe
        }

        // Mise à jour table login
        $this->db->where('u_id', $userup['u_id']);
        $this->db->update('login', $userup);

        // Mise à jour des permissions dans login_roles
        if (!empty($data['permissions']) && is_array($data['permissions'])) {
            $role = $data['permissions'];
            $this->db->where('lr_u_id', $userup['u_id']);
            return $this->db->update('login_roles', $role);
        }

        return true;
    }

    // Supprimer un utilisateur + ses rôles
    public function delete_user($u_id) {
        if (!is_numeric($u_id)) return false;

        // Supprimer d'abord dans login_roles
        $this->db->where('lr_u_id', $u_id);
        $this->db->delete('login_roles');

        // Ensuite supprimer dans login
        $this->db->where('u_id', $u_id);
        return $this->db->delete('login');
    }
}
