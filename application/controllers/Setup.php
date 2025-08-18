<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->dbforge(); // Utilitaire CodeIgniter pour créer/modifier des tables
    }

    public function init_groups_system() {
        // === 1. Table groups ===
        if (!$this->db->table_exists('groups')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100'
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('groups', TRUE);
            echo "Table 'groups' créée ✅<br>";
        } else {
            echo "Table 'groups' déjà existante ⏩<br>";
        }

        // === 2. Table group_permissions ===
        if (!$this->db->table_exists('group_permissions')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'group_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ],
                'permission_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '150'
                ]
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['group_id']);
            $this->dbforge->create_table('group_permissions', TRUE);
            echo "Table 'group_permissions' créée ✅<br>";
        } else {
            echo "Table 'group_permissions' déjà existante ⏩<br>";
        }

        // === 3. Table user_groups ===
        if (!$this->db->table_exists('user_groups')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ],
                'group_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ]
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['user_id', 'group_id']);
            $this->dbforge->create_table('user_groups', TRUE);
            echo "Table 'user_groups' créée ✅<br>";
        } else {
            echo "Table 'user_groups' déjà existante ⏩<br>";
        }

        echo "<hr>Vérification terminée ✅";
    }
}
