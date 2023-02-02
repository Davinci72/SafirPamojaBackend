<?php 
class Clients_model extends CI_Model{
    public function getClients($uid,$limit,$start){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('clients', array('uid' => $uid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getCustomerByID($custID,$uid){
        $query = $this->db->query('SELECT * FROM clients WHERE id = "'.$custID.'" AND uid="'.$uid.'"');
        return $query->result_array();
    }
    public function get_count($uid){
        $this->db->where('uid',$uid);
        $this->db->from("clients");
        return $num = $this->db->count_all_results();
    }
   
    public function get_count_pending_transactions($uid){
        $this->db->where('uid',$uid);
        $this->db->from("credit");
        return $num = $this->db->count_all_results();
    }
    public function getPendingPayments($uid,$limit,$start){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('credit', array('uid' => $uid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function addNewClient($uid){
        $data = array(
            'client_name'=>$this->input->post('client_name'),
            'phone_number'=>$this->input->post('phone_number'),
            'uid'=>$uid,
            'status'=>1,
            'date_c'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('clients', $data);
    }
   
    public function logCredit($uid,$customerID,$purchase_id,$balance){
        $data = array(
            'client_id'=>$customerID,
            'uid'=>$uid,
            'purchase_id'=>$purchase_id,
            'balance'=>$balance,
            'status'=>1,
            'date_c'=>date("Y-m-d h:i:s"),
            'date_last_updated'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('credit', $data);
    }
    public function updateClient(){
        $data = array(
            'client_name' => $this->input->post('prod'),
            'phone_number'=> $this->input->post('phone')
        );
        $this->db->where('id',$this->input->post('pid'));
        $this->db->where('uid',$this->input->post('user'));
        return $this->db->update('clients',$data);
    }
    public function deleteClient(){
        $this->db->where('id',$this->input->post('pid'));
        $this->db->where('uid',$this->input->post('user'));
        return $this->db->delete('clients');
    }
}