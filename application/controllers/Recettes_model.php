<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Objectifs extends MY_Controller {

    public function get_total_by_objectifs($type_cible = null, $cible_id = null)
    {
        $this->db->select('periode_debut, periode_fin, SUM(montant) as total_recette');
        $this->db->from('recettes');

        if ($type_cible && $cible_id) {
            $this->db->where('type_cible', $type_cible);
            $this->db->where('cible_id', $cible_id);
        }

        $this->db->group_by(['periode_debut', 'periode_fin']);
        $query = $this->db->get();
        return $query->result_array();
    }

}
