<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_model extends CI_Model
{
    public function yorumlist(){
        $this->db->from('comment');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function statusupdate($post){
        $this->db->set($post)->WHERE(['id'=>$post->id])->update('comment');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }


}