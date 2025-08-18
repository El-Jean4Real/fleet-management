<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('userpermission')) {
    function userpermission($perm) {
        if (!isset($_SESSION['userroles']) || !is_array($_SESSION['userroles'])) return false;

        // OPTIONNEL : enlever ce bypass si tu veux que même les admins soient limités
        // if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') return true;

        return in_array(trim(strtolower($perm)), array_map('strtolower', array_map('trim', $_SESSION['userroles'])));
    }
}

if (!function_exists('has_any_permission')) {
    function has_any_permission($permissions = []) {
        foreach ($permissions as $perm) {
            if (userpermission($perm)) return true;
        }
        return false;
    }
}
