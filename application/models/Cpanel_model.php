<?php 
class Cpanel_model extends CI_Model {
    public function getUsers($uid,$limit,$start){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('users', array('uid'=>$uid,'status'=>1));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getUserDet($uid){
        $query = $this->db->get_where('users', array('id'=>$uid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function get_count_users($uid){
        $this->db->where('uid',$uid);
        $this->db->from("users");
        return $num = $this->db->count_all_results();
    }
    public function getUserSpaces($uid){
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('user_spaces', array('uid'=>$uid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getBizOne($bizId){
        $query = $this->db->get_where('biz', array('id'=>$bizId));
        return $query->result();
    }
    public function getSales($uid,$limit,$start){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('sales', array('uid'=>$uid,'status'=>0));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function updateUser($uid){
        $data = array(
            'fname' => $this->input->post('fname'),
            'lname' => $this->input->post('lname'),
            'email' => $this->input->post('email'),
            'user_space' => $this->input->post('user_space'),
            'date_last_updated' => date("Y-m-d h:i:s")
        );
        $this->db->where('id',$this->input->post('user'));
        $this->db->where('uid',$uid);
        return $this->db->update('users',$data);
    }
    public function getUserSpace($id){
        $query = $this->db->query('SELECT * FROM user_spaces WHERE id = "'.$id.'"');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = '<span class="badge badge-success">'.$row->user_space.'</span>';
        }
        else
        {
            return '<span class="badge badge-danger">Update User Space:</span>';
        }
    }
    public function detailedReport($userSpace,$uid){

        $this->db->order_by("id", "desc");
        if($userSpace != "3")
        {
            if($userSpace == "4")
            {
                //get the uid
                //get the subcounty
                $subcounty = $this->getUserDet($uid)[0]->work_station;
                //get subcounty id
                $subID = $this->getSubIdBySubName($subcounty)[0]->id;
                //get the biz based on that subcounty
                $query = $this->db->query('SELECT count(business_name) as num, uid FROM `biz` where subcounty='.$subID.' GROUP BY uid order by num desc');

            }
            else
            {
                $query = $this->db->query('SELECT count(business_name) as num, uid FROM `biz` GROUP BY uid order by num desc');
            }
        }
        
        else
        {
            $query = $this->db->query('SELECT count(business_name) as num, uid FROM `biz` GROUP BY uid order by num desc');
        }
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }

    public function detailedBizReportByWard(){
        $query = $this->db->query('SELECT count(business_name) as num, subcounty,ward FROM `biz` GROUP BY ward order by num desc');
        return $query->result();
        
    }
    public function detailedBizReportByConstituency(){
        $query = $this->db->query('SELECT count(business_name) as num, subcounty FROM `biz` GROUP BY subcounty order by num desc');
        return $query->result();
    }

    public function getu_user_space($uid){
        $query = $this->db->query('SELECT * FROM users WHERE id = "'.$uid.'"');
        $row = $query->row();
        return $row->user_space;
    }
    public function checkIfCanEdit($user_space,$menu_id){
        $query = $this->db->query('SELECT * FROM user_space_access WHERE user_space="'.$user_space.'" AND menu_item="'.$menu_id.'"');
        $row = $query->row();
        return $row->can_edit;
    }
   
    public function checkIfCanDelete($user_space,$menu_id){
        $query = $this->db->query('SELECT * FROM user_space_access WHERE user_space="'.$user_space.'" AND menu_item="'.$menu_id.'"');
        $row = $query->row();
        return $row->can_delete;
    }
    public function getUserSpacesF($uid,$limit,$start,$appid){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('user_spaces', array('uid' => $uid,'app_id'=>$appid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function get_count_apps($uid){
        $this->db->where('uid',$uid);
        $this->db->from("apps");
        return $num = $this->db->count_all_results();
    }
    public function getApplications($uid,$limit,$start){
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get_where('apps', array('uid' => $uid));
        return $query->result();
    }
    public function addNewApplication($uid){
        $data = array(
            'uid'=>$uid,
            'app_name'=>$this->input->post('app_name'),
            'status'=>1,
            'date_c'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('apps', $data);
    }
    public function updateAppName($uid){
        $data = array(
            'app_name' => $this->input->post('app_name')
        );
        $this->db->where('id',$this->input->post('appid'));
        $this->db->where('uid',$uid);
        return $this->db->update('apps',$data);
    }
    public function deleteAppName($uid){
        $this->db->where('id',$this->input->post('appid'));
        $this->db->where('uid',$uid);
        return $this->db->delete('apps');
    }
    public function get_count_user_spaces($uid){
        $this->db->where('uid',$uid);
        $this->db->from("user_spaces");
        return $num = $this->db->count_all_results();
    }
    public function addNewUserSpace($uid,$appid){
        $data = array(
            'user_space'=>$this->input->post('user_space'),
            'uid'=>$uid,
            'app_id'=>$appid,
            'status'=>$this->input->post('status'),
            'date_created'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('user_spaces', $data);
    }
    public function saveBiz($data){
        return $this->db->insert('biz', $data);
    }
    public function saveWard($data){
        return $this->db->insert('constituencies', $data);
    }
    public function saveSubC($data){
        return $this->db->insert('sub_county', $data);
    }
    public function saveLand($data){
        return $this->db->insert('land_registry', $data);
    }
    public function updateUserSpae($uid,$appid){
        $data = array(
            'user_space' => $this->input->post('user_space'),
            'status' => $this->input->post('status'),
        );
        $this->db->where('id',$this->input->post('usid'));
        $this->db->where('uid',$uid);
        return $this->db->update('user_spaces',$data);
    }
    public function deleteUserSpae($uid){
        $this->db->where('id',$this->input->post('usid'));
        $this->db->where('uid',$uid);
        return $this->db->delete('user_spaces');
    }
    public function getCount($uid,$table){
        $this->db->where('uid',$uid);
        $this->db->from($table);
        return $num = $this->db->count_all_results();
    }
    public function getCountMenuItemsByApp($uid,$table,$appid){
        $this->db->where('uid',$uid);
        $this->db->where('appid',$appid);
        $this->db->from($table);
        return $num = $this->db->count_all_results();
    }
    public function checkForChildren($uid,$parentID){
        $this->db->where('parent_id',$parentID);
        $this->db->where('uid',$uid);
        $this->db->from('menuitemschildren');
        return $num = $this->db->count_all_results();
    }
    public function getParentMenuItems($uid,$limit,$start,$appid){
        // echo $start;exit();
        $this->db->limit($limit, $start);
        $this->db->order_by("id", "asc"); 
        $query = $this->db->get_where('menuitems', array('uid' => $uid,'appid'=>$appid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getSubcoutyName($wardName){
        //go to constituencies and get the subcounty
        $query = $this->db->get_where('constituencies', array('constituency' => $wardName));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        $res = $query->result();
        return $this->readSubCounty($res[0]->subcounty)[0]->Subcounty;
    }
    public function getSubIdBySubName($subName){
         //go to constituencies and get the subcounty
         $query = $this->db->get_where('sub_county', array('Subcounty' => $subName));
         // echo '<pre>';
         // var_dump($this->db);exit('at database');
         return $res = $query->result();
    }
    public function getSubcountyId($wardName){
        //go to constituencies and get the subcounty
        $query = $this->db->get_where('constituencies', array('constituency' => $wardName));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        $res = $query->result();
        if(count($res) == 0 ){

        }else{
            return $res[0]->subcounty; 
        }
    }
    public function readSubCounty($id){
        $query = $this->db->get_where('sub_county', array('id' => $id));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $res = $query->result();
    }
    public function updaTor(){
        //get all biz ward name
        $query = $this->db->get('biz');
        $res = $query->result();
        foreach($res as $g){
            //read cosntituencies
            $subcountyID = $this->getSubcountyId($g->ward);
            //update
            $this->updateBiz($subcountyID,$g->id);
        }
        //return subcounty id
        //update biz table subcounty column
    }
    public function updateBiz($subcounty,$id){
        $data = array(
            'subcounty' => $subcounty
        );
        $this->db->where('id',$id);
        return $this->db->update('biz',$data);
    }
    public function getAllMenuItems($uid,$appid){
        $this->db->order_by("id", "asc"); 
        $query = $this->db->get_where('menuitems', array('uid' => $uid,'appid'=>$appid));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getChildrenMenuItems($uid,$parentID){
        $this->db->order_by("id", "asc"); 
        $query = $this->db->get_where('menuitemschildren', array('uid' => $uid,'parent_id'=> $parentID));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getChildrenByID($parentID,$userSpaceId){
        $this->db->order_by("id", "asc"); 
        $query = $this->db->get_where('user_space_access', array('parent_id'=> $parentID,'can_access'=>1,'user_space'=>$userSpaceId,'is_parent'=>2));
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getPeros($id){
        $query = $this->db->get_where('menuitems', array('id'=> $id));
        return $query->result();
    }
    public function addNewMenuItem($uid){
        $data = array(
            'menuitem'=>$this->input->post('menu_name'),
            'menu_icon'=>$this->input->post('menu_icon'),
            'appid'=>$this->input->post('appid'),
            'uid'=>$uid,
            'date_c'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('menuitems', $data);
    }

    public function addNewChildMenuItem($uid){
        $data = array(
            'uid'=>$uid,
            'parent_id'=>$this->input->post('parent_id'),
            'menu_item'=>$this->input->post('child_menu_name'),
            'menu_icon'=>$this->input->post('child_menu_icon'),
            'slug'=>$this->input->post('slug'),
            'date_created'=>date("Y-m-d h:i:s")
        );
        return $this->db->insert('menuitemschildren', $data);
    }
    public function updateChildItem($uid){
        $data = array(
            'menu_item' => $this->input->post('child_name'),
            'menu_icon' => $this->input->post('child_menu_icon'),
            'slug' => $this->input->post('slug')
        );
        $this->db->where('id',$this->input->post('cid'));
        $this->db->where('uid',$uid);
        return $this->db->update('menuitemschildren',$data);
    }
    public function updateParentItem($uid){
        $data = array(
            'menuitem' => $this->input->post('main_menu'),
            'menu_icon'=> $this->input->post('menu_icon')
        );
        $this->db->where('id',$this->input->post('menuid'));
        $this->db->where('uid',$uid);
        return $this->db->update('menuitems',$data);
    }
    public function deleteChildItem($uid){
        $this->db->where('id',$this->input->post('child_id'));
        $this->db->where('uid',$uid);
        return $this->db->delete('menuitemschildren');
    }
    public function getChildDetails($childID){
        $query = $this->db->get_where('menuitemschildren', array('id'=> $childID));
        return $query->result();
    }
    public function userSpaceAccessAjax($uid){
        $parentID = $this->input->post('parent_id');
        $childID = $this->input->post('child_id');
        $user_space = $this->input->post('user_space');
        $status = $this->input->post('status');
        $e = $this->chekIfMenuItemExistsUSA($uid,$childID,$user_space,2);
        if($e==0)
        {
            $data = array(
                'uid'=>$uid,
                'menu_item'=>$childID,
                'is_parent'=>2,
                'parent_id'=>$parentID,
                'user_space'=>$user_space,
                'can_access'=>$status,
                'date_created'=>date("Y-m-d h:i:s"),
                'date_last_updated'=>date("Y-m-d h:i:s")
            );
            $this->db->insert('user_space_access', $data);
            echo 'Creating New Record';
        }
        else
        {
            $data = array(
                'can_access' => $status,
            );
            $this->db->where('is_parent',2);
            $this->db->where('uid',$uid);
            $this->db->where('menu_item',$childID);
            $this->db->where('user_space',$user_space);
            $this->db->update('user_space_access',$data);
            echo 'Updating Access Status';
        }
        //chek if the user space access exists for this menu, update the status accordingly,
        //if it exists the user spcae and the ,menu item update, if it doesnt exist insert new record
    }
    public function userSpaceAccessAjaxParent($uid){
        $parent = $this->input->post('id');
        $user_space = $this->input->post('user_space');
        $status = $this->input->post('status');
        $e = $this->chekIfMenuItemExistsUSA($uid,$parent,$user_space,1);
        if($e==0)
        {
            $data = array(
                'uid'=>$uid,
                'menu_item'=>$parent,
                'is_parent'=>1,
                'parent_id'=>$parent,
                'user_space'=>$user_space,
                'can_access'=>$status,
                'date_created'=>date("Y-m-d h:i:s"),
                'date_last_updated'=>date("Y-m-d h:i:s")
            );
            $this->db->insert('user_space_access', $data);
            echo 'Creating New Parent Access Record';
        }
        else
        {
            $data = array(
                'can_access' => $status,
            );
            $this->db->where('is_parent',1);
            $this->db->where('uid',$uid);
            $this->db->where('menu_item',$parent);
            $this->db->where('user_space',$user_space);
            $this->db->update('user_space_access',$data);
            echo 'Updating Parent Access Status';
        }
    }
    public function chekIfMenuItemExistsUSA($uid,$childID,$user_space,$is_parent){
        $this->db->where('is_parent',$is_parent);
        $this->db->where('uid',$uid);
        $this->db->where('menu_item',$childID);
        $this->db->where('user_space',$user_space);
        $this->db->from('user_space_access');
        return $num = $this->db->count_all_results();
    }
    public function getParentMenuItemsUsa($userSpace){
        $this->db->order_by("id", "asc"); 
        $query = $this->db->get_where('user_space_access', array('is_parent' => 1,'user_space'=>$userSpace,'can_access'=>1));
        return $query->result();
    }
    public function get_count_biz($uid){
        $this->db->from("purchases");
        return $num = $this->db->count_all_results();
    }
    public function getBiz($userSpace,$uid){
        // $this->db->limit($limit, $start);
        $this->db->order_by("id", "desc");
        if($userSpace != "3")
        {
            if($userSpace == "4")
            {
                //get the uid
                //get the subcounty
                $subcounty = $this->getUserDet($uid)[0]->work_station;
                //get subcounty id
                $subID = $this->getSubIdBySubName($subcounty)[0]->id;
                //get the biz based on that subcounty
                $this->db->where('subcounty',$subID);
                $query = $this->db->get('biz');
            }
            else
            {
                $this->db->where('uid',$uid);
                $query = $this->db->get('biz');
            }
        }
        
        else
        {
            $query = $this->db->get('biz');
        }
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getBizbyWard($ward){
        $this->db->where('ward',$ward);
        $query = $this->db->get('biz');
        return $query->result();
    }
    public function getLands($userSpace,$uid){
        if($uid == "2"){
            $query = $this->db->get('land_registry');
        }
        else
        {
            $this->db->where('uid',$uid);
            $query = $this->db->get('land_registry');
        }
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getLand($id){
        $query = $this->db->get_where('land_registry', array('id' => $id));
        return $query->result();
    }
    public function getSubcouty(){
        $query = $this->db->get('sub_county');
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function chekIfchek($uid,$childID,$user_space){
        $where = array(
            'is_parent'=>2,
            'uid'=>$uid,
            'menu_item'=>$childID,
            'user_space'=>$user_space
        );
        $query = $this->db->get_where('user_space_access',$where);
        if($query->num_rows()==0){
            return 0;
        }
        else
        {
            $res = $query->result();
            return $res[0]->can_access;
        }

    }
    public function chekIfchekCanEdit($uid,$childID,$user_space){
        $where = array(
            'is_parent'=>2,
            'uid'=>$uid,
            'menu_item'=>$childID,
            'user_space'=>$user_space
        );
        $query = $this->db->get_where('user_space_access',$where);
        if($query->num_rows()==0){
            return 0;
        }
        else
        {
            $res = $query->result();
            return $res[0]->can_edit;
        }
    }
    public function getParentAccessStatus($parentId,$userSpaceId){
        $where = array(
            'is_parent'=>1,
            'menu_item'=>$parentId,
            'user_space'=>$userSpaceId
        );
        $query = $this->db->get_where('user_space_access',$where);
        if($query->num_rows()==0){
            return 0;
        }
        else
        {
            $res = $query->result();
            return $res[0]->can_access;
        }
    }
    public function chekIfchekCanDelete($uid,$childID,$user_space){
        $where = array(
            'is_parent'=>2,
            'uid'=>$uid,
            'menu_item'=>$childID,
            'user_space'=>$user_space
        );
        $query = $this->db->get_where('user_space_access',$where);
        if($query->num_rows()==0){
            return 0;
        }
        else
        {
            $res = $query->result();
            return $res[0]->can_delete;
        }
    }
    public function userSpaceAccessAjaxCanEdit($uid){
        $parentID = $this->input->post('parent_id');
        $childID = $this->input->post('child_id');
        $user_space = $this->input->post('user_space');
        echo 'Status : '.$status = $this->input->post('status');
            $data = array(
                'can_edit' => $status,
            );
            $this->db->where('is_parent',2);
            $this->db->where('uid',$uid);
            $this->db->where('menu_item',$childID);
            $this->db->where('user_space',$user_space);
            $this->db->update('user_space_access',$data);
            echo 'Updating Edit Access Status';
        //chek if the user space access exists for this menu, update the status accordingly,
        //if it exists the user spcae and the ,menu item update, if it doesnt exist insert new record
    }
    public function userSpaceAccessAjaxCanDelete($uid){
        $parentID = $this->input->post('parent_id');
        $childID = $this->input->post('child_id');
        $user_space = $this->input->post('user_space');
        $status = $this->input->post('status');
            $data = array(
                'can_delete' => $status,
            );
            $this->db->where('is_parent',2);
            $this->db->where('uid',$uid);
            $this->db->where('menu_item',$childID);
            $this->db->where('user_space',$user_space);
            $this->db->update('user_space_access',$data);
            echo 'Updating Delete Access Status';
        //chek if the user space access exists for this menu, update the status accordingly,
        //if it exists the user spcae and the ,menu item update, if it doesnt exist insert new record
    }
}
