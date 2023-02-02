<?php 
class Ncr extends CI_Controller {
    var $tucha_WC_CK = 'ck_a49abdb1e9f894c1a6ff0cb63728f29bb04e1542';
    var $tucha_WC_CS = 'cs_064f01049e382b5440e60bb4eb8042912bc9bff6';
    var $tuchaGoo_WC_CK = 'ck_65f045cced19ac37f5e8ce7a665e5ae3f5affaa0';
    var $tuchaGoo_WC_CS = 'cs_b9a5b47162e41bb541d32a34f57e6798fdea52ab';
        public function __construct(){
            parent::__construct();
            $this->load->model('Cosmere_Mpesa_model');
            $this->load->model('Clients_model');
            $this->load->model('Server_model');
        }
        public function index(){
            echo 'You are not allowed here';
        }
        public function sendPush() {
            // $this->load->model('Sms_model');
            $string = file_get_contents('php://input');
            $arr = $this->returnArr($string);
            echo $response = $this->Cosmere_Mpesa_model->pushStk($arr['phone'],$arr['amount'],$arr['acc']);
            // var_dump($arr['phone']);exit();
            // echo $string;
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/ncr.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string.$response);
            fclose($myfile);
            //log request
            //log response
            $this->logResponse($response,$arr['phone'],$arr['amount'],$arr['acc']);
            //log result
        }
        public function sendPushJijenge(){
               // $this->load->model('Sms_model');
               $string = file_get_contents('php://input');
               $arr = $this->returnArr($string);
               echo $response = $this->Cosmere_Mpesa_model->pushStkJijenge($arr['phone'],$arr['amount'],$arr['acc']);
               // var_dump($arr['phone']);exit();
               // echo $string;
               $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jijenge.txt", "a") or die("Unable to open file!");
               fwrite($myfile, $string.$response);
               fclose($myfile);
               //log request
               //log response
               $this->logResponse($response,$arr['phone'],$arr['amount'],$arr['acc']);
               //log result
        }
        public function sendPushSupa(){
              // $this->load->model('Sms_model');
              echo $string = file_get_contents('php://input');
              $arr = $this->returnArr($string);
              echo $response = $this->Cosmere_Mpesa_model->pushStkSupaTribe($arr['phone'],$arr['amount'],$arr['acc']);
              // var_dump($arr['phone']);exit();
              // echo $string;
              $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jijenge.txt", "a") or die("Unable to open file!");
              fwrite($myfile, $string.$response);
              fclose($myfile);
              //log request
              //log response
              $this->logResponse($response,$arr['phone'],$arr['amount'],$arr['acc']);
              //log result
        }
        public function sendPushIzone(){
        // $this->load->model('Sms_model');
        $string = file_get_contents('php://input');
        $arr = $this->returnArr($string);
        echo $response = $this->Cosmere_Mpesa_model->pushStkIzone($arr['phone'],$arr['amount'],$arr['phone']);
        // var_dump($arr['phone']);exit();
        // echo $string;
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jijenge.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string.$response);
        fclose($myfile);
        //log request
        //log response
        $this->logResponseIzone($response,$arr['phone'],$arr['amount'],$arr['possition'],$arr['county'],$arr['phone']);
        //log result
        }
        public function refreshTokenStawika(){
            echo $this->Cosmere_Mpesa_model->refreshTokenStawika();
        }
        public function refreshTokenIzone(){
            echo $this->Cosmere_Mpesa_model->refreshTokenStawika();
        }
        public function Jijenge(){
            $this->load->model('Sms_model');
            $string = file_get_contents('php://input');
            $this->logResult($string);
            $arr = $this->returnArr($string);
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jijengeResult.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
        }
        public function tucha(){
            $string = file_get_contents('php://input');
            // $string = '{"order_id":"2052"}';
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/ncr.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            $arr = $this->returnArr($string);
            $orderID = $arr['order_id'];
            $orderDetails = $this->tuchaTest($orderID);
            $it = round($orderDetails['total'], 0);
            // var_dump($orderDetails['phone']);
            echo $response = $this->Cosmere_Mpesa_model->pushStk($orderDetails['phone'],$it,$orderID);

        }
        public function tuchagoo(){
            $string = file_get_contents('php://input');
            // $string = '{"order_id":"2052"}';
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/ncr.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            $arr = $this->returnArr($string);
            $orderID = $arr['order_id'];
            $orderDetails = $this->tuchaGooTest($orderID);
            $it = round($orderDetails['total'], 0);
            // var_dump($orderDetails['phone']);
            echo $response = $this->Cosmere_Mpesa_model->pushStk($orderDetails['phone'],$it,$orderID);
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/ncr.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $response);
            fclose($myfile);

        }
        public function testPush($phone,$amount,$acc){
            $it = round($amount, 0);
            echo $this->Cosmere_Mpesa_model->pushStk($phone,$it,$acc);
        }
        public function tuchaTest($orderID){
            // $wc = $this->Rail_model->wcT();
            $orders = 'https://oja.wvy.mybluehost.me/wp-json/wc/v3/orders/'.$orderID.'?consumer_key='.$this->tucha_WC_CK.'&consumer_secret='.$this->tucha_WC_CS;
            echo '<pre>';
            $order = $this->Server_model->getRequests($orders);
            
            $orderObject = json_decode($order,true);
            $rawPhone = $orderObject['billing']['phone'];
            $cleanPhone = $this->cleanPhone($rawPhone);
            $total = $orderObject['total'];
            return array(
                'phone'=>$cleanPhone,
                'total'=>$total
            );
            //call mpesa endpoint
            //put the phone and the order id in a table
            //once payment is done check the account number
            // var_dump($orderObject['total']);
        }
        public function tuchaGooTest($orderID){
            // $wc = $this->Rail_model->wcT();
            $orders = 'https://oja.wvy.mybluehost.me/tuchagoo/wp-json/wc/v3/orders/'.$orderID.'?consumer_key='.$this->tuchaGoo_WC_CK.'&consumer_secret='.$this->tuchaGoo_WC_CS;
            // echo '<pre>';
            $order = $this->Server_model->getRequests($orders);
            // var_dump($order);
            $orderObject = json_decode($order,true);
            $rawPhone = $orderObject['billing']['phone'];
            $cleanPhone = $this->cleanPhone($rawPhone);
            $total = $orderObject['total'];
            return $res =  array(
                'phone'=>$cleanPhone,
                'total'=>$total
            );
            //call mpesa endpoint
            //put the phone and the order id in a table
            //once payment is done check the account number
            // var_dump($orderObject['total']);
        }
        function cleanPhone($phone){
            $c = strlen($phone);
            if($c == 10){
                $res = substr($phone, 1);
                return '254'.$res;
            }
            if($c == 13){
                return $res = substr($phone, 1);
            }
            if($c == 12){
                return $phone;
            }
            if($c == 9 ){
                return '254'.$phone;
            }
        }

        public function tester(){
            $string = 'Davinci';
            echo $firstCharacter = substr($string, 0, 1);
        }
    
        public function activate($phone,$amt,$acc){
            // $this->load->model('Sms_model');
            // $string = file_get_contents('php://input');
            // $arr = $this->returnArr($string);
            echo $response = $this->Cosmere_Mpesa_model->pushStk($phone,$amt,$acc);
            // var_dump($arr['phone']);exit();
            // echo $string;
            // $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajirani.txt", "a") or die("Unable to open file!");
            // fwrite($myfile, $string);
            // fclose($myfile);
            //log request
            //log response
            // $this->logResponse($response,$arr['phone'],$arr['amount']);
            //log result
        }
        public function refreshTokensJijenge(){
            echo $this->Cosmere_Mpesa_model->getTokenJijenge();
        }
        public function kfm(){
            // $this->load->model('Sms_model');
            $string = file_get_contents('php://input');
            // $string = '{"service_name" : "MPESA",
            //     "business_number" : "888555",
            //     "transaction_reference" : "DE45GK45",
            //     "internal_transaction_id" : 3222,
            //     "transaction_timestamp" : "2013-03-18T13:57:00Z",
            //     "transaction_type" : "Paybill",
            //     "account_number" : "445534",
            //     "sender_phone" : "+254903119111",
            //     "first_name" : "John",
            //     "middle_name" : "K",
            //     "last_name" : "Doe",
            //     "amount" : 4000,
            //     "currency" : "KES",
            //     "signature" : "dfafwerewKkladaHOKJdafdf"
            //  }';
    
            $arr = $this->returnArr($string);
            $name = $arr['first_name'].' '.$arr['middle_name'].' '.$arr['last_name'];
            $phone = $arr['sender_phone'];
            $this->Cosmere_Mpesa_model->sendKfmSMS($name,$phone);
            // var_dump($arr['phone']);exit();
            // echo $string;
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/kirinyagafm.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            //log request
            //log response
            // $this->logKfmResp($arr,$response,$phone,$amount);
            //log result
        }
        public function cleanData(){
            $this->Cosmere_Mpesa_model->cleanData();
        }
        public function notifications(){
            $string = file_get_contents('php://input');
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResultIpn.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            $this->mpesaIpn($string);
            // $this->Cosmere_Mpesa_model->notifications();
        }
        public function cosIpn(){
            $string = file_get_contents('php://input');
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResultIpn.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            $this->mpesaCosIpn($string);
        }
        public function runOnce(){
            $this->Cosmere_Mpesa_model->runOnce();
        }
        public function transResult(){
            $this->load->model('Sms_model');
            $string = file_get_contents('php://input');
            $this->logResult($string);
            $arr = $this->returnArr($string);
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResult.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
        }
        public function transResultIzone(){
            $this->load->model('Sms_model');
            $string = file_get_contents('php://input');
            $this->logResultIzone($string);
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResult.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
        }
        public function logKfmResp($arr=array(),$response,$phone,$amount){
            $this->Cosmere_Mpesa_model->saveKfm($arr);
        }
        public function logResponse($response,$phone,$amount,$acc){
            $arr = json_decode($response,true);
            $reqId = $arr['MerchantRequestID'];
            $checkoutId = $arr['CheckoutRequestID'];
            $respCode = $arr['ResponseCode'];
            $respDesc = $arr['ResponseDescription'];
            $custMsg = $arr['CustomerMessage'];
            $this->Cosmere_Mpesa_model->logResponse($phone,$amount,$reqId,$checkoutId,$respCode,$respDesc,$custMsg);
        }
        public function logResponseIzone($response,$phone,$amount,$position,$county,$acc){
            $arr = json_decode($response,true);
            $reqId = $arr['MerchantRequestID'];
            $checkoutId = $arr['CheckoutRequestID'];
            $respCode = $arr['ResponseCode'];
            $respDesc = $arr['ResponseDescription'];
            $custMsg = $arr['CustomerMessage'];
            $this->Cosmere_Mpesa_model->logResponseIzone($phone,$amount,$position,$county,$reqId,$checkoutId,$respCode,$respDesc,$custMsg);
        }
        public function getTicketId($phone){
            return $this->Cosmere_Mpesa_model->getTicketID($phone);
        }
        public function getTicketIdCurl($phone){
            $url = "https://ncr.co.ke/Ticketing/getTicketID/".$phone;
            return $this->Server_model->getRequests($url);
        }
        public function getTicketDetails($ticketID){
            return $this->Cosmere_Mpesa_model->getTicketDetails($ticketID); 
        }
        public function getTicket($id){
            $url = "https://ncr.co.ke/Ticketing/getTicket/".$id;
            return json_decode($this->Server_model->getRequests($url));
        }
        public function getDeparture($id){
             $url = "https://ncr.co.ke/Ticketing/getDeparture/".$id;
            return json_decode($this->Server_model->getRequests($url));
        }
        public function updatePaidTicket($id){
            $url = "https://ncr.co.ke/Ticketing/updatePaid/".$id;
            return json_decode($this->Server_model->getRequests($url));
        }
        public function sendTicket($phone){
            //get the ticket_id
            // $ticketID = $this->getTicketId($phone);
            $ticketID = $this->getTicketIdCurl($phone);
            //get the ticket details
            // $details = $this->getTicketDetails($ticketID);
            $details = $this->getTicket($ticketID);
            // echo '<pre>';
            // var_dump($details);exit();
            // $this->Cosmere_Mpesa_model->updatePaidTicket($ticketID);
            $this->updatePaidTicket($ticketID);
            //ticket url here
            $ticketUrl = 'https://ncr.co.ke/ticket/'.$ticketID;
            $departure = $this->Cosmere_Mpesa_model->getDeparture($details->departure);
            //update status to paid
            $message = "Dear ".$details->passenger_f_names." Your NCR ticket ID is ".$ticketID." From ".$details->main_route.". ".$details->booking_ts.". Departure ".$departure.". Download your ticket from ".$ticketUrl;
            $d = array(
                "message"=>$message,
                "phone_number"=>"$phone",
                "service_id"=>"6019542000189454",
                "access_code"=>"COSMERE",
                "unique_identifier"=>rand(100000,10000000)
            );
           echo $data = json_encode($d);
    
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
                "authorization: Token eb9a1c1c219564f1d4e72d10b52f1670b7a01bf9",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 9267f8ed-efd6-d470-5f6e-3f123408de7c"
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
        public function logResult($json){
            //$json = '{"Body":{"stkCallback":{"MerchantRequestID":"31054-18957838-1","CheckoutRequestID":"ws_CO_03112020150902371301","ResultCode":0,"ResultDesc":"The service request is processed successfully.","CallbackMetadata":{"Item":[{"Name":"Amount","Value":5.00},{"Name":"MpesaReceiptNumber","Value":"OK394WESPV"},{"Name":"Balance"},{"Name":"TransactionDate","Value":20201103151000},{"Name":"PhoneNumber","Value":254725597552}]}}}}';
            $arr =  $this->returnArr($json);
            if($arr['Body']['stkCallback']['ResultCode'] == 1032){
                $this->Cosmere_Mpesa_model->logResult($arr['Body']['stkCallback']['ResultDesc'],$arr['Body']['stkCallback']['CheckoutRequestID']);
            }
            else
            {
                $MerchantRequestID = $arr['Body']['stkCallback']['MerchantRequestID'];
                $CheckoutRequestID = $arr['Body']['stkCallback']['CheckoutRequestID'];
                $ResultCode = $arr['Body']['stkCallback']['ResultCode'];
                $resultDesc = $arr['Body']['stkCallback']['ResultDesc'];
                $amount = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
                $transCode = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
                $transDate = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
                $phone = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
                //get id from ussd_uda
                $id = $this->Cosmere_Mpesa_model->getIDUssd($phone)[0]->id;
                //send success registration message
                echo $this->Cosmere_Mpesa_model->sendPCEAmessage($phone,$id);
                // $this->sendTicket($phone);
                $this->Cosmere_Mpesa_model->logSuccessResult($MerchantRequestID,$CheckoutRequestID,$ResultCode,$resultDesc,$amount,$transCode,$transDate,$phone);
                $this->Cosmere_Mpesa_model->logResult($arr['Body']['stkCallback']['ResultDesc'],$arr['Body']['stkCallback']['CheckoutRequestID']);
            }
            
            // echo '<pre>';
            // var_dump($arr['Body']['stkCallback']['CallbackMetadata']);
        }
        public function logResultIzone($json){
            //$json = '{"Body":{"stkCallback":{"MerchantRequestID":"31054-18957838-1","CheckoutRequestID":"ws_CO_03112020150902371301","ResultCode":0,"ResultDesc":"The service request is processed successfully.","CallbackMetadata":{"Item":[{"Name":"Amount","Value":5.00},{"Name":"MpesaReceiptNumber","Value":"OK394WESPV"},{"Name":"Balance"},{"Name":"TransactionDate","Value":20201103151000},{"Name":"PhoneNumber","Value":254725597552}]}}}}';
            $arr =  $this->returnArr($json);
            if($arr['Body']['stkCallback']['ResultCode'] == 1032){
                $this->Cosmere_Mpesa_model->logResult($arr['Body']['stkCallback']['ResultDesc'],$arr['Body']['stkCallback']['CheckoutRequestID']);
            }
            else
            {
                $MerchantRequestID = $arr['Body']['stkCallback']['MerchantRequestID'];
                $CheckoutRequestID = $arr['Body']['stkCallback']['CheckoutRequestID'];
                $ResultCode = $arr['Body']['stkCallback']['ResultCode'];
                $resultDesc = $arr['Body']['stkCallback']['ResultDesc'];
                $amount = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
                $transCode = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
                $transDate = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
                $phone = $arr['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
                //get position and county
                $details = $this->Cosmere_Mpesa_model->getIzonePending($CheckoutRequestID);
                if(count($details) == 0 ){

                }
                else
                {
                    $this->load->model("Izone_model");
                    $string = $details[0]->position.' '.$details[0]->county;
                    $this->Izone_model->checkCriteria($string,$phone);
                    // var_dump($string);
                    //send message with the success result
                    //update the result processed to 1
                    $this->Cosmere_Mpesa_model->logResultIzone($resultDesc,$CheckoutRequestID);
                    //send success message with download url link
                    // $this->sendTicket($phone);
                }

            }
            // echo '<pre>';
            // var_dump($arr['Body']['stkCallback']['CallbackMetadata']);
        }
        public function jumia($phone,$amount){
            if($amount >=100){
                //get the voucher
                //send the voucher
            }
        }
        public function sendToKenoobi(){
        $this->Cosmere_Mpesa_model->sendToKenoobi();
        }
        public function sendMessage($phone){
            echo $this->Cosmere_Mpesa_model->sendPCEAmessage($phone);
        }
        public function test(){
            echo "It works";
        }
        public function mpesaIpn($string){
            $arr = $this->returnArr($string);
            $data = array(
                'trans_type'=>$arr['TransactionType'],
                'trans_id'=>$arr['TransID'],
                'transtime'=>$arr['TransTime'],	
                'transamt'=>$arr['TransAmount'],
                'paybill_no'=>$arr['BusinessShortCode'],	
                'ac_no'=>$arr['BillRefNumber'],
                'org_bal'=>$arr['OrgAccountBalance'],
                'msisdn'=>$arr['MSISDN'],	
                'fname'	=>$arr['FirstName'],
                'midle_name'=>$arr['MiddleName'],	
                'last_name'=>$arr['LastName']
            );
            $this->Cosmere_Mpesa_model->saveIpnMpesa($data);
        }
       public function mpesaCosIpn($string){
        $arr = $this->returnArr($string);
        $data = array(
            'trans_type'=>$arr['TransactionType'],
            'trans_id'=>$arr['TransID'],
            'transtime'=>$arr['TransTime'],	
            'transamt'=>$arr['TransAmount'],
            'paybill_no'=>$arr['BusinessShortCode'],	
            'ac_no'=>$arr['BillRefNumber'],
            'org_bal'=>$arr['OrgAccountBalance'],
            'msisdn'=>$arr['MSISDN'],	
            'fname'	=>$arr['FirstName'],
            'midle_name'=>$arr['MiddleName'],	
            'last_name'=>$arr['LastName']
        );
        $this->Cosmere_Mpesa_model->saveIpnMpesa($data);
       }
        public function returnArr($json){
            return json_decode($json,true);
        }
        public function sessionResumeQ($n){
            switch($n){
                case 1:
                    return "CON Enter Your Full Names \n";
                break;
                case 2:
                    return "CON Enter your Date Of Birth \n";
                break;
                case 3:
                    return "CON Enter your Email Address \n";
                break;
                case 4:
                    return "CON Enter Your ID Number \n";
                break;
                case 5:
                    return "CON Enter Your County Of Residence \n";
                break;
                case 6:
                    return "CON Enter Constituency of Residence \n";
                break;
                case 7:
                    return "CON Ward of Residence \n";
                break;
            }
        }
        public function getValueFromStringArray($string,$n){
            $names = $this->splitter($string);
            if(count($names) == 1)
            {
                $v = $names[0];
            }
            else
            {
                $v = $names[$n];
            }
            return $v;
        }
        public function udaRegistrationUssd($phone,$string){
            $status = $this->Cosmere_Mpesa_model->getCountUssdPhoneUda($phone);
            $Details = $this->Cosmere_Mpesa_model->getREgistrationDetailsSession($phone);
            // if($status == "9"){
            //     $response = "END Thank you, You are already registerd";
            // }
            //scenario one
            if(empty($string) && $status == 9)
            {
                $response  = "CON Welcome To United Democratic Alliance USSD Service\n";
                $response .= "1. Register \n";
                $response .= "2. Check Registration Status \n";
                $response .= "3. Check IEBC Registration Status \n";
            }
            if($string=="1" && $status == 9)
            {
                $response  =  "CON Enter Your ID Number \n";
            }
            if($string == "2"){
                //get the id number
                $response = "CON Enter Your ID Number \n";
            }
            if($string == "3"){
                //get the id number
                $response = "CON Enter Your ID Number \n";
            }
            //check is string has 2 in it
            $firstTwoCharacters = substr($string, 0, 2);
            if($firstTwoCharacters == "2*")
            {
                $idNo = $this->splitter($string);
                $rDetails = $this->Cosmere_Mpesa_model->getREgistrationDetails($idNo[1]);
                if($rDetails ==false)
                {
                    $response = "END The ID Number Is Not Registerd, Dial *569# to register \n";
                }
                else
                {
                    $response = "END Your Registration Details \n";
                    $response .= "Names :".$rDetails->names." \n";
                    $response .= "Date Of Birth :".$rDetails->dob." \n";
                    $response .= "Email :".$rDetails->email." \n";
                    $response .= "ID Number :".$rDetails->id_no." \n";
                    $response .= "County :".$rDetails->county." \n";
                    $response .= "Constituency :".$rDetails->constituency." \n";
                    $response .= "Ward :".$rDetails->ward." \n";
                }
            }
            if($firstTwoCharacters == "3*")
            {
                $idNo = $this->splitter($string);
                $rDetails = $this->Cosmere_Mpesa_model->getREgistrationDetails($idNo[1]);
                if($rDetails ==false)
                {
                    $response = "END The ID Number Is Not Registerd, Dial *569# to register \n";
                }
                else
                {
                    $response = "END Your Registration Details \n";
                    $response .= "Names :".$rDetails->names." \n";
                    $response .= "Date Of Birth :".$rDetails->dob." \n";
                    $response .= "Email :".$rDetails->email." \n";
                    $response .= "ID Number :".$rDetails->id_no." \n";
                    $response .= "County :".$rDetails->county." \n";
                    $response .= "Constituency :".$rDetails->constituency." \n";
                    $response .= "Ward :".$rDetails->ward." \n";
                }
            }
            if(empty($string) && $status == 0)
            {
            
                if($status == 0 ){
                    //create the phone number
                    $this->Cosmere_Mpesa_model->addNewClientUssdUda($phone);
                    //display the registration menu
                    $response  = "CON Welcome To United Democratic Alliance USSD Service\n";
                    $response .= "1. Register \n";
                    $response .= "2. Check Registration Status \n";
                    $response .= "3. Check IEBC Registration Status \n";
                }
            }
            //scenario three
            else
            {
                if($string == "1")
                {
                    if($status == 0 ){
                        //create the phone number
                        //display the registration menu
                        $this->Cosmere_Mpesa_model->updateUserUda('completed','1',$phone);
                        $response = "CON Enter Your ID Number \n";
                    }
                }
                
                
                    if($status === '1' ){
                        //update names column where phone is phone
                        //check for registration Details
                        $firstTwoCharacters = $firstTwoCharacters = substr($string, 0, 2);
                        if(empty($string))
                        {
                            $response = "CON Enter Your Full Names \n";
                        }
                        else
                        {
                        // $s = $Details->phone."*".$string;
                            $names = $this->splitter($string);
                            $c = count($names);
                            if($c == 1)
                            {
                                $n = $names[0];
                            }
                            if($c == 3)
                            {
                                if($firstTwoCharacters == "1*"){
                                    //update names column and status
                                    $names = $this->splitter($string);
                                    $this->Cosmere_Mpesa_model->updateUserUda('names',$names[2],$phone);
                                    $this->Cosmere_Mpesa_model->updateUserUda('completed','2',$phone);
                                    $response = "CON Enter your Date Of Birth \n";
                                }
                            }
                            else
                            {
                                $n = $names[1];
                            }
                            if(empty($n))
                            {
    
                            }
                            else
                            {
                                $rDetails = $this->Cosmere_Mpesa_model->getREgistrationDetails($n);
                                if($rDetails ==false)
                                {
                                    $response = "CON The ID Number Is Not In our database Proceed To Register \n";
                                    $this->Cosmere_Mpesa_model->updateUserUda('id_no',$n,$phone);
                                    $this->Cosmere_Mpesa_model->updateUserUda('completed','1',$phone);
                                    $response.= "CON Enter your Full Names \n";
                                }
                                else
                                {
                                    $this->Cosmere_Mpesa_model->updateUserUda('names',$n,$phone);
                                    $this->Cosmere_Mpesa_model->updateUserUda('completed','2',$phone);
                                    $response = "CON Enter your Date Of Birth \n";
                                }
                            }
                            
                        }
                    }
                    if($status === '2' ){
                        if(empty($string))
                        {
                            $response = "CON Enter your Date Of Birth \n";
                        }
                        else
                        {
                            //update names column where phone is phone
                            $dob = $this->splitter($string);
                            if(count($dob) == 1)
                            {
                                $n = $dob[0];
                            }
                            else
                            {
                                $n = $dob[3];
                            }
                            $this->Cosmere_Mpesa_model->updateUserUda('dob',$n,$phone);
                            $this->Cosmere_Mpesa_model->updateUserUda('completed','3',$phone);
                            $response = "CON Enter your Email Address \n";
                        }
                    }
                    if($status === '3' ){
                        if(empty($string))
                        {
                            $response = "CON Enter your Email Address \n";
                        }
                        else
                        {
                            //update names column where phone is phone
                            $email = $this->splitter($string);
                            if(count($email)  == 1)
                            {
                                $n = $email[0];
                            }
                            else
                            {
                                $n = $email[4];
                            }
                            $this->Cosmere_Mpesa_model->updateUserUda('email',$n,$phone);
                            $this->Cosmere_Mpesa_model->updateUserUda('completed','4',$phone);
                            $response = "CON Enter Your County Of Residence \n";
                        }
                    }
                    if($status === '4' ){
                        if(empty($string))
                        {
                            $response = "CON Enter Your County Of Residence \n";
                        }
                        else
                        {
                            //update names column where phone is phone
                            $email = $this->splitter($string);
                            if(count($email) == 1)
                            {
                                $n = $email[0];
                            }
                            else
                            {
                                $n = $email[5];
                            }
                            $this->Cosmere_Mpesa_model->updateUserUda('county',$n,$phone);
                            $this->Cosmere_Mpesa_model->updateUserUda('completed','5',$phone);
                            $response = "CON Enter Constituency of Residence \n";
                        }
                    }
                    if($status === '5' ){
                        if(empty($string))
                        {
                            $response = "CON Enter Constituency of Residence \n";
                        }
                        else
                        {
                            //update names column where phone is phone
                            $email = $this->splitter($string);
                            if(count($email) == 1)
                            {
                                $n = $email[0];
                            }
                            else
                            {
                                $n = $email[6];
                            }
                            $this->Cosmere_Mpesa_model->updateUserUda('constituency',$n,$phone);
                            $this->Cosmere_Mpesa_model->updateUserUda('completed','6',$phone);
                            $response = "CON Ward of Residence \n";
                        }
                    }
                    if($status === '6' ){
                        //update names column where phone is phone
                        $email = $this->splitter($string);
                        if(count($email) == 1)
                        {
                            $n = $email[0];
                        }
                        else
                        {
                            $n = $email[7];
                        }
                        $this->Cosmere_Mpesa_model->updateUserUda('ward',$n,$phone);
                        $this->Cosmere_Mpesa_model->updateUserUda('completed','8',$phone);
                        $response = "CON Your Registration Is Complete, Please See Bellow Your Details \n";
                        $response .= "Full Names :".$email[2]." \n";
                        $response .= "Date Of Birth :".$email[3]." \n";
                        $response .= "Email Address :".$email[4]." \n";
                        $response .= "ID Number :".$email[1]." \n";
                        $response .= "County Of Residence :".$email[5]." \n";
                        $response .= "Constituency :".$email[6]." \n";
                        $response .= "Ward :".$email[7]." \n";
                        $response .= "1.Confirm \n";
                        $response .= "2.Cancel \n";
                    }
                    if($status === '7' ){
                        //update names column where phone is phone
                        $email = $this->splitter($string);
                        if(count($email) == 1)
                        {
                            $n = $email[0];
                        }
                        else
                        {
                            $n = $email[7];
                        }
                        $this->Cosmere_Mpesa_model->updateUserUda('ward',$n,$phone);
                        $this->Cosmere_Mpesa_model->updateUserUda('completed','8',$phone);
                        $response = "CON Your Registration Is Complete, Please See Bellow Your Details \n";
                        $response .= "Full Names :".$email[1]." \n";
                        $response .= "Date Of Birth :".$email[2]." \n";
                        $response .= "Email Address :".$email[3]." \n";
                        $response .= "ID Number :".$email[4]." \n";
                        $response .= "County Of Residence :".$email[5]." \n";
                        $response .= "Constituency :".$email[6]." \n";
                        $response .= "Ward :".$email[7]." \n";
                        $response .= "1.Confirm \n";
                        $response .= "2.Cancel \n";
                    }
                    if($status === '8')
                    {
                        if(empty($string)){
                            $this->Cosmere_Mpesa_model->updateUserUda('completed','9',$phone);
                            $response = "END You have already registerd for this service dial *569# to check your status \n";
                        }
                        else
                        {
                            $l = $this->splitter($string);
                            $tnc = $l[8];
                            if($tnc == "1")
                            {
                                $this->Cosmere_Mpesa_model->updateUserUda('completed','9',$phone);
                                $response = "END Thank You for your confirmation \n";
                            }
                            if($tnc == "2")
                            {
                                $this->Cosmere_Mpesa_model->removeUserUda($phone);
                                $response = "END Thank You, You Can Register Afresh by dialing *569# \n";
                            }
                        }
    
                    }
                    if($status=== '9'){
                        $names = $this->splitter($string);
                        $c = count($names);
                        if($c == 2)
                        {
                            $firstTwoCharacters = $firstTwoCharacters = substr($string, 0, 2);
                            if($firstTwoCharacters == "1*"){
                                //update names column and status
                                $names = $this->splitter($string);
                                $rDetails = $this->Cosmere_Mpesa_model->getREgistrationDetails($names[1]);
                                if($rDetails ==false)
                                {
                                    $response = "END The ID Number Is Not Registerd, Dial *569# to register \n";
                                }
                                else
                                {
                                    $response = "END Your Registration Details \n";
                                    $response .= "Names :".$rDetails->names." \n";
                                    $response .= "Date Of Birth :".$rDetails->dob." \n";
                                    $response .= "Email :".$rDetails->email." \n";
                                    $response .= "ID Number :".$rDetails->id_no." \n";
                                    $response .= "County :".$rDetails->county." \n";
                                    $response .= "Constituency :".$rDetails->constituency." \n";
                                    $response .= "Ward :".$rDetails->ward." \n";
                                }
                            }
                        }
                    }
                    
            }
            header('Content-type: text/plain');
            echo $response;
        }
        public function authenticateUssdNcr($phone,$string){
            $status = $this->Cosmere_Mpesa_model->getCountUssdPhone($phone);
            if($status == "6"){
                return true;
            }
            
            if($status == 0 ){
                //create the phone number
                $this->Cosmere_Mpesa_model->addNewClientUssd($phone);
                //display the registration menu
                $this->Cosmere_Mpesa_model->updateUser('completed','1',$phone);
                $response  = "CON Welcome To Nairobi Commuter Rail, Please follow the prompts to register \n";
                $response .= "1. Enter Your Full Names \n";
            }
            if($status === '1' ){
                //update names column where phone is phone
                $names = $this->splitter($string);
                $this->Cosmere_Mpesa_model->updateUser('names',$names[0],$phone);
                $this->Cosmere_Mpesa_model->updateUser('completed','2',$phone);
                $response = "CON Enter your Date Of Birth \n";
            }
            if($status === '2' ){
                //update names column where phone is phone
                $dob = $this->splitter($string);
                $this->Cosmere_Mpesa_model->updateUser('dob',$dob[1],$phone);
                $this->Cosmere_Mpesa_model->updateUser('completed','3',$phone);
                $response = "CON Enter your Email Address \n";
            }
            if($status === '3' ){
                //update names column where phone is phone
                $email = $this->splitter($string);
                $this->Cosmere_Mpesa_model->updateUser('email',$email[2],$phone);
                $this->Cosmere_Mpesa_model->updateUser('completed','4',$phone);
                $response = "CON Enter Your ID Number \n";
            }
            if($status === '4' ){
                //update names column where phone is phone
                $email = $this->splitter($string);
                $this->Cosmere_Mpesa_model->updateUser('id_no',$email[3],$phone);
                $this->Cosmere_Mpesa_model->updateUser('completed','5',$phone);
                $response = "CON Enter Your Nationality \n";
            }
            if($status === '5' ){
                //update names column where phone is phone
                $email = $this->splitter($string);
                $this->Cosmere_Mpesa_model->updateUser('nationality',$email[4],$phone);
                $this->Cosmere_Mpesa_model->updateUser('completed','6',$phone);
                $response = "END Thank you for Registering Dial *569# to book your ticket.";
            }
            // else
            // {
            //     return true;
            // }
            header('Content-type: text/plain');
            echo $response;
        }
        public function ussdNcr(){
             // Reads the variables sent via POST from our gateway
             $sessionId   = $_POST["sessionId"];
             $serviceCode = $_POST["serviceCode"];
             $phone = $_POST["phoneNumber"];
             $phoneNumber = $str = ltrim($phone, '+');
             $text        = $_POST["text"];
            //  $this->authenticateUssdNcr($phoneNumber,$text);
             $string = 'Session ID : '.$sessionId.' Service Code : '.$serviceCode.' Phone Number : '.$phoneNumber.' Text : '.$text.PHP_EOL;
             $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/NCRUSSD.txt", "a") or die("Unable to open file!");
             fwrite($myfile, $string);
             fclose($myfile);
            //  $this->udaRegistrationUssd($phoneNumber,$text);exit();
            //  $this->udaRegistrationUssd($phoneNumber,$text);exit();
             $auth = $this->authenticateUssdNcr($phoneNumber,$text);
             if($auth == true){
                if ($text == "") {
                    // This is the first request. Note how we start the response with CON
                    $response  = "CON Welcome To Nairobi Commuter Rail, Select Train Type \n";
                    $response .= "1. DMU \n";
                    $response .= "2. Commuter Train \n";
                }
                else if ($text == "1"){
                    $response  = "CON Select Departure Station \n";
                    $response .= "1. Nairobi Central To Syokimau \n";
                    $response .= "2. Syokimau To Nairobi Centrall \n";
                }
                else if ($text == "2"){
                    $response  = "CON Select Route \n";
                    $response .= "1. Syokimau \n";
                    $response .= "2. Embakasi \n";
                    $response .= "3. Ruiru \n";
                    $response .= "4. Kahawa West \n";
                    $response .= "5. Kikuyu \n";
                }
                else if ($text == "1*1"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 6:35 AM \n";
                    $response .= "2. 8:00 AM \n";
                    $response .= "3. 9:35 AM \n";
                    $response .= "4. 12:00 PM \n";
                    $response .= "5. 6:20 PM \n";
                }
                else if ($text == "1*2"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 8:00 AM \n";
                    $response .= "2. 10:15 AM \n";
                    $response .= "3. 12:10 AM \n";
                    $response .= "4. 14:20 PM \n";
                    $response .= "5. 19:15 PM \n";
                }
                else if($text == "2*1"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 17:30 Nairobi Station To Syokimau \n";
                    $response .= "2. 6:20 AM Syokimau To Nairobi Station \n";
                }
                else if($text == "2*2"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 18:00 Nairobi station to Embakasi \n";
                    $response .= "2. 7:00  Embakasi to Nairobi Station\n";
                }
                else if($text == "2*3"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 17:40 Nairobi station to Ruiru \n";
                    $response .= "2. 6:00 Ruiru to Nairobi station \n";
                }
                else if($text == "2*4"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 8:00 Nairobi station to Kahawa west \n";
                    $response .= "2. 9:00 Kahawa west to Nairobi station \n";
                }
                else if($text == "2*5"){
                    $response  = "CON Select Departure Time \n";
                    $response .= "1. 17:35 Nairobi station to Kikuyu station \n";
                    $response .= "2. 10:15 Kikuyu to Nairobi Station \n";
                }
                else
                {
                    $response = "END Thank you, Kindly Confirm the payment on your phone.";
                    $amt = 5;//$this->makeAmount($text);
                    $this->Cosmere_Mpesa_model->pushStk($phoneNumber,$amt);
                }
                header('Content-type: text/plain');
                echo $response;
             }
        }
        public function makeAmount($string){
            switch($string){
                case '2*1*1':
                    return 40;
                break;
                case '2*1*2':
                    return 40;
                break;
                case '2*2*1':
                    return 40;
                break;
                case '2*2*2':
                    return 40;
                break;
                case '2*3*1':
                    return 40;
                break;
                case '2*3*2':
                    return 40;
                break;
                case '2*4*1':
                    return 40;
                break;
                case '2*4*2':
                    return 40;
                break;
                case '2*5*1':
                    return 40;
                break;
                case '2*5*2':
                    return 40;
                break;
                default:
                return 100;
            }
        }
        public function deceisionMaker($string){
            switch($string){
                case "1*1*2":
    
                break;
            }
    
        }
        public function ussdJenga(){
            // $this->ussdNcr();
            // exit();
            // Reads the variables sent via POST from our gateway
            $sessionId   = $_POST["sessionId"];
            $serviceCode = $_POST["serviceCode"];
            $phone = $_POST["phoneNumber"];
            $phoneNumber = $str = ltrim($phone, '+');
            $text        = $_POST["text"];
    
            $string = 'Session ID : '.$sessionId.' Service Code : '.$serviceCode.' Phone Number : '.$phoneNumber.' Text : '.$text.PHP_EOL;
            $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniUSSD.txt", "a") or die("Unable to open file!");
            fwrite($myfile, $string);
            fclose($myfile);
            // $this->udaRegistrationUssd($phoneNumber,$text);exit();
    
            if ($text == "") {
                // This is the first request. Note how we start the response with CON
                $response  = "CON Welcome To Jenga Jirani. Select Amount You Wish To Donate \n";
                $response .= "1. KSHS 100 \n";
                $response .= "2. KSHS 200 \n";
                $response .= "3. KSHS 500 \n";
                $response .= "4. KSHS 1000 \n";
                $response .= "5. Other Amount ";
    
            } else if ($text == "1") {
                // Business logic for first level response
                $response = "END Thank you for your donation of kshs 100, Please check your phone to complete the transaction.";
                $this->Cosmere_Mpesa_model->pushStk($phoneNumber,100);
    
            } else if ($text == "2") {
                // Business logic for first level response
                // This is a terminal request. Note how we start the response with END
                $response = "END Thank you for your donation of kshs 200, Please check your phone to complete the transaction";
                $this->Cosmere_Mpesa_model->pushStk($phoneNumber,200);
    
    
            }
    
            else if ($text == "3") {
                // Business logic for first level response
                // This is a terminal request. Note how we start the response with END
                $response = "END Thank you for your donation of kshs 500, Please check your phone to complete the transaction";
                $this->Cosmere_Mpesa_model->pushStk($phoneNumber,500);
    
    
            }
            else if ($text == "4") {
                // Business logic for first level response
                // This is a terminal request. Note how we start the response with END
                $response = "END Thank you for your donation of kshs 1000, Please check your phone to complete the transaction";
                $this->Cosmere_Mpesa_model->pushStk($phoneNumber,1000);
    
    
            }
            else if ($text == "5") {
                // Business logic for first level response
                // This is a terminal request. Note how we start the response with END
                $response = "CON Enter the amount you wish to donate";
    
            }
            else
            {
                $array_name = explode ('*', $text, 2);
                $amt = $array_name[1];
                if($amt >=5)
                {
                    $response = "END Thank you for your donation of KSHS".$amt." Please check your phone to complete the transaction";
                    $this->Cosmere_Mpesa_model->pushStk($phoneNumber,$amt);
    
                }
                else
                {
                    $response = "END Wrong input, Kindly input amount greater than KSHS 5";
                }
            }
            // else if ($text == "5000") {
            //     // Business logic for first level response
            //     // This is a terminal request. Note how we start the response with END
            //     $response = "CON i see";
    
            // }
            // else if($text == "1*1") { 
            //     // This is a second level response where the user selected 1 in the first instance
            //     $accountNumber  = "ACC1001";
    
            //     // This is a terminal request. Note how we start the response with END
            //     $response = "END Your account number is ".$accountNumber;
    
            // } else if ( $text == "1*2" ) {
            //     // This is a second level response where the user selected 1 in the first instance
            //     $balance  = "KES 10,000";
    
            //     // This is a terminal request. Note how we start the response with END
            //     $response = "END Your balance is ".$balance;
            // }
    
            // Echo the response back to the API
            header('Content-type: text/plain');
            echo $response;
        }
        public function splitter($string){
            return $array_name = explode ('*', $string);
        }
        public function splitterD(){
            $string = 'David Kipkemboi*31/08/1988*davidkla12@gmail.com*27526422*Nairobi*Syokimau*Katani*1';
            $array_name = explode ('*', $string);
            var_dump($array_name);
        }
}