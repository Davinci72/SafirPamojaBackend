<?php 
class Cosmere_Mpesa_model extends CI_Model{
    var $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    // var $passkey = 'fa21339237300f8b6f7a8e47254b3b2b1f4c5a17e0c4a3e071551239fdce713c';
    // var $businessSC = '182729';
    var $businessSC = '182729';
    var $passkey = 'fa21339237300f8b6f7a8e47254b3b2b1f4c5a17e0c4a3e071551239fdce713c';

    var $jijengeBussinessSC = '4083021';
    var $jijengePasskey = '63ca26a7fbbcf4b08c0901af34e26701d9f7c5099e7a20627f5274ed9f0bff83';

    var $supaTribeSC = '4072931';
    var $supaTribePassKey = 'ee68d33b0fc73ce775f91e50f5e951548ce5574ecb5bc002c2ee1175b82b8df3';
    var $supaTribeCK = 'myWaBtiaY5UXO3V710k30QZCZSgyMWtJ';
    var $supaTribeCS = 'WaWGP1EdGYnFoiCu';

    //izone credentials
    var $izoneSC = '3980209';
    var $izonePassKey = 'fab9c29ea74c1d8746e3f036487e796511caceb8f15bd8e1f5dce040d6276ee8';
    var $izoneCK = 'A8FvXDkVj02fGidlTHnD37AaxEtKBfR4';
    var $izoneCS = 'gkQwKUrnsX2GZ15u';
    
    public function tstmp(){
        return date('YmdHis');
    }
    public function encodedPass(){
        return base64_encode($this->businessSC.$this->passkey.$this->tstmp());
    }
    public function encodedPassSupatribe(){
        return base64_encode($this->supaTribeSC.$this->supaTribePassKey.$this->tstmp());
    }
    public function encodedPassIzone(){
        return base64_encode($this->izoneSC.$this->izonePassKey.$this->tstmp());
    }
    public function encodedPassJijenge(){
        return base64_encode($this->jijengeBussinessSC.$this->jijengePasskey.$this->tstmp());
    }
    public function pushStk($phone,$amt,$acc){
        $data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $this->businessSC,
            'Password' => $this->encodedPass(),
            'Timestamp' => $this->tstmp(),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amt,
            'PartyA' => $phone,
            'PartyB' => $this->businessSC,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'http://157.230.164.75:8040/pos/Ncr/transResult',
            'AccountReference' => $acc,
            'TransactionDesc' => $acc
          );
        $resp = $this->caller($data);
        $arr = $this->checkMpesaResp($resp);
        if(array_key_exists('errorMessage', $arr)){
            if($arr['errorMessage'] =='Invalid Access Token'){
                //'Get New Access Code and redo the transaction again'; 
                
                //log the error somewhere
                $newToken = $this->refreshToken();
                $tokenArr = json_decode($newToken,true);
                $this->updateTokens($tokenArr['access_token']);
                return $resp = $this->caller($data);
                //update token table then finish the transaction
            }
        }
        else
        {
            return $resp;
        }
    }
    public function pushStkIzone($phone,$amt,$acc){
        $data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $this->izoneSC,
            'Password' => $this->encodedPassIzone(),
            'Timestamp' => $this->tstmp(),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amt,
            'PartyA' => $phone,
            'PartyB' => $this->izoneSC,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'http://157.230.164.75:8040/pos/Ncr/transResultIzone',
            'AccountReference' => $phone,
            'TransactionDesc' => $phone
          );
        $resp = $this->callerIzone($data);
        $arr = $this->checkMpesaResp($resp);
        if(array_key_exists('errorMessage', $arr)){
            if($arr['errorMessage'] =='Invalid Access Token'){
                //'Get New Access Code and redo the transaction again'; 
               
                //log the error somewhere
                $newToken = $this->refreshTokenIzone();
                $tokenArr = json_decode($newToken,true);
                $this->updateTokensIzone($tokenArr['access_token']);
                return $resp = $this->callerIzone($data);
                //update token table then finish the transaction
            }
        }
        else
        {
            return $resp;
        }
    }

    public function pushStkSupaTribe($phone,$amt,$acc){
        $data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $this->supaTribeSC,
            'Password' => $this->encodedPassSupatribe(),
            'Timestamp' => $this->tstmp(),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amt,
            'PartyA' => $phone,
            'PartyB' => $this->supaTribeSC,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'http://157.230.164.75:8040/pos/Ncr/transResult',
            'AccountReference' => 'SafirPamoja',
            'TransactionDesc' => 'NCR-'.$acc
          );
        $resp = $this->callerStawika($data);
        $arr = $this->checkMpesaResp($resp);
        if(array_key_exists('errorMessage', $arr)){
            if($arr['errorMessage'] =='Invalid Access Token'){
                //'Get New Access Code and redo the transaction again'; 
               
                //log the error somewhere
                $newToken = $this->refreshTokenStawika();
                $tokenArr = json_decode($newToken,true);
                $this->updateTokensStawika($tokenArr['access_token']);
                return $resp = $this->callerStawika($data);
                //update token table then finish the transaction
            }
        }
        else
        {
            return $resp;
        }
    }


    public function pushStkJijenge($phone,$amt,$acc){
        $data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $this->jijengeBussinessSC,
            'Password' => $this->encodedPassJijenge(),
            'Timestamp' => $this->tstmp(),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amt,
            'PartyA' => $phone,
            'PartyB' => $this->jijengeBussinessSC,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'http://157.230.164.75:8040/pos/Ncr/Jijenge',
            'AccountReference' => 'jijenge-'.$acc,
            'TransactionDesc' => 'jijenge-'.$acc
          );
          
        $resp = $this->callerJijenge($data);
        $arr = $this->checkMpesaResp($resp);
        if(array_key_exists('errorMessage', $arr)){
            if($arr['errorMessage'] =='Invalid Access Token'){
                //'Get New Access Code and redo the transaction again'; 
                
                //log the error somewhere
                $newToken = $this->refreshTokenJijenge();
                $tokenArr = json_decode($newToken,true);
                $this->updateTokensJijenge($tokenArr['access_token']);
                return $resp = $this->callerJijenge($data);
                //update token table then finish the transaction
            }
            if($arr['errorMessage'] =='Wrong credentials'){
                //'Get New Access Code and redo the transaction again'; 
                
                //log the error somewhere
               $e = array(
                "Error"=>$arr['errorMessage']
               );
                return json_encode($e);
                //update token table then finish the transaction
            }
        }
        else
        {
            return $resp;
        }
    }
    public function checkMpesaResp($dataStr){
        return $arr = json_decode($dataStr,true);
    }
    public function getCountUssdPhone($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM ussdncr WHERE phone ='.$phone);
        if($query->num_rows() == 1){
            $row = $query->row();
            return $c = $row->completed;
        }
        else
        {
            return 0;
        }


        // $this->db->where('uid',$uid);
        // $this->db->where('completed','7');
        // $this->db->from("ussdncr");
        // return $num = $this->db->count_all_results();
    }

    public function addNewClientUssd($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            'phone'=>$phone,
            'names'=>'',
            'dob'=>'',
            'email'=>'',
            'id_no'=>'',
            'nationality'=>'',
            'completed'=>0
        );
        return $jengaDb->insert('ussdncr', $data);
    }
    
    public function updateUser($where,$string,$phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            $where => $string,
        );
        $jengaDb->where('phone',$phone);
        return $jengaDb->update('ussdncr',$data);
    }
    public function logResult($resultDesc,$cheoutReqId){
            $jengaDb = $this->load->database('jenga',TRUE);
            //update response on mpesa responses table where merchant request id = that one
            $data = array(
                'status' => $resultDesc
            );
            $jengaDb->where('checkoutid',$cheoutReqId);
            return $jengaDb->update('mpesa_response',$data);
    }
    public function logResultIzone($resultDesc,$cheoutReqId){
        $jengaDb = $this->load->database('jenga',TRUE);
        //update response on mpesa responses table where merchant request id = that one
        $data = array(
            'status' => $resultDesc,
            'processed'=>1
        );
        $jengaDb->where('checkoutid',$cheoutReqId);
        return $jengaDb->update('mpesa_response_izone',$data);
}
    public function logResponse($phone,$amount,$reqId,$checkoutId,$respCode,$respDesc,$custMsg){
        $jengaDb = $this->load->database('jenga',TRUE);
        if($reqId==''){
            echo 'Error';exit();
        }
        $data = array(
            'phone'=>$phone,
            'amount'=>$amount,
            'requestid'=>$reqId,
            'checkoutid'=>$checkoutId,
            'responsecode'=>$respCode,
            'respdesc'=>$respDesc,
            'customermsg'=>$custMsg,
            "processed"=>0,
            'date_c'=>date("Y-m-d h:i:s")
        );
        $jengaDb->insert('mpesa_response', $data);
    }

    public function logResponseIzone($phone,$amount,$position,$county,$reqId,$checkoutId,$respCode,$respDesc,$custMsg){
        $jengaDb = $this->load->database('jenga',TRUE);
        if($reqId==''){
            echo 'Error';exit();
        }
        $data = array(
            'phone'=>$phone,
            'amount'=>$amount,
            'position'=>$position,
            'county'=>$county,
            'requestid'=>$reqId,
            'checkoutid'=>$checkoutId,
            'responsecode'=>$respCode,
            'respdesc'=>$respDesc,
            'customermsg'=>$custMsg,
            "processed"=>0,
            'date_c'=>date("Y-m-d h:i:s")
        );
        $jengaDb->insert('mpesa_response_izone', $data);
    }

    public function logSuccessResult($MerchantRequestID,$CheckoutRequestID,$ResultCode,$resultDesc,$amount,$transCode,$transDate,$phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            'reqID'=>$MerchantRequestID,
            'chekoutreqID'=>$CheckoutRequestID,
            'resCode'=>$ResultCode,
            'resDesc'=>$resultDesc,
            'amt'=>$amount,
            'transCode'=>$transCode,
            'transDate'=>$transDate,
            'phone'=>$phone,
            'date_c'=>date("Y-m-d h:i:s")
        );
        //insert the data
        $jengaDb->insert('mpesa_transactions', $data);
        //check the amount if greater than 100 send the jumia voucher if less send thank you message
        //send to kenoobi endpoint
        $d2 = array(
            "msisdn"=>$phone,
            "transcode"=>$transCode,
            "amount"=>$amount,
            "acc_no"=>"Jenga Jirani",
            "date_completed"=>$transDate,
            "customer"=>""
        );
        $trackerData = json_encode($d2);
        // $this->sendToKenoobi($trackerData);

    }
    public function saveIpnMpesa($data){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->insert('mpesa_ipn', $data);

        $d2 = array(
            "msisdn"=>$data['msisdn'],
            "transcode"=>$data['trans_id'],
            "amount"=>$data['transamt'],
            "acc_no"=>"Jenga Jirani",
            "date_completed"=>$data['transtime'],
            "customer"=>$data['fname']. " ".$data['midle_name']. " ".$data['last_name']
        );
        $trackerData = json_encode($d2);
    }
    public function sendTicket($phone,$ticket){
        $d = array(
            "message"=>"Thank you For booking with ncr, click ".$ticket." to download your ticket",
            "phone_number"=>$phone,
            "service_id"=>"6019542000189454",
            "access_code"=>"COSMERE",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
        $this->sendTicketSms($data);
    }
    public function apeckMessenger($data){
        $d = array(
            "message"=>"Thank you For booking with ncr, click ".$ticket." to download your ticket",
            "phone_number"=>$phone,
            "service_id"=>"6019542000189454",
            "access_code"=>"COSMERE",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
    }
    public function sendPCEAmessage($phone,$id){
        $d = array(
            "message"=>"Congratulations! You have successfully registered as a member of APECK. Your registration number is APECK00".$id.". Thank you.",
            "phone_number"=>"".$phone."",
            "service_id"=>"6019542000189454",
            "access_code"=>"APECK",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
        $this->sendTicketSms($data);
    }

    public function getTransactions($limit,$start){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->limit($limit, $start);
        $jengaDb->order_by("id", "desc"); 
        $query = $jengaDb->get('mpesa_ipn');
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getIzonePending($checkoutid){
        $jengaDb = $this->load->database('jenga',TRUE);
        $where = array('checkoutid'=>$checkoutid,'processed'=>0);
        $jengaDb->where($where);
        $query = $jengaDb->get('mpesa_response_izone');
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function getIDUssd($phone){
        $where = array('phone'=>$phone);
        $this->db->where($where);
        $query = $this->db->get('uda_ussd');
        // echo '<pre>';
        // var_dump($this->db);exit('at database');
        return $query->result();
    }
    public function get_count_mpesa_transactions(){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->from("mpesa_ipn");
        return $num = $jengaDb->count_all_results();
    }
  
    public function sendTicketSms($data){
        // var_dump($data);exit();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://cms.cosmereventures.com/sms/v1/send/simple/sms",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "authorization: Token bb2ad83206b1f9b6fffd79be3a16b562b4ae01a8",
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: ad999435-55a7-4f5d-c2e8-f50151104dbb"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
    }

    public function callIzoneEndpoint($data,$url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "authorization: Token eb9a1c1c219564f1d4e72d10b52f1670b7a01bf9",
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: ad999435-55a7-4f5d-c2e8-f50151104dbb"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
         "cURL Error #:" . $err;
        } else {
         $response;
        }
    }
    public function updateTokens($token){
        $data = array(
            'token' => $token,
        );
        return $this->db->update('s_token',$data);
    }
    public function updateTokensStawika($token){
        // exit('At Token Update :'.$token);
        $data = array(
            'token' => $token,
        );
        return $this->db->update('izone_token',$data);
    }
    public function updateTokensIzone($token){
        $data = array(
            'token' => $token,
        );
        return $this->db->update('izone_token',$data);
    }
    public function updateTokensJijenge($token){
        $data = array(
            'token' => $token,
        );
        return $this->db->update('j_token',$data);
    }
    public function updatePaidTicket($ticketID){
        $ncrDB = $this->load->database('ncr',TRUE);
        $data = array(
            'is_paid' =>1,
        );
        $ncrDB->where('id',$ticketID);
        return $ncrDB->update('ticket_orders',$data);
    }
    public function caller($data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->getToken())); //setting custom header
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        //   print_r($curl_response);
          return $curl_response;
    }
    public function callerStawika($data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->getTokenSupaTribe())); //setting custom header
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        //   print_r($curl_response);
          return $curl_response;
    }

    public function callerIzone($data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->getTokenIzone())); //setting custom header
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        //   print_r($curl_response);
          return $curl_response;
    }
    public function callerJijenge($data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->getTokenJijenge())); //setting custom header
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        //   print_r($curl_response);
          return $curl_response;
    }
    public function notifications(){
        echo 'Success';
    }
    public function refreshToken(){
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode('q4OGSufcQ4hAAZaz2BhUbILACA3fmcZu:D7QwPs81si3YlFws');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
    }
    public function refreshTokenStawika(){
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($this->supaTribeCK.":".$this->supaTribeCS);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
    }
    public function refreshTokenIzone(){
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($this->izoneCK.":".$this->izoneCS);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
    }
    public function refreshTokenJijenge(){
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode('5hd3GSPvyC7PexxSYdpGMwTL8Mi7EIqY:uqLCrff2ANyNYDRx');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
    }
    public function getTicketID($phone){
        $ncrDB = $this->load->database('ncr',TRUE);
        $sql = 'SELECT * FROM ticket_orders WHERE phone="'.$phone.'" AND is_paid=0 LIMIT 1';
        $query = $ncrDB->query($sql);
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->id;
        }
        else
        {
            return false;
        }
    }
    public function getTicketDetails($ticketID){
        $ncrDB = $this->load->database('ncr',TRUE);
        $query = $ncrDB->query('SELECT * FROM ticket_orders WHERE id='.$ticketID);
        if($query->num_rows() == 1){
            return $row = $query->row();
        }
        else
        {
            return false;
        }
    }
    public function getDeparture($id){
        $ncrDB = $this->load->database('ncr',TRUE);
        $query = $ncrDB->query('SELECT * FROM train_timings WHERE id='.$id);
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->departure_time;
        }
        else
        {
            return false;
        }
    }
    public function getToken(){
        $query = $this->db->query('SELECT * FROM s_token');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->token;
        }
        else
        {
            return false;
        }
    }
    public function getTokenJijenge(){
        $query = $this->db->query('SELECT * FROM j_token');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->token;
        }
        else
        {
            return false;
        }
    }
    public function getTokenSupaTribe(){
        $query = $this->db->query('SELECT * FROM supa_token');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->token;
        }
        else
        {
            return false;
        }
    }
    public function getTokenIzone(){
        $query = $this->db->query('SELECT * FROM izone_token');
        if($query->num_rows() == 1){
            $row = $query->row();
            return $email = $row->token;
        }
        else
        {
            return false;
        } 
    }
}