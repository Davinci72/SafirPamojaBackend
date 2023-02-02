<?php
class Supatribe_model extends CI_Model{
    public function createUser($phone,$names,$email,$idno,$vtype,$role){
        $data = array(
            'phone'=>$phone,
            'names'=>$names,
            'email'=>$email,
            'id_no'=>$idno,
            'role'=>$role,
            'vtype'=>$vtype,
            'date_c'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('supatribe_users', $data);
    }
    public function Authenticate($uname,$password){
        $query = $this->db->get_where('supatribe_users', array('email' => $uname, 'password'=>$password));
        return $query->result_array();
    }
    public function checkPayments($phone){
        $query = $this->db->get_where('mpesa_transactions', array('phone' => $phone));
        return $query->result_array();  
    }
    public function getDeTails($email){
        $query = $this->db->get_where('supatribe_users', array('email' => $email));
        return $query->result_array();
    }
    public function createTrip($data){
        return $this->db->insert('supatribe_trips', $data);
    }
    public function GetTrips($destination){
        $query = $this->db->get_where('supatribe_trips', array('destination' => $destination));
        return $query->result_array();
    }
    public function getUserDetails($email){
        $query = $this->db->get_where('supatribe_users', array('email' => $email));
        return $query->result_array();
    }
    public function getDriver($locale){
        $query = $this->db->get_where('supatribe_trips', array('destinationName' => $locale));
        return $query->result_array();
    }
}