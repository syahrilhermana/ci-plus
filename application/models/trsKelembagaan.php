<?php
/**
 * Created by syaharil.hermana@gmail.com
 */

use Eloquent\Model as Model;
use Guard as Security;

class TrsKelembagaan extends Model {
    protected $table = "trs_kelembagaan-form11";
    protected $primary = "trs_kelembagaan_id";
    var $CI = NULL;

    public function __construct()
    {
        parent::__construct();

        $this->CI =& get_instance();
    }

    public function get_trs_data_desa($offset, $limit, $search, $sortCol, $sortDir)
    {
        $this->CI->db->select($this->table.'.*');
        $this->CI->db->join('mst_user', 'mst_user.mst_user_id = '.$this->table.'.trs_kelembagaan_created_by', 'left');
        $this->CI->db->where($this->table.'.trs_kelembagaan_satker', Security::get_satker());

        if(Security::get_role() != 'all'){
            $this->CI->db->where('mst_user.mst_role', Security::get_role());
        }

        if($search != ""){
            $this->CI->db->like("trs_kelembagaan_desa", $search);
        }
        if($sortCol != "") $this->CI->db->order_by($sortCol, $sortDir);

        return $this->CI->db->get($this->table, $limit, $offset);
    }

    public function get_trs_data_desa_count($search = "")
    {
        $this->CI->db->select($this->primary);
        if($search != "") {
            $this->CI->db->like("trs_kelembagaan_desa", $search);
        }

        return $this->CI->db->count_all_results($this->table);
    }
}