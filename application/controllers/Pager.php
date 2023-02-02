<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pager extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('Cpanel_model');
		$this->load->library("pagination");
		$this->load->model('Authentication');
        $this->authenticator();
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
        $config["uri_segment"] = 3;
        return $config;
    }
	public function index()
	{
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/analytics');
        $this->load->view('templates/footer');
	}
	public function userProfile(){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/my-account');
        $this->load->view('templates/footer');
	}
	public function updatePassword(){
		echo $pass = $this->input->post('password');
		echo $confpass = $this->input->post('conf_password');
		$uid = $this->session->userdata['uid'];
		if($pass == $confpass){
			if($this->Authentication->updatePass($uid,$pass)){
			//error
			$this->session->set_flashdata('success', 'Your Password Has Been Changed');
			redirect(base_url('my-account'));
			}else
			{
				//success
			$this->session->set_flashdata('error', 'An Error Occured !!');
			redirect(base_url('my-account'));

			}
		}
		else
		{
			//success
			$this->session->set_flashdata('error', 'Passwords Do Not Match !!');
			redirect(base_url('my-account'));
		}
	}
	public function detailedReport(){
		return $r = $this->Cpanel_model->detailedReport();
		// echo $r[0]->count()
	}
	public function userDet($uid){
		return $this->Cpanel_model->getUserDet($uid);;
	}
	public function detailedR($menuID){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		//$data['user_data'] = 
		// var_dump($data['parent_menu']);exit();
		$data['r'] = $this->Cpanel_model->detailedReport($userSpace,$uid);
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/detailed-user-report',$data);
        $this->load->view('templates/footer');
	}
	public function bizViewReportsPerWard($menuID){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		//$data['user_data'] = 
		// var_dump($data['parent_menu']);exit();
		$data['r'] = $this->Cpanel_model->detailedBizReportByWard();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		$data['menuID'] = $menuID;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/detailed-biz-reports-per-ward',$data);
        $this->load->view('templates/footer');
	}
	public function bizViewReportsPerConstituency($uid){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		//$data['user_data'] = 
		// var_dump($data['parent_menu']);exit();
		$data['r'] = $this->Cpanel_model->detailedBizReportByConstituency();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/detailed-biz-reports-per-constituency',$data);
        $this->load->view('templates/footer');
	}
	public function readSubCounty($id){
		return $this->Cpanel_model->readSubCounty($id);
	}
	public function groupByWard($menuID,$ward){
		$uid = $this->session->userdata['uid'];
		// $this->pagination->initialize($this->paginationConf("Pager/viewReports/".$menuID,$this->Cpanel_model->get_count_biz($uid)));
        // $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        // $data["links"] = $this->pagination->create_links();
		// $this->Cpanel_model->updaTor();exit();

		
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		$data['biz'] = $this->Cpanel_model->getBizbyWard($ward);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		$data['menuID']=$menuID;
		
        $this->load->view('templates/header_tables');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/payments/transactions',$data);
        $this->load->view('templates/footer_tables');
	}
	public function addNewProperty(){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/newProperty');
        $this->load->view('templates/footer');
	}
	public function createSubCounty(){
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/new-sub-county');
        $this->load->view('templates/footer');
	}
	public function saveSubcounty(){
		$data = array(
			'Subcounty'=>$this->input->post('sub_county')
		);
		if($this->Cpanel_model->saveSubC($data)){
			$msg = 'Thank you '.$this->input->post("names").'. The details of your business '.$this->input->post("businessname").' have been updated successfully. The County government of Kajiado looks forward to serving you better.';
			$this-> messenger($msg,$this->input->post("phone"));
            //do nothing
            //silence is golden
			//error
			$this->session->set_flashdata('success', 'Subcounty Added Successfuly');
            redirect(base_url('Pager/createSubCounty'));
        }
		else
		{
			//error
			$this->session->set_flashdata('error', 'Subcounty Not Added!!');
            redirect(base_url('Pager/createSubCounty'));
        }
	}
	public function createWard(){
		$data['sub_county'] = $this->Cpanel_model->getSubcouty();
		$uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
		$data['saved_biz'] = $this->Cpanel_model->getCount($uid,'biz');
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/new-ward');
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

	public function saveBiashara(){
		//trigger message method here
		// $this->load->helper(array('form', 'url'));
		// $this->form_validation->set_rules('sbp_amount', 'Single Business Permit', 'required|regex_match[/^[0-9]{10}$/]'); //{10} for 10 digits number
		// if ($this->form_validation->run() == FALSE) {
		// 	redirect(base_url());
		// }
		// else
		// {
		// 	return true;
		// }
		// exit();
		$totalFeePayable = $this->input->post('sbp_amount') + $this->input->post('adv_amount') + $this->input->post('ph_amount') + $this->input->post('lr_amount') + $this->input->post('fire_inspection_fees') + $this->input->post('reserved_parking_fees') + $this->input->post('dpmvb_feef') + $this->input->post('monthly_parking_fees'); 
		$data = array(
			'names'=>$this->input->post("names"),
			'business_name'=>$this->input->post("businessname"),
			'id_no'=>$this->input->post("id_no"),
			'phone'=>$this->input->post("phone"),
			'email'=>$this->input->post("email"),
			'type_of_business'=>$this->input->post("type_of_business"),
			'locale'=>$this->input->post("locale"),
			'ward'=>$this->input->post("ward"),
			'size_of_biz'=>$this->input->post("size"),
			'no_employees'=>$this->input->post('no_employees'),//unsorted
			'sbp_amount'=>$this->input->post('sbp_amount'),//unsorted
			'adv_amount'=>$this->input->post('adv_amount'),//unsorted
			'ph_amount'=>$this->input->post('ph_amount'),//unsorted
			'lr_amount'=>$this->input->post('lr_amount'),//unsorted
			'license_fees'=>$totalFeePayable,
			'plwd'=>$this->input->post("plwd"),
			'reg_no'=>$this->input->post("uniqueID"),
			'kra'=>$this->input->post("kra"),
			'fire_inspection_fees'=>$this->input->post('fire_inspection_fees'),
			'reserved_parking_fees'=>$this->input->post('reserved_parking_fees'),
			'monthly_parking_fees'=>$this->input->post('monthly_parking_fees'),
			'dpmvb_feef'=>$this->input->post('dpmvb_feef'),
			//get the subcounty id
			'subcounty'=>$this->Cpanel_model->getSubcountyId($this->input->post("ward")),
			'uid'=>$this->session->userdata('uid'),
			'date_c'=>date("Y-m-d h:i:s")
		); 

		if($this->Cpanel_model->saveBiz($data)){
			$msg = 'Thank you '.$this->input->post("names").'. The details of your business '.$this->input->post("businessname").' have been updated successfully. The County government of Kajiado looks forward to serving you better.';
			$this-> messenger($msg,$this->input->post("phone"));
            //do nothing
            //silence is golden
			//error
			$this->session->set_flashdata('success', 'Bussiness Added Successfuly');
            redirect(base_url());
        }
		else
		{
			//error
			$this->session->set_flashdata('error', 'Bussiness Not Added!!');
            redirect(base_url());
        }

	}
	public function saveWard(){
		$data = array(
			'subcounty'=>$this->input->post('sub_county'),
			'constituency'=>$this->input->post('ward')
		);
		if($this->Cpanel_model->saveWard($data)){
			//success
			$this->session->set_flashdata('success', 'Ward Added Successfuly');
            redirect(base_url('Pager/createWard'));
        }
		else
		{
			//error
			$this->session->set_flashdata('error', 'Ward Not Added!!');
            redirect(base_url('Pager/createWard'));
        }
	}
	public function messenger($msg,$phone){
		$api_key = "siHbzy3afn4rIDUEtqmCu6vJ7hd1YjgMTFQBAL0VpWkGxPceOX2oN5wl8ZSR9K";
		$shortcode = "CG_KAJIADO";
		$billing_phone=$phone;
		$textmessage=$msg;
		$serviceId = '0';
		$smsdata = array(
			'api_key' => $api_key,
			'shortcode' =>$shortcode,
			'mobile' => $billing_phone,
			'message' => $textmessage,
			'service_id' => $serviceId,
			'response_type' => "json",
			);
	
		$data = json_encode($smsdata);
	   
		$ch = curl_init('https://api.tililtech.com/sms/v3/sendsms');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		// $arr = array(
		// 		"api_key"=>"siHbzy3afn4rIDUEtqmCu6vJ7hd1YjgMTFQBAL0VpWkGxPceOX2oN5wl8ZSR9K",
		// 		"service_id"=>0,
		// 		"mobile"=>$phone,
		// 		"response_type"=>"json",
		// 		"shortcode"=>"CG_KAJIADO",
		// 		"message"=>$msg,
		// 		"date_send"=>date("Y-m-d h:i:s")
		// );
		// echo $raw_json = json_encode($arr);
		// $curl = curl_init();
		// curl_setopt_array($curl, array(
		// CURLOPT_URL => 'http://sms.crowdcomm.co.ke/sms/v3/sendsms',
		// CURLOPT_RETURNTRANSFER => true,
		// CURLOPT_ENCODING => '',
		// CURLOPT_MAXREDIRS => 10,
		// CURLOPT_TIMEOUT => 0,
		// CURLOPT_FOLLOWLOCATION => true,
		// CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		// CURLOPT_CUSTOMREQUEST => 'POST',
		// CURLOPT_POSTFIELDS =>$raw_json,
		// CURLOPT_HTTPHEADER => array(
		// 	'Content-Type: application/json'
		// ),
		// ));
		// $response = curl_exec($curl);
		// curl_close($curl);
		// echo $response;
	}
	public function saveLandRegistry(){
		if($this->input->post("land_use") == "Other"){
			$landUse = $this->input->post("land_use_2");
		}
		else {
			$landUse = $this->input->post("land_use");
		}
		$tot = $this->input->post('lr_amount') + $this->input->post('land_r_amount');
		$data = array(
			'names'=>$this->input->post("names"),
			'id_no'=>$this->input->post("id_no"),
			'phone'=>$this->input->post("phone"),
			'ward'=>$this->input->post("ward"),
			'lr_amount'=>$this->input->post('lr_amount'),//unsorted
			'land_r_amount'=>$this->input->post('land_r_amount'),
			'total_land_fee_amount'=>$tot,
			'type_of_ownership'=>$this->input->post('type_of_ownership'),
			'al_no'=>$this->input->post("al_no"),
			'locale'=>$this->input->post("locale_desc"),
			'acre'=>$this->input->post("acre"),
			'lr_no'=>$this->input->post('lr_pre').'/'.$this->input->post("lr_no"),
			'uid'=>$this->session->userdata('uid'),
			'land_use'=>$this->input->post("land_use"),
			'date_c'=>date("Y-m-d h:i:s")
		); 

		if($this->Cpanel_model->saveLand($data)){
			$msg = 'Thank you '.$this->input->post("names").'. The details of your land '.$this->input->post('lr_pre').'/'.$this->input->post("lr_no").' have been updated successfully. The County government of Kajiado looks forward to serving you better.';
			$this-> messenger($msg,$this->input->post("phone"));
            //do nothing
            //silence is golden
			//error
			$this->session->set_flashdata('success', 'Land Registry Added Successfuly');
            redirect(base_url('Pager/addNewProperty/'));
        }
		else
		{
			//error
			$this->session->set_flashdata('error', 'Land Registry Not Added!!');
            redirect(base_url('Pager/addNewProperty/'));
        }
	}
	
	public function viewReports($menuID){
		$uid = $this->session->userdata['uid'];
		// $this->pagination->initialize($this->paginationConf("Pager/viewReports/".$menuID,$this->Cpanel_model->get_count_biz($uid)));
        // $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        // $data["links"] = $this->pagination->create_links();
		// $this->Cpanel_model->updaTor();exit();

		
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		$data['biz'] = $this->Cpanel_model->getBiz($userSpace,$uid);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		$data['menuID']=$menuID;
		
        $this->load->view('templates/header_tables');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/payments/transactions',$data);
        $this->load->view('templates/footer_tables');
	}
	public function viewBiz($menuID,$bizId){
		$uid = $this->session->userdata['uid'];
		// $this->pagination->initialize($this->paginationConf("Pager/viewReports/".$menuID,$this->Cpanel_model->get_count_biz($uid)));
        // $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        // $data["links"] = $this->pagination->create_links();


		
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		$data['biz'] = $this->Cpanel_model->getBizOne($bizId);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		$data['menuID']=$menuID;
		$this->load->view('templates/header');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/view-biz',$data);
        $this->load->view('templates/footer');
	}
	public function getSubCounty($ward){
		return $this->Cpanel_model->getSubcoutyName($ward);
	}
	public function getUserName($id){
		return $this->Authentication->allUserD($id)[0]['fname'].' '.$this->Authentication->allUserD($id)[0]['lname'];
	}
	public function ViewLandReports(){
		$uid = $this->session->userdata['uid'];
		// $this->pagination->initialize($this->paginationConf("Pager/viewReports/".$menuID,$this->Cpanel_model->get_count_biz($uid)));
        // $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        // $data["links"] = $this->pagination->create_links();


		
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		$data['lands'] = $this->Cpanel_model->getLands($userSpace,$uid);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header_tables');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/payments/lands',$data);
        $this->load->view('templates/footer_tables');
	}
	public function viewLandDetails($id){
		$uid = $this->session->userdata['uid'];
		// $this->pagination->initialize($this->paginationConf("Pager/viewReports/".$menuID,$this->Cpanel_model->get_count_biz($uid)));
        // $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        // $data["links"] = $this->pagination->create_links();


		
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
		$data['biz'] = $this->Cpanel_model->getLand($id);
		// var_dump($data['parent_menu']);exit();
        $data['cpanel'] = $this;
		$data['userSpace'] = $userSpace;
		
        $this->load->view('templates/header_tables');
		$this->load->view('templates/side-nav',$data);
		$this->load->view('pages/payments/land-details',$data);
        $this->load->view('templates/footer_tables');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */