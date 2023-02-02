<?php
class Authentication extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
    }
    public function sessIgniter(){
        
    }
    public function authenticator(){
        $sess_id = $this->session->userdata('username');
        if(!empty($sess_id)){
            //do nothing
            //silence is golden
        }
		else
		{
			//error
			$this->session->set_flashdata('error', 'Your Session has expired. Please login again');
            redirect(base_url('auth'));
        }
    }
    public function authenticate($uname,$email,$uid){
        $userData = array(
                'uid' =>$uid,
                'username' =>$uname,
                'loggedIn' =>'1',
                'email' =>$email
        );
        $this->session->set_userdata($userData);
        //get username and password from the db
        //if true authenticate and add the data to the session
        //if false redirect and set flash message error
    }
  
    public function newUserd(){
        // $this->authenticate();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('error', 'Email Address Already Exists! Use Another');
            redirect('auth/register');
        }
        else
        {
            if($this->input->post('password') == $this->input->post('password_conf') )  {
                $data = array(
                    'fname' => $this->input->post('fname'),
                    'lname' => $this->input->post('lname'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'emp_id'=> $this->input->post('emp_id'),
                    'emp_id_no'=> $this->input->post('emp_id'),
                    'work_station'=> $this->input->post('work_station'),
                    'password' => md5($this->input->post('password')),
                    'uid'=>1,
                    'status'=>1,
                    'tnc'=>$this->input->post('tnc'),
                    'date_last_updated'=>date("Y-m-d h:i:s"),
                    'date_c'=>date("Y-m-d h:i:s")
                    );
                
                if($this->db->insert('users', $data))
                {
                    $uid = $this->db->insert_id();
                    $this->authenticate($this->input->post('fname'),$this->input->post('email'),$uid);
                }
                return true;
            }
            else
            {
                return false;
            }
        }
         
    }
    public function getUser(){
        $email = $this->input->post('uname');
        $password = $this->input->post('password');
        $query = $this->db->query('SELECT * FROM users WHERE email = "'.$email.'" AND password="'.md5($password).'"');
        if($query->num_rows() == 1){
             
            $row = $query->row();
            $uid = $row->id;
            $uname = $row->fname. ' '. $row->lname;
            $email = $row->email;
            $this->authenticate($uname,$email,$uid);
            return 1;
        }
        else
        {
            return false;
        }
    }

    public function allUserD($id){
        $query = $this->db->query('SELECT * FROM users WHERE id = '.$id);
        return $query->result_array();
    }

    public function getAllUsers(){
        $query = $this->db->query('SELECT * FROM users');
        return $query->result_array();
    }
    public function updatePass($uid,$pass){
        $data = array(
            'password'=>md5($pass)
        );
        $this->db->where('id', $uid);
        return $this->db->update('users', $data);
    }

    public function uploadPImg($file,$uid){
        //first check if they have an active profile inage
        if($this->checkPimg($uid))
        {
            //if they do then update it to 0 then insert new record
            $data = array(
                'active' =>0
            );
            $id = $this->checkPimg($uid);
            $this->db->where('id', $id);
            $this->db->update('gallery', $data);
            $this->savePimgDb($file,$uid);
        }
        else
        {
            $this->savePimgDb($file,$uid);
        }
        return true;    
    }
    public function savePimgDb($file,$uid){
        $data = array(
            'file'=>$file,
            'uid'=>$uid,
            'active'=>1,
            'date_c'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('gallery', $data);
    }
    public function getPimg($uid){
        $query = $this->db->query('SELECT * FROM gallery WHERE uid = "'.$uid.'" AND active=1');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $pic = $row->file;
        }
        else
        {
            return 'default_pimg_for_ajiira.png';
        }
       
    }

    public function checkPimg($uid){
        $query = $this->db->query('SELECT * FROM gallery WHERE uid = "'.$uid.'" AND active=1');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $pic = $row->id;
        }
        else
        {
            return false;
        }
    }
    
    public function getMobileMoney($uid)
    {
        $query = $this->db->query('SELECT * FROM payment_option_mobile_money WHERE uid = '.$uid);
        return $query->result_array();
    }

    public function getBankingInfo($uid)
    {
        $query = $this->db->query('SELECT * FROM payment_option_bank_details WHERE uid = '.$uid);
        return $query->result_array();
    }
    public function getWorkExp($uid){
        $query = $this->db->query('SELECT * FROM work_experience WHERE uid = '.$uid);
        return $query->result_array();   
    }
}