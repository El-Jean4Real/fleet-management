<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_audit extends CI_Controller {
    public function index() {
        // 1. Get all permissions from DB
        $db_permissions = $this->db->list_fields('login_roles');
        $db_permissions = array_filter($db_permissions, function($item) {
            return strpos($item, 'lr_') === 0;
        });

        // 2. Get all permissions used in code
        $code_permissions = [];
        
        // Scan controllers
        $controllers = scandir(APPPATH.'controllers');
        foreach ($controllers as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $content = file_get_contents(APPPATH.'controllers/'.$file);
                preg_match_all('/userpermission\(\'([^\']+)\'\)/', $content, $matches);
                if (!empty($matches[1])) {
                    $code_permissions = array_merge($code_permissions, $matches[1]);
                }
            }
        }

        // 3. Compare
        $unused_in_db = array_diff($code_permissions, $db_permissions);
        $unused_in_code = array_diff($db_permissions, $code_permissions);

        // 4. Display results
        echo "<h2>Permissions Audit Report</h2>";
        
        echo "<h3>Missing in Database (".count($unused_in_db).")</h3>";
        echo "<ul>";
        foreach ($unused_in_db as $perm) {
            echo "<li>$perm</li>";
        }
        echo "</ul>";

        echo "<h3>Unused in Code (".count($unused_in_code).")</h3>";
        echo "<ul>";
        foreach ($unused_in_code as $perm) {
            echo "<li>$perm</li>";
        }
        echo "</ul>";

        echo "<h3>Complete Structure Analysis</h3>";
        echo "<pre>";
        print_r($this->analyze_menu_structure());
        echo "</pre>";
    }

    private function analyze_menu_structure() {
        $structure = [];
        
        // Example - adapt based on your actual menu system
        $menu_items = [
            'Dashboard' => ['view'],
            'Vehicle' => ['list', 'add', 'edit', 'delete'],
            'Income & Expenses' => ['list', 'add', 'edit', 'delete', 'objectifs'],
            // Add all your menu items
        ];

        foreach ($menu_items as $menu => $actions) {
            foreach ($actions as $action) {
                $perm_name = 'lr_'.strtolower(str_replace(' ', '_', $menu)).'_'.$action;
                $structure[$menu][] = [
                    'action' => $action,
                    'permission' => $perm_name,
                    'exists_in_db' => $this->db->field_exists($perm_name, 'login_roles'),
                    'used_in_code' => $this->check_permission_usage($perm_name)
                ];
            }
        }

        return $structure;
    }

    private function check_permission_usage($permission) {
        // Check controllers
        $controllers = scandir(APPPATH.'controllers');
        foreach ($controllers as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $content = file_get_contents(APPPATH.'controllers/'.$file);
                if (strpos($content, $permission) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}