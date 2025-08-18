<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

    private $permission_keys = [
        // Vehicle
        'lr_vech_list','lr_vech_list_view','lr_vech_list_edit','lr_vech_add',
        // Vehicle Group
        'lr_vech_group','lr_vech_group_add','lr_vech_group_action',
        // Driver
        'lr_drivers_list','lr_drivers_list_edit','lr_drivers_add',
        // Bookings
        'lr_trips_list','lr_trips_list_edit','lr_trips_add',
        // Customer
        'lr_cust_list','lr_cust_edit','lr_cust_add',
        // Fuel
        'lr_fuel_list','lr_fuel_edit','lr_fuel_add',
        // Reminder
        'lr_reminder_list','lr_reminder_delete','lr_reminder_add',
        // Income Expense (indépendant)
        'lr_ie_list','lr_ie_edit','lr_ie_add',
        // Objectifs
        'lr_objectifs_view','lr_objectifs_add','lr_objectifs_edit','lr_objectifs_delete','lr_objectifs_stats',
        // Recettes
        'lr_recette_view','lr_recette_add','lr_recette_edit','lr_recette_delete',
        // Notifications
        'lr_notifications_view','lr_notifications_manage',
        // Utilisateurs
        'lr_user_list','lr_user_add','lr_user_edit_roles','lr_user_delete',
        // Tracking
        'lr_tracking','lr_liveloc',
        // Geofence
        'lr_geofence_add','lr_geofence_list','lr_geofence_delete','lr_geofence_events',
        // Reports / Settings
        'lr_reports','lr_settings',
    ];

    /** Vérifie si une ligne login_roles existe déjà pour l'utilisateur */
    private function has_roles_row($u_id) {
        return (bool)$this->db->select('lr_u_id')
            ->from('login_roles')
            ->where('lr_u_id', $u_id)
            ->get()->row();
    }

    /** Normalise le tableau des permissions : toute clé manquante => 0 */
    private function normalize_permissions($permissions) {
        $out = [];
        foreach ($this->permission_keys as $k) {
            $out[$k] = !empty($permissions[$k]) ? 1 : 0;
        }
        return $out;
    }

    /** Ajouter un nouvel utilisateur + permissions */
    public function add_user($data) {
        if (empty($data['basic']) || !is_array($data['basic'])) {
            log_message('error', 'add_user() - Données de base manquantes ou invalides');
            return false;
        }

        $userins = $data['basic'];
        unset($userins['u_id']); // sécurité

        // Hash mot de passe (compatibilité: md5 comme existant)
        if (!empty($userins['u_password'])) {
            $userins['u_password'] = md5($userins['u_password']);
        } else {
            unset($userins['u_password']); // ne pas insérer vide
        }

        // Valeurs par défaut
        $userins['u_created_date'] = date('Y-m-d H:i:s');
        if (!isset($userins['u_isactive'])) {
            $userins['u_isactive'] = 1;
        }

        $this->db->trans_start();

        $insert = $this->db->insert('login', $userins);
        if (!$insert) {
            $this->db->trans_rollback();
            log_message('error', 'User_model::add_user - échec insertion login');
            return false;
        }

        $uid = $this->db->insert_id();

        // Permissions (ligne toujours créée, même vide)
        $perm = !empty($data['permissions']) && is_array($data['permissions']) ? $data['permissions'] : [];
        $perm = $this->normalize_permissions($perm);
        $perm['lr_u_id'] = $uid;

        $this->db->insert('login_roles', $perm);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Tous les utilisateurs */
    public function getall_user() {
        return $this->db->select('*')
                        ->from('login')
                        ->order_by('u_id','desc')
                        ->get()
                        ->result_array();
    }

    /** Détails utilisateur + permissions (LEFT JOIN si pas encore de droits) */
    public function get_userdetails($u_id) {
        $this->db->select('login.*, login_roles.*');
        $this->db->from('login');
        $this->db->join('login_roles', 'login.u_id=login_roles.lr_u_id', 'left');
        $this->db->where('login.u_id', $u_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /** Mise à jour utilisateur + permissions */
    public function update_user($data) {
        if (empty($data['basic']) || !is_array($data['basic'])) {
            return false;
        }

        $userup = $data['basic'];
        if (empty($userup['u_id']) || !is_numeric($userup['u_id'])) {
            return false;
        }

        $u_id = (int)$userup['u_id'];

        // Mot de passe : seulement si fourni
        if (!empty($userup['u_password'])) {
            $userup['u_password'] = md5($userup['u_password']);
        } else {
            unset($userup['u_password']);
        }

        // Photo: si vide, ne pas écraser
        if (isset($userup['u_photo']) && $userup['u_photo'] === '') {
            unset($userup['u_photo']);
        }

        $this->db->trans_start();

        $this->db->where('u_id', $u_id)->update('login', $userup);

        // Permissions normalisées (indépendance garantie)
        $perm = !empty($data['permissions']) && is_array($data['permissions']) ? $data['permissions'] : [];
        $perm = $this->normalize_permissions($perm);

        if ($this->has_roles_row($u_id)) {
            $this->db->where('lr_u_id', $u_id)->update('login_roles', $perm);
        } else {
            $perm['lr_u_id'] = $u_id;
            $this->db->insert('login_roles', $perm);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Suppression utilisateur + ses rôles */
    public function delete_user($u_id) {
        if (!is_numeric($u_id)) return false;

        $this->db->trans_start();
        $this->db->where('lr_u_id', $u_id)->delete('login_roles');
        $this->db->where('u_id', $u_id)->delete('login');
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
