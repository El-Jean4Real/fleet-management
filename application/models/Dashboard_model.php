<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    /* ===========================
     *      STATS GLOBALES
     * =========================== */
    public function getdashboard_info()
    {
        return [
            'tot_vehicles'      => $this->db->count_all('vehicles'),
            'tot_drivers'       => $this->db->count_all('drivers'),
            'tot_customers'     => $this->db->count_all('customers'),
            'tot_today_trips'   => $this->db->where('t_start_date', date('Y-m-d'))->count_all_results('trips'),
            'tot_today_income'  => $this->sumIncomeExpense('income', date('Y-m-d')),
            'tot_today_expense' => $this->sumIncomeExpense('expense', date('Y-m-d')),
        ];
    }

    private function sumIncomeExpense($type, $date)
    {
        return $this->db->select_sum('ie_amount')
            ->from('incomeexpense')
            ->where(['ie_date' => $date, 'ie_type' => $type])
            ->get()->row()->ie_amount ?? 0;
    }

    /* ===========================
     *      DRIVERS & VEHICULES
     * =========================== */
    public function get_driverdetails($d_id)
    {
        return $this->db->get_where('drivers', ['d_id' => $d_id])->result_array();
    }

    public function get_vechicle_currentlocation()
    {
        $vehicles = $this->db->select('v_id,v_registration_no,v_name')->from('vehicles')->get()->result_array();
        $lastlocation = [];

        foreach ($vehicles as $vehicle) {
            $location = $this->db->select('latitude,longitude')
                ->from('positions')
                ->where('v_id', $vehicle['v_id'])
                ->order_by('id', 'desc')
                ->get()->row();

            $vehicle['current_location'] = $location
                ? $this->getaddress($location->latitude, $location->longitude)
                : '';

            $lastlocation[] = $vehicle;
        }

        return $lastlocation;
    }

    public function getvechicle_status()
    {
        $sql = "
            SELECT t_vechicle, t_trip_status, v.v_name, v.v_registration_no
            FROM trips t
            INNER JOIN vehicles v ON t.t_vechicle = v.v_id
            WHERE t_id IN (
                SELECT MAX(t_id) FROM trips GROUP BY t_vechicle
            )
            ORDER BY t_trip_status
        ";

        return $this->db->query($sql)->result_array();
    }

    /* ===========================
     *      RAPPELS & CHARTS
     * =========================== */
    public function get_todayreminder()
    {
        return $this->db->get_where('reminder', [
            'r_date' => date('Y-m-d'),
            'r_isread' => 0
        ])->result_array();
    }

    public function get_iechartdata()
    {
        $dates = $this->createDateRangeArray(date('Y-m-d', strtotime('-5 day')), date('Y-m-d'));
        $arr = [];

        foreach ($dates as $d) {
            $arr[$d] = [
                'income'  => $this->sumIncomeExpense('income', $d),
                'expense' => $this->sumIncomeExpense('expense', $d)
            ];
        }

        return $arr;
    }

    /* ===========================
     *      OUTILS
     * =========================== */
    private function createDateRangeArray($start, $end)
    {
        $range = [];
        $current = strtotime($start);
        $endTime = strtotime($end);

        while ($current <= $endTime) {
            $range[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        return $range;
    }

    private function getaddress($lat, $lng)
    {
        $apiKey = $this->config->item('google_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key={$apiKey}&latlng={$lat},{$lng}&sensor=false";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result);
        return (!empty($data) && $data->status == "OK")
            ? $data->results[1]->formatted_address
            : '';
    }

    /* ===========================
     *      OBJECTIFS & PÉRIODES
     * =========================== */
    public function get_objectifs_avec_depenses($periode_type = 'hebdomadaire', $date_debut = null, $date_fin = null)
    {
        [$date_debut, $date_fin] = $this->resolvePeriode($periode_type, $date_debut, $date_fin);

        $sql = "
            SELECT 
                o.id, o.periode_type, o.periode_debut, o.periode_fin, o.montant_objectif,
                v.v_id, v.v_name AS vehicule_nom,

                -- Recettes automatiques
                (SELECT IFNULL(SUM(tp.tp_amount), 0)
                 FROM trip_payments tp
                 WHERE tp.tp_v_id = o.cible_id
                   AND DATE(tp.tp_created_date) BETWEEN ? AND ?) AS montant_realise,

                -- Recettes manuelles
                (SELECT IFNULL(SUM(ie.ie_amount), 0)
                 FROM incomeexpense ie
                 WHERE ie.ie_v_id = o.cible_id
                   AND ie.ie_type = 'income'
                   AND DATE(ie.ie_date) BETWEEN ? AND ?) AS montant_manuel,

                -- Dépenses
                (SELECT IFNULL(SUM(ie.ie_amount), 0)
                 FROM incomeexpense ie
                 WHERE ie.ie_v_id = o.cible_id
                   AND ie.ie_type = 'expense'
                   AND DATE(ie.ie_date) BETWEEN ? AND ?) AS depenses,

                -- Taux atteinte
                ROUND((
                    (
                        (SELECT IFNULL(SUM(tp.tp_amount), 0)
                         FROM trip_payments tp
                         WHERE tp.tp_v_id = o.cible_id
                           AND DATE(tp.tp_created_date) BETWEEN ? AND ?)
                        +
                        (SELECT IFNULL(SUM(ie.ie_amount), 0)
                         FROM incomeexpense ie
                         WHERE ie.ie_v_id = o.cible_id
                           AND ie.ie_type = 'income'
                           AND DATE(ie.ie_date) BETWEEN ? AND ?)
                    ) / o.montant_objectif
                ) * 100, 2) AS taux_atteinte

            FROM objectifs_recette o
            LEFT JOIN vehicles v ON v.v_id = o.cible_id
            WHERE o.type_cible = 'vehicule'
              AND o.periode_type = ?
              AND o.periode_debut = ?
              AND o.periode_fin = ?
            ORDER BY v.v_name ASC
        ";

        $params = [
            $date_debut, $date_fin, // montant_realise
            $date_debut, $date_fin, // montant_manuel
            $date_debut, $date_fin, // depenses
            $date_debut, $date_fin, // taux_atteinte - auto
            $date_debut, $date_fin, // taux_atteinte - manuel
            $periode_type, $date_debut, $date_fin // WHERE final
        ];

        return $this->db->query($sql, $params)->result_array();
    }

    private function resolvePeriode($type, $debut, $fin)
    {
        if (!$debut || !$fin) {
            if ($type === 'hebdomadaire') {
                $debut = date('Y-m-d', strtotime('monday this week'));
                $fin   = date('Y-m-d', strtotime('sunday this week'));
            } elseif ($type === 'mensuel') {
                $debut = date('Y-m-01');
                $fin   = date('Y-m-t');
            }
        }
        return [$debut, $fin];
    }
}
