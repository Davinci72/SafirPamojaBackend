<?php 
class Cpanel extends CI_Controller{
    var $uid;
    public function __construct(){
        parent::__construct();
        $this->authenticator();
        $this->load->model('Cpanel_model');
        $this->load->model('Sales_model');
        $this->load->model('Clients_model');
        $this->load->model('Mpesa_model');
        $this->load->library("pagination");
        $user_data = $this->session->all_userdata();
        $this->uid = $user_data['uid'];
    }
    public function index(){
        $this->page('dashboard');
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
    public function authenticator(){
        $sess_id = $this->session->userdata('username');
        if(!empty($sess_id)){
            //do nothing
            //silence is golden
        }
        else
        {
            $this->session->set_flashdata('error', 'Your Session has expired. Please login again');
            redirect(base_url('Auth'));
        }
    }
    public function newApplication(){
        $this->pagination->initialize($this->paginationConf("Cpanel/newApplication",$this->Cpanel_model->get_count_apps($this->uid)));
        $data['user_spaces'] = $this->Cpanel_model->getUserSpaces($this->uid);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['applications'] = $this->Cpanel_model->getApplications($this->uid,10,$page);
        $data['title'] = 'Create New Application';
        $this->page('newapp',$data);
    }
    public function updateAppName(){
        if($this->Cpanel_model->updateAppName($this->uid)){
            //success
            $this->session->set_flashdata('success', 'App Name Updated Successfully');
            redirect(base_url('Cpanel/newApplication'));
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Application Is Not Updated Please Try Again Later');
            redirect(base_url('Cpanel/newApplication'));
        }
    }
    public function deleteAppName(){
        if($this->Cpanel_model->deleteAppName($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Application Name  Deleted Successfully');
            redirect(base_url('Cpanel/newApplication'));
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Application NameNot Deleted Please Try Again Later');
            redirect(base_url('Cpanel/newApplication'));
        }
    }
    public function page($page,$data=array()){
        //get user space
        $uid = $this->session->userdata['uid'];
        $userSpace = $this->get_user_space($uid);
        $data['parent_menu'] = $this->Cpanel_model->getParentMenuItemsUsa($userSpace);
        $data['cpanel'] = $this;
        $data['userSpace'] = $userSpace;

        $this->load->view('templates/cpanel/header');
        $this->load->view('templates/cpanel/nav',$data);
        $this->load->view('pages/cpanel/'.$page,$data);
        $this->load->view('templates/cpanel/footer');
    }
    public function get_user_space($uid){
        return $this->Cpanel_model->getu_user_space($uid);
    }
    public function getChildDetails($childID){
        // echo '<pre>';
        return $child = $this->Cpanel_model->getChildDetails($childID);
    }
    public function getChildren($parentID,$userSpaceId){
        return $children = $this->Cpanel_model->getChildrenByID($parentID,$userSpaceId);
    }
    public function checkCanEdit($userSpaceId){

    }
    public function checkCanDelete($userSpaceId){

    }
    public function getParentDetails($id){
        return $parent = $this->Cpanel_model->getPeros($id);
    }
    public function addUser(){
        $this->pagination->initialize($this->paginationConf("back-office/add-user",$this->Cpanel_model->get_count_users($this->uid)));
        $data['user_spaces'] = $this->Cpanel_model->getUserSpaces($this->uid);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['users'] = $this->Cpanel_model->getUsers($this->uid,100,$page);
        $this->page('newuser',$data);
    }
    public function updateUser(){
        if($this->Cpanel_model->updateUser($this->uid)){
            //success
            $this->session->set_flashdata('success', 'User '.$this->input->post('email').'  Updated Successfully');
            redirect(base_url('back-office/add-user'));
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'User '.$this->input->post('email').' Not Updated Please Try Again Later');
            redirect(base_url('back-office/add-user'));
        }
    }
    public function activateUser(){

    }
    public function deactivateUser(){
        
    }

    public function getUserspace($id){
        return $this->Cpanel_model->getUserSpace($id);
    }
    public function addUserSpace($appid,$page=''){
        $this->pagination->initialize($this->paginationConf("back-office/add-user-space",$this->Cpanel_model->get_count_user_spaces($this->uid)));
        $data['user_spaces'] = $this->Cpanel_model->getUserSpaces($this->uid);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data['appid']=$appid;
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['user_spaces'] = $this->Cpanel_model->getUserSpacesF($this->uid,10,$page,$appid);
        $this->page('userspaces',$data);
    }
    public function addNewUserSpace($appid){
        if($this->Cpanel_model->addNewUserSpace($this->uid,$appid)){
            //success
            $this->session->set_flashdata('success', 'User Space Added Successfully');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'User Space Not Added');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
    }
    public function addNewApplicationName(){
        if($this->Cpanel_model->addNewApplication($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Application Created Successfully');
            redirect(base_url('Cpanel/newApplication'));
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Application Was Not Created, Check Logs');
            redirect(base_url('Cpanel/newApplication'));
        }
    }
    public function getStatus($n){
        switch($n){
            case 1:
                return '<span class="badge badge-success">Active</span>';
            break;
            case 2:
                return '<span class="badge badge-danger">Inactive</span>';
            break;
            default:
                return 'No Defined Status';
            break;

        }
    }
    public function deleteUserSpace($appid){
        if($this->Cpanel_model->deleteUserSpae($this->uid)){
            //success
            $this->session->set_flashdata('success', 'User Space  Deleted Successfully');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'User Space Not Deleted Please Try Again Later');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
    }
    public function updateUserSpace($appid){
        if($this->Cpanel_model->updateUserSpae($this->uid,$appid)){
            //success
            $this->session->set_flashdata('success', 'User Space  Updated Successfully');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'User Space Not Updated Please Try Again Later');
            redirect(base_url('Cpanel/addUserSpace/').$appid);
        }
    }
    public function userSpaceAccess($appid,$userSpaceID=''){
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['userSpaceId'] = $userSpaceID;
        $data['uid'] = $this->uid;
        $data['appid'] = $appid;
        $data['parent_menu_items'] = $this->Cpanel_model->getAllMenuItems($this->uid,$appid);
        $this->page('userspaceaccess',$data);
    }
    public function userSpaceAccessAjax(){
        $this->Cpanel_model->userSpaceAccessAjax($this->uid);
    }
    public function userSpaceAccessParentAjax(){
        $this->Cpanel_model->userSpaceAccessAjaxParent($this->uid);
    }

    public function userSpaceAccessAjaxCanEdit(){
        $this->Cpanel_model->userSpaceAccessAjaxCanEdit($this->uid);
    }

    public function userSpaceAccessAjaxCanDelete(){
        $this->Cpanel_model->userSpaceAccessAjaxCanDelete($this->uid);
    }
    public function returnChecked($status,$childID,$userSpace){
        switch($status){
            case  1:
                return 'checked';
            case 0 :
                return '';
                default:
                return '';
                break ;
        }
    }
    public function returnCheckedParent($parentId,$userSpaceId){
        $status = $this->Cpanel_model->getParentAccessStatus($parentId,$userSpaceId);
        return $this->returnChecked($status,$parentId,$userSpaceId);

    }
    public function userSpaceMatching($page=''){
        $this->pagination->initialize($this->paginationConf("back-office/user-space-matching",$this->Cpanel_model->get_count_user_spaces($this->uid)));
        $data['user_spaces'] = $this->Cpanel_model->getUserSpaces($this->uid);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['user_spaces'] = $this->Cpanel_model->getUserSpacesF($this->uid,10,$page);
        $this->page('userspacematching',$data);
    }
    public function newMenuItem($appid,$page=""){
        $this->pagination->initialize($this->paginationConf("back-office/menu-items",$this->Cpanel_model->getCountMenuItemsByApp($this->uid,'menuitems',$appid)));
        // $data['user_spaces'] = $this->Cpanel_model->getUserSpaces($this->uid);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["links"] = $this->pagination->create_links();
        $data['cpanelobject'] = $this;
        $data['appid']=$appid;
        $data['parent_menu_items'] = $this->Cpanel_model->getParentMenuItems($this->uid,10,$page,$appid);
        // $data['children_menu_items'] = $this->Cpanel_model->getChildrenMenuItems($this->uid,10,$page);
        $this->page('newmenuitem',$data);
    }
    public function addNewMenuItem($appid){
        if($this->Cpanel_model->addNewMenuItem($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Menu Item Added Successfully');
            redirect(base_url('back-office/menu-items/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Menu Item Not Added, Please Try Again Later');
            redirect(base_url('back-office/menu-items/').$appid);
        }
    }
    public function getMenuCount($uid,$parentID){
        return $this->Cpanel_model->checkForChildren($uid,$parentID);
    }
    public function addNewChildMenuItem($appid){
        if($this->Cpanel_model->addNewChildMenuItem($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Menu Child Item Added Successfully');
            redirect(base_url('back-office/menu-items/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Menu Child Item Not Added, Please Try Again Later');
            redirect(base_url('back-office/menu-items/').$appid);
        }
    }
    public function updateChildItem($appid){
        if($this->Cpanel_model->updateChildItem($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Child Menu Item Updated Successfully');
            redirect(base_url('back-office/menu-items/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Child Menu Item Not Updated Please Try Again Later');
            redirect(base_url('back-office/menu-items/').$appid);
        }
    }
    public function updateMainMenu($appid){
        if($this->Cpanel_model->updateParentItem($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Parent Menu Item Updated Successfully');
            redirect(base_url('back-office/menu-items/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Parent Menu Item Not Updated Please Try Again Later');
            redirect(base_url('back-office/menu-items/').$appid);
        }
    }
    public function deleteChildItem($appid){
        if($this->Cpanel_model->deleteChildItem($this->uid)){
            //success
            $this->session->set_flashdata('success', 'Child Menu Item Deleted Successfully');
            redirect(base_url('back-office/menu-items/').$appid);
        }
        else
        {
            //error
            $this->session->set_flashdata('error', 'Child Menu Item Not Deleted Please Try Again Later');
            redirect(base_url('back-office/menu-items/').$appid);
        }
    }
}
?>