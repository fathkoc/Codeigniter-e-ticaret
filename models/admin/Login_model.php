<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
 public function login($post){
     $this->db->from('login');
     $this->db->WHERE(['username'=>$post->username]);
     $return_query = $this->db->get();
     if($return_query->num_rows() > 0) {
         return $return_query->row();
     } else {
         return false;
     }
  
 }


}
