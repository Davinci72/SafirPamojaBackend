<?php
class Mpesa_Model extends CI_Model {
    var $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    // var $passkey = 'fa21339237300f8b6f7a8e47254b3b2b1f4c5a17e0c4a3e071551239fdce713c';
    // var $businessSC = '182729';
    var $businessSC = '4089543';
    var $passkey = '81f413f7ab6141afcb0ceefd1738cbc41f5efd9e21dba66f99d3b0cafa67ba4b';
    public function tstmp(){
        return date('YmdHis');
    }
    public function encodedPass(){
        return base64_encode($this->businessSC.$this->passkey.$this->tstmp());
    }
    public function pushStk($phone,$amt){
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
            'CallBackURL' => 'https://kajiadorevenue.info/Jenga/transResult',
            'AccountReference' => 'SAFIRI',
            'TransactionDesc' => 'SAFIRI'
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
    public function checkMpesaResp($dataStr){
        return $arr = json_decode($dataStr,true);
    }
    public function getCountUssdPhoneUda($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM ussduda WHERE phone ='.$phone);
        if($query->num_rows() == 1){
            $row = $query->row();
            return $c = $row->completed;
        }
        else
        {
            return 0;
        }
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
    public function addNewClientUssdUda($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            'phone'=>$phone,
            'names'=>'',
            'dob'=>'',
            'email'=>'',
            'id_no'=>'',
            'county'=>'',
            'ward'=>'',
            'completed'=>0
        );
        return $jengaDb->insert('ussduda', $data);
    }
    public function updateUser($where,$string,$phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            $where => $string,
        );
        $jengaDb->where('phone',$phone);
        return $jengaDb->update('ussdncr',$data);
    }
    public function updateUserUda($where,$string,$phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            $where => $string,
        );
        $jengaDb->where('phone',$phone);
        return $jengaDb->update('ussduda',$data);
    }
    public function removeUserUda($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->where('phone',$phone);
        $jengaDb->delete('ussduda');
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
            'date_c'=>date("Y-m-d h:i:s")
        );
        $jengaDb->insert('mpesa_response', $data);
    }

    public function logSuccessResult($MerchantRequestID,$CheckoutRequestID,$ResultCode,$resultDesc,$amount,$transCode,$transDate,$phone){
        // $jengaDb = $this->load->database('jenga',TRUE);
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
        $this->db->insert('mpesa_transactions', $data);
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
        $voucher = $this->getVoucher();
        $d = array(
            "message"=>"Thank you for donating on Jenga Jirani, You have been awarded aJumia Voucher Worth KSHS 100,".$voucher." Please redeem the voucher on jumia.co.ke",
            "phone_number"=>$data['msisdn'],
            "service_id"=>"6019542000189454",
            "access_code"=>"JengaJirani",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
        $this->sendVouchers($data);
        // $this->sendToKenoobi($trackerData);
    }

    public function saveIpnMpesaIzone($data){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->insert('mpesa_ipn', $data);
    }

    public function sendTicket($phone){
        $d = array(
            "message"=>"Thank you For booking with ncr, click https://bit.ly/3vs2F8T to download your ticket",
            "phone_number"=>$phone,
            "service_id"=>"6019542000189454",
            "access_code"=>"COSMERE",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
        $this->sendVouchers($data);
    }
    public function getVoucher(){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM jumiavouchers WHERE status =0 LIMIT 1');
        if($query->num_rows() == 1){
             
            $row = $query->row();
            $this-> updateVoucherStatus($row->id);
            return $row->voucher;
        }
        else
        {
            return false;
        }
    }
    public function getREgistrationDetails($id){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM ussduda WHERE id_no ='.$id);
        if($query->num_rows() == 1){
             
            return $row = $query->row();
        }
        else
        {
            return false;
        }
    }
    public function getREgistrationDetailsIebc($idNo){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM iebc WHERE id_passport_no ='.$idNo.' LIMIT 1');
        if($query->num_rows() == 1){
             
            return $row = $query->row();
        }
        else
        {
            return false;
        }
    }
    public function getREgistrationDetailsSession($phone){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM ussduda WHERE phone ='.$phone);
        if($query->num_rows() == 1){
             
            return $row = $query->row();
        }
        else
        {
            return false;
        }
    }
    public function sendKfmSMS($name,$phone){
        $d = array(
            "message"=>"Dear ".$name." ,Thank you for visiting Kirinyaga Flour Mills, we value your partnership. Call us on 0773475566 to book your order.",
            "phone_number"=>$phone,
            "service_id"=>"6019542000189454",
            "access_code"=>"KIRINYAGAFM",
            "unique_identifier"=>rand(100000,10000000)
        );
        $data = json_encode($d);
        $this->sendVouchers($data);
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
    public function get_count_mpesa_transactions(){
        $jengaDb = $this->load->database('jenga',TRUE);
        $jengaDb->from("mpesa_ipn");
        return $num = $jengaDb->count_all_results();
    }
    public function runOnce(){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM contacts WHERE `status`=0 LIMIT 400');
        echo '<pre>';
        $res = $query->result_array();
        foreach($res as $r){
            $id = $r['id'];
            $phone = $r['phone'];
            $amount = $r['amount'];
            if($amount >= 100){
                $voucher = $this->getVoucher();
                $d = array(
                    "message"=>"Thank you for joining us for the Jenga Jirani Festival and showing some love. Your kind donation will help us continue reaching out to those in need. You have been awarded a Jumia Voucher Worth KSHS100, Voucher Code :".$voucher." Please redeem the voucher on jumia.co.ke",
                    "phone_number"=>$phone,
                    "service_id"=>"6019542000189454",
                    "access_code"=>"JengaJirani",
                    "unique_identifier"=>rand(100000,10000000)
                );
                $data = json_encode($d);
                echo 'Phone Number : '.$phone.' Has received Voucher Code : '.$voucher.'<br />';
                $this->sendVouchers($data);
                $data2Update = array(
                    'voucher_code'=>$voucher,
                    'status' =>1
                );
                $jengaDb->where('id',$id);
                $jengaDb->update('contacts',$data2Update);
                // exit();
            }
        }
    }
    public function updateVoucherStatus($id){
        $jengaDb = $this->load->database('jenga',TRUE);
        $data = array(
            'status' =>1
        );
        $jengaDb->where('id',$id);
        return $jengaDb->update('jumiavouchers',$data);
    }
    public function sendVouchers($data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://cms.cosmereventures.com/sms/v1/send/simple/sms",
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
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
    }
    public function updateTokens($token){
        $data = array(
            'token' => $token,
        );
        return $this->db->update('s_token',$data);
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
    public function sendToKenoobi($data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://jengajirani.org/tracker/mpesa.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "postman-token: 75b1b66d-66e8-c6f1-cd7f-f7b83449bcd8"
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
    public function notifications(){
        echo 'Success';
    }
    public function refreshToken(){
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode('pOp7mKuoeDHVpC9y1thVFtDVbhgRxEtG:TgMXnmaV53sAUtJN');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
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
    public function cleanData(){
        $jengaDb = $this->load->database('jenga',TRUE);
        $query = $jengaDb->query('SELECT * FROM contacts');
        if($query->num_rows() >= 1){
            $arr = $query->result();
            echo '<pre>';
            // var_dump($query->result());exit();
            foreach($arr as $r)
            {
                $array_name = explode (' - ', $r->phone, 2);
                $newPhone = $array_name[0];
                echo $id = $r->id;
                $data = array(
                    'phone' => $newPhone
                );
                $jengaDb->where('id',$r->id);
                $jengaDb->update('contacts',$data);
            }
        }
        else
        {
            echo 'Empty : No Such Results Found :'.$query->num_rows();
        }
    }
}