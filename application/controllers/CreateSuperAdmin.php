<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CreateSuperAdmin extends CI_Controller {

    public function index() {
        $this->load->database();
        $this->load->model('User_model'); // adapte au modèle utilisateur existant
        $this->load->model('Role_model'); // adapte au modèle rôles existants
        $this->load->model('Permission_model'); // adapte au modèle permissions existants

        // 1. Création du rôle Super Admin s'il n'existe pas
        $role = $this->db->get_where('roles', ['name' => 'Super Admin'])->row();
        if (!$role) {
            $this->db->insert('roles', ['name' => 'Super Admin', 'description' => 'Accès complet']);
            $role_id = $this->db->insert_id();
            echo "Rôle Super Admin créé avec l'ID $role_id.<br>";
        } else {
            $role_id = $role->id;
            echo "Rôle Super Admin déjà existant (ID $role_id).<br>";
        }

        // 2. Création de l'utilisateur superadmin s'il n'existe pas
        $user = $this->db->get_where('users', ['username' => 'superadmin'])->row();
        if (!$user) {
            $data_user = [
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                // Assure-toi d’adapter le champ password et la méthode de hash selon ton projet
                'password' => password_hash('Admin@123', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('users', $data_user);
            $user_id = $this->db->insert_id();
            echo "Utilisateur superadmin créé avec l'ID $user_id.<br>";
        } else {
            $user_id = $user->id;
            echo "Utilisateur superadmin déjà existant (ID $user_id).<br>";
        }

        // 3. Associer le rôle Super Admin à l'utilisateur superadmin
        $exists = $this->db->get_where('user_roles', ['user_id' => $user_id, 'role_id' => $role_id])->row();
        if (!$exists) {
            $this->db->insert('user_roles', ['user_id' => $user_id, 'role_id' => $role_id]);
            echo "Rôle Super Admin assigné à l'utilisateur superadmin.<br>";
        } else {
            echo "Utilisateur superadmin a déjà le rôle Super Admin.<br>";
        }

        // 4. Assigner toutes les permissions existantes au rôle Super Admin
        $permissions = $this->db->get('permissions')->result();
        foreach ($permissions as $perm) {
            $exists_perm = $this->db->get_where('role_permissions', ['role_id' => $role_id, 'permission_id' => $perm->id])->row();
            if (!$exists_perm) {
                $this->db->insert('role_permissions', ['role_id' => $role_id, 'permission_id' => $perm->id]);
            }
        }
        echo "Toutes les permissions ont été assignées au rôle Super Admin.<br>";

        echo "<br><strong>Super Admin créé avec succès !</strong><br>";
        echo "Identifiants de connexion : <br>Utilisateur : superadmin<br>Mot de passe : Admin@123<br>";
        echo "Change ce mot de passe immédiatement après connexion.<br>";
    }
}
