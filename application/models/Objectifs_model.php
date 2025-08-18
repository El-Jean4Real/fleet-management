<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Objectifs_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Insère un nouvel objectif
     */
    public function insert($data)
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }
        return $this->db->insert('objectifs_recette', $data);
    }

    /**
     * Récupère tous les objectifs (avec ou sans filtres)
     */
    public function search($type_cible = null, $cible_id = null, $periode_debut = null, $periode_fin = null)
    {
        if ($type_cible) {
            $this->db->where('type_cible', $type_cible);
        }

        if ($cible_id) {
            $this->db->where('cible_id', $cible_id);
        }

        if ($periode_debut) {
            $this->db->where('periode_debut >=', $periode_debut);
        }

        if ($periode_fin) {
            $this->db->where('periode_fin <=', $periode_fin);
        }

        return $this->db->order_by('periode_debut', 'DESC')
                        ->get('objectifs_recette')
                        ->result_array();
    }

    /**
     * Récupère un objectif par son ID
     */
    public function get_by_id($id)
    {
        if (!is_numeric($id)) {
            return null;
        }
        return $this->db->get_where('objectifs_recette', ['id' => $id])->row_array();
    }

    /**
     * Supprime un objectif
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            return false;
        }
        return $this->db->delete('objectifs_recette', ['id' => $id]);
    }

    /**
     * Met à jour un objectif
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || !is_array($data) || empty($data)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update('objectifs_recette', $data);
    }

    /**
     * Objectifs par période
     */
    public function get_by_period($start_date, $end_date)
    {
        return $this->db->where('periode_debut >=', $start_date)
                        ->where('periode_fin <=', $end_date)
                        ->order_by('periode_debut', 'ASC')
                        ->get('objectifs_recette')
                        ->result_array();
    }
    
    /**
     * Retourne le total des objectifs (agrégé)
     */
    public function get_sum_objectif($type_cible = null, $cible_id = null, $start = null, $end = null)
    {
        $this->db->select('SUM(montant_objectif) as total');
        $this->db->from('objectifs_recette');

        if ($type_cible) $this->db->where('type_cible', $type_cible);
        if ($cible_id) $this->db->where('cible_id', $cible_id);
        if ($start) $this->db->where('periode_debut >=', $start);
        if ($end) $this->db->where('periode_fin <=', $end);

        $row = $this->db->get()->row();
        return $row ? $row->total : 0;
    }

}
