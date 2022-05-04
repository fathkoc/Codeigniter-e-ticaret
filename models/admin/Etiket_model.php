<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etiket_model extends CI_Model
{
    public function insertetiket($post){
        $this->db->set($post)->insert('etiket');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }

    }
    public function etiketlist(){
        $this->db->from('etiket');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }


}
