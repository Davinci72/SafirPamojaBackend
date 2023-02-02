<?php 
Class Payments extends CI_Controller{
    var $uid;
    public function __construct(){
        parent::__construct();
        $this->load->model('Cpanel_model');
        $this->load->model('Polls_model');
        // Load form validation library
        $this->load->library('form_validation');
        //load pagination library
        $this->load->library("pagination");
        // Load file helper
        $this->load->helper('file');
        $user_data = $this->session->all_userdata();
        $this->uid = $user_data['uid'];
    }
    public function paginationConf($stub,$count){
        $config = array();
        $config['full_tag_open'] = "<div class='btn-group mb-1' style='text-color:white'>";
        $config['full_tag_close'] = '</div>';
        $config['num_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['num_tag_close'] = '</button>';
        $config['cur_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['cur_tag_close'] = '</button>';
        $config['prev_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['prev_tag_close'] = '</button>';

        $config['first_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['first_tag_close'] = '</button>';
        $config['last_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['last_tag_close'] = '</button>';

        $config['next_link'] = '<i class="fas fa-angle-double-right"></i>';
        $config['next_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['next_tag_close'] = '</button>';

        $config['prev_link'] = '<i class="fas fa-angle-double-left"></i>';
        $config['prev_tag_open'] = '<button type="button" class="btn btn-secondary waves-effect">';
        $config['prev_tag_close'] = '</button>';


        $config["base_url"] = base_url() . $stub;
        $config["total_rows"] = $count;
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        return $config;
    }
    public function template($page,$data=array()){
        $uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/payments/'.$page,$data);
        $this->load->view('templates/footer');
    }
    public function getParentDetails($id){
        return $parent = $this->Cpanel_model->getPeros($id);
	}
	public function getChildren($parentID,$userSpaceId){
        return $children = $this->Cpanel_model->getChildrenByID($parentID,$userSpaceId);
	}
	public function getChildDetails($childID){
        // echo '<pre>';
        return $child = $this->Cpanel_model->getChildDetails($childID);
	}
	public function get_user_space($uid){
        return $this->Cpanel_model->getu_user_space($uid);
    }
    public function newPayout($menuID){
        $data['title'] = "Create New Payment Task";
        $data['menuID'] = $menuID;
        $this->template('newTask',$data);
    }
    public function settle($menuID){
        $data['title'] = "Settle Payments";
        $data['menuID'] = $menuID;
        $this->template('settle',$data);
    }
    public function Deposits($menuID){
        $data['title'] = "Deposit Funds";
        $data['menuID'] = $menuID;
        $this->template('deposit',$data);
    }
    public function Reversal($menuID){
        $data['title'] = "Make A Reversal";
        $data['menuID'] = $menuID;
        $this->template('reversal',$data);
    }
    public function Transactions($menuID){
        $data['title'] = "View Transactions";
        $data['menuID'] = $menuID;
        $this->template('transactions',$data);
    }

}