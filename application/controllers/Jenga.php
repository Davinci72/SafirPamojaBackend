<?php 
class Jenga extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Mpesa_model');
        $this->load->model('Cosmere_Mpesa_model');
        $this->load->model('Ussd_Mpesa_model');
        $this->load->model('Clients_model');
        $this->load->model('Ncr_model');
    }
    public function index(){
        echo 'You are not allowed here';
    }
    public function test(){
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
		$data = array(
			'phone'=>$arr['phone'],
            'amount'=>$arr['amount'],
            'acc'=>$arr['acc'],
		);
        // echo json_encode($data);
        $this->callSupa($data);
        //call pos
        echo "{status':'success'}";
	}

    public function callSupa($d){
    //    $data = json_encode($d);
    $data = array(
        'phone'=>"254725597552",
        'amount'=>100,
        'acc'=>'wewew33443',
    );
    $enc = json_encode($data);
       $curl = curl_init();

       curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://kajiadorevenue.info/Jenga/sendPushSupa',
         CURLOPT_SSL_VERIFYPEER=>false,
         CURLOPT_SSL_VERIFYHOST =>false,
         CURLOPT_SSL_VERIFYSTATUS =>false,
         CURLOPT_SSLVERSION =>3,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 1000,
         CURLINFO_HEADER_OUT=>true,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS =>  $enc,
         CURLOPT_HTTPHEADER => array(
           'Content-Type: application/json',
           'Cookie: ci_session=d64ld6764hmiu51ijbff108atqavqmdr'
         ),
       ));
       
       $response = curl_exec($curl);
      if( $response=== false)
{
    echo $headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT );
    echo 'Curl error: ' . curl_error($curl);
}
else
{
    echo 'Operation completed without any errors'.$response;
}

// Close handle
curl_close($curl);
    }
    public function sendPushSupa(){
        echo 'Success';
    }
    public function sendPush () {
        $this->load->model('Sms_model');
        $string = file_get_contents('php://input');
        $arr = $this->returnArr($string);
        echo $response = $this->Mpesa_model->pushStk($arr['phone'],'5');
        // var_dump($arr['phone']);exit();
        // echo $string;
        // $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajirani.txt", "a") or die("Unable to open file!");
        // fwrite($myfile, $string);
        // fclose($myfile);
        //log request
        //log response
        $this->logResponse($response,$arr['phone'],$arr['amount']);
        //log result
    }

    public function activate($phone,$amt){
        $this->load->model('Sms_model');
        $string = file_get_contents('php://input');
        $arr = $this->returnArr($string);
        echo $response = $this->Mpesa_model->pushStk($arr['phone'],$arr['amount']);
        // var_dump($arr['phone']);exit();
        // echo $string;
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajirani.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string);
        fclose($myfile);
        //log request
        //log response
        $this->logResponse($response,$arr['phone'],$arr['amount']);
        //log result
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
        $this->Mpesa_model->sendKfmSMS($name,$phone);
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
        $this->Mpesa_model->cleanData();
    }
    public function notifications(){
        $string = file_get_contents('php://input');
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResultIpn.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string);
        fclose($myfile);
        //get the string
        $this->load->model("Izone_model");
        $this->Izone_model->checkCriteria($string);
        echo "Success";
        //check against a database
        
        //if no result send error meesage. or activate validation api
        //if there is a result, return the message template
        $this->mpesaIpn($string);
        // $this->Mpesa_model->notifications();
    }
    public function validation(){
        $string = file_get_contents('php://input');
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/cosmereValidation.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string);
        fclose($myfile);
        $dataArr = json_decode($string,true);
        $amt = $dataArr['TransAmount'];
        // if($amt == "5.00")
        // {
            header('Content-Type: application/json; charset=utf-8');
            $arr = array(
                "ResultCode"=>0,
                "ResultDesc"=>"Accepted"
        );
            echo json_encode($arr);
        // }
        // else
        // {
        //     header('Content-Type: application/json; charset=utf-8');
        //     $arr =  array(
        //         "ResultCode"=>"C2B00013",
        //         "ResultDesc"=>"Rejected"
        //     );
        //     echo json_encode($arr);
            
        // }
    }
    public function cosIpn(){
        $string = file_get_contents('php://input');
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/jengajiraniResultIpn.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string);
        fclose($myfile);
        $this->mpesaCosIpn($string);
    }
    public function runOnce(){
        $this->Mpesa_model->runOnce();
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
    public function logKfmResp($arr=array(),$response,$phone,$amount){
        $this->Mpesa_model->saveKfm($arr);
    }
    public function logResponse($response,$phone,$amount){
        $arr = json_decode($response,true);
        $reqId = $arr['MerchantRequestID'];
        $checkoutId = $arr['CheckoutRequestID'];
        $respCode = $arr['ResponseCode'];
        $respDesc = $arr['ResponseDescription'];
        $custMsg = $arr['CustomerMessage'];
        $this->Mpesa_model->logResponse($phone,$amount,$reqId,$checkoutId,$respCode,$respDesc,$custMsg);
    }
    public function testerCurl(){
        $ch = curl_init('https://176.58.126.237/ussd?MSISDN=254721632557&SERVICE_CODE=706&SESSION_ID=1514417589&USSD_STRING=*706#');
 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        
        curl_close($ch);
        
        echo $data;

    }
    public function sendTicket($phone){
        $d = array(
            "message"=>"Welcome to the world of possibilities!, thanks for joining supertibe solutions, make life easier by giving affordable services",
            "phone_number"=>"$phone",
            "service_id"=>"6019542000189454",
            "access_code"=>"SuperTribe",
            "unique_identifier"=>rand(100000,10000000)
        );
       $data = json_encode($d);

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
            "authorization: Token fcda95a724186fd159cf5f7f47f6983759c0dc23",
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
            $this->Mpesa_model->logResult($arr['Body']['stkCallback']['ResultDesc'],$arr['Body']['stkCallback']['CheckoutRequestID']);
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
            //send success message with download url link
            $this->sendTicket($phone);
            $this->Mpesa_model->logSuccessResult($MerchantRequestID,$CheckoutRequestID,$ResultCode,$resultDesc,$amount,$transCode,$transDate,$phone);
            $this->Mpesa_model->logResult($arr['Body']['stkCallback']['ResultDesc'],$arr['Body']['stkCallback']['CheckoutRequestID']);
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
	$this->Mpesa_model->sendToKenoobi();
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
        $this->Mpesa_model->saveIpnMpesaIzone($data);
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
    $this->Mpesa_model->saveIpnMpesa($data);
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
        $status = $this->Mpesa_model->getCountUssdPhoneUda($phone);
        
        $Details = $this->Mpesa_model->getREgistrationDetailsSession($phone);
        // if($status == "9"){
        //     $response = "END Thank you, You are already registerd";
        // }
        //scenario one
        if(empty($string) && $status == 9)
        {
            $response  = "CON Welcome To United Democratic Alliance USSD Service \n";
            $response .= "1. Register \n";
            $response .= "2. Check Registration Status \n";
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
            $rDetails = $this->Mpesa_model->getREgistrationDetails($idNo[1]);
            $iebcDetails = $this->Mpesa_model->getREgistrationDetailsIebc($idNo[1]);
            if($rDetails ==false)
            {
                if($iebcDetails == false)
                {
                    $response = "END The ID Number Is Not Registerd, Dial *569# And select option 1 to register \n";
                }
                else
                {
                    $this->Mpesa_model->updateUserUda('completed','21',$phone);
                    $response = "CON Confirm The Details Bellow \n";
                    $response .= "Names : ".$iebcDetails->fname." ".$iebcDetails->mname." ".$iebcDetails->sname.", DOB : ".$iebcDetails->date_of_birth.", County : ".$iebcDetails->county.", Constituency : ".$iebcDetails->constituency.", Ward : ".$iebcDetails->caw." \n";
                    $response .= "1. Confirm \n";
                    $response .= "2. Cancel \n";

                }
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
            $rDetails = $this->Mpesa_model->getREgistrationDetails($idNo[1]);
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
                $this->Mpesa_model->addNewClientUssdUda($phone);
                //display the registration menu
                $response  = "CON Welcome To United Democratic Alliance USSD Service\n";
                $response .= "1. Register \n";
                $response .= "2. Check Registration Status \n";
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
                    $this->Mpesa_model->updateUserUda('completed','1',$phone);
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
                                $this->Mpesa_model->updateUserUda('names',$names[2],$phone);
                                $this->Mpesa_model->updateUserUda('completed','2',$phone);
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
                            $rDetails = $this->Mpesa_model->getREgistrationDetails($n);
                            $iebcDetails = $this->Mpesa_model->getREgistrationDetailsIebc($n);
                            if($rDetails ==false)
                            {
                                if($iebcDetails == false )
                                {
                                    // $response = "CON The ID Number Is Not In our database Proceed To Register \n";
                                    $this->Mpesa_model->updateUserUda('id_no',$n,$phone);
                                    $this->Mpesa_model->updateUserUda('completed','1',$phone);
                                    $response = "CON Enter your Full Names \n";
                                }
                                else
                                {
                                    //update the status to a unique number
                                    $this->Mpesa_model->updateUserUda('completed','21',$phone);
                                    $response = "CON Confirm The Details Bellow \n";
                                    $response .= "Names : ".$iebcDetails->fname." ".$iebcDetails->mname." ".$iebcDetails->sname.", DOB : ".$iebcDetails->date_of_birth.", County : ".$iebcDetails->county.", Constituency : ".$iebcDetails->constituency.", Ward : ".$iebcDetails->caw." \n";
                                    $response .= "1. Confirm \n";
                                    $response .= "2. Cancel \n";
                                }
                            }
                            else
                            {
                                $this->Mpesa_model->updateUserUda('names',$n,$phone);
                                $this->Mpesa_model->updateUserUda('completed','2',$phone);
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
                        $this->Mpesa_model->updateUserUda('dob',$n,$phone);
                        $this->Mpesa_model->updateUserUda('completed','3',$phone);
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
                        $this->Mpesa_model->updateUserUda('email',$n,$phone);
                        $this->Mpesa_model->updateUserUda('completed','4',$phone);
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
                        $this->Mpesa_model->updateUserUda('county',$n,$phone);
                        $this->Mpesa_model->updateUserUda('completed','5',$phone);
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
                        $this->Mpesa_model->updateUserUda('constituency',$n,$phone);
                        $this->Mpesa_model->updateUserUda('completed','6',$phone);
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
                    $this->Mpesa_model->updateUserUda('ward',$n,$phone);
                    $this->Mpesa_model->updateUserUda('completed','8',$phone);
                    $response = "CON Your Registration Is Complete, Please See Bellow Your Details \n";
                    $response .= "Full Names :".$email[2].", Date Of Birth :".$email[3].= ", Email :".$email[4].", ID No :".$email[1].", County :".$email[5].", Constituency :".$email[6].", Ward :".$email[7]." \n";
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
                    $this->Mpesa_model->updateUserUda('ward',$n,$phone);
                    $this->Mpesa_model->updateUserUda('completed','8',$phone);
                    $response = "CON Your Registration Is Complete, Please See Bellow Your Details \n";
                    $response .= "Full Names :".$email[1].", Date Of Birth :".$email[2].", Email Address :".$email[3].", ID Number :".$email[4].", County Of Residence :".$email[5].", Constituency :".$email[6].", Ward :".$email[7]." \n";
                    $response .= "1.Confirm \n";
                    $response .= "2.Cancel \n";
                }
                if($status === '8')
                {
                    if(empty($string)){
                        $this->Mpesa_model->updateUserUda('completed','9',$phone);
                        $response = "END You have already registerd for this service dial *569# to check your status \n";
                    }
                    else
                    {
                        $l = $this->splitter($string);
                        $tnc = $l[8];
                        if($tnc == "1")
                        {
                            $this->Mpesa_model->updateUserUda('completed','9',$phone);
                            $response = "END Thank You for your confirmation \n";
                        }
                        if($tnc == "2")
                        {
                            $this->Mpesa_model->removeUserUda($phone);
                            $response = "END Thank You, You Can Register Afresh by dialing *569# \n";
                        }
                    }

                }
                if($status=== '9')
                {
                    $names = $this->splitter($string);
                    $c = count($names);
                    if($c == 2)
                    {
                        $firstTwoCharacters = $firstTwoCharacters = substr($string, 0, 2);
                        if($firstTwoCharacters == "1*")
                        {
                            //update names column and status
                            $names = $this->splitter($string);
                            $rDetails = $this->Mpesa_model->getREgistrationDetails($names[1]);
                            $iebcDetails = $this->Mpesa_model->getREgistrationDetailsIebc($names[1]);
                            if($rDetails ==false)
                            {
                                if($iebcDetails == false)
                                {
                                    $response = "END The ID Number Is Not Registerd, Dial *569# to register \n";
                                }
                                else
                                {
                                    //update the status to a unique number
                                    $this->Mpesa_model->updateUserUda('completed','21',$phone);
                                    $response = "CON The ID Number Is Not Registerd, Confirm The Details Bellow \n";
                                    $response .= "Names : ".$iebcDetails->fname." ".$iebcDetails->mname." ".$iebcDetails->sname.", DOB : ".$iebcDetails->date_of_birth.", County : ".$iebcDetails->county.", Constituency : ".$iebcDetails->constituency.", Ward : ".$iebcDetails->caw." \n";
                                    $response .= "1. Confirm \n";
                                    $response .= "2. Cancel Here \n";
                                }
                                if($names[3] === "1"){
                                    //save the details into the ussduda table
                                    $this->Mpesa_model->updateUserUda('completed','9',$phone);
                                    $this->Mpesa_model->updateUserUda('names',$iebcDetails->fname." ".$iebcDetails->mname." ".$iebcDetails->sname,$phone);
                                    $this->Mpesa_model->updateUserUda('dob',$iebcDetails->date_of_birth,$phone);
                                    $this->Mpesa_model->updateUserUda('county',$iebcDetails->county,$phone);
                                    $this->Mpesa_model->updateUserUda('constituency',$iebcDetails->constituency,$phone);
                                    $this->Mpesa_model->updateUserUda('id_no',$iebcDetails->id_passport_no,$phone);
                                    $this->Mpesa_model->updateUserUda('ward',$iebcDetails->caw,$phone);
                                    $response = "END Thank you for confirming, Your Registration Is Complete \n";
                                }
                                if($names[3] === "2"){
                                    //delete from ussduda table where phone is equal to that phone number
                                    $this->Mpesa_model->removeUserUda($phone);
                                    $response = "END Thank you for your time, To register dial *569# and select option 1 \n";
                                }
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
                if($status === "21"){
                    //update relevant fields depending if the selection is one or two
                    $names = $this->splitter($string);
                    $iebcDetails = $this->Mpesa_model->getREgistrationDetailsIebc($names[1]);
                    if($names[3] === "1"){
                        //save the details into the ussduda table
                        $this->Mpesa_model->updateUserUda('completed','9',$phone);
                        $this->Mpesa_model->updateUserUda('names',$iebcDetails->fname." ".$iebcDetails->mname." ".$iebcDetails->sname,$phone);
                        $this->Mpesa_model->updateUserUda('dob',$iebcDetails->date_of_birth,$phone);
                        $this->Mpesa_model->updateUserUda('county',$iebcDetails->county,$phone);
                        $this->Mpesa_model->updateUserUda('constituency',$iebcDetails->constituency,$phone);
                        $this->Mpesa_model->updateUserUda('id_no',$iebcDetails->id_passport_no,$phone);
                        $this->Mpesa_model->updateUserUda('ward',$iebcDetails->caw,$phone);
                        $response = "END Thank you for confirming, Your Registration Is Complete \n";
                    }
                    if($names[3] === "2"){
                        //delete from ussduda table where phone is equal to that phone number
                        $this->Mpesa_model->removeUserUda($phone);
                        $response = "END Thank you for your time, To register dial *569# and select option 1 \n";
                    }
                   
                }
                
        }
        header('Content-type: text/plain');
        echo $response;
    }
    public function authenticateUssdNcr($phone,$string){
        $status = $this->Mpesa_model->getCountUssdPhone($phone);
        if($status == "6"){
            return true;
        }
        
        if($status == 0 ){
            //create the phone number
            $this->Mpesa_model->addNewClientUssd($phone);
            //display the registration menu
            $this->Mpesa_model->updateUser('completed','1',$phone);
            $response  = "CON Welcome To Nairobi Commuter Rail, Please follow the prompts to register \n";
            $response .= "1. Enter Your Full Names \n";
        }
        if($status === '1' ){
            //update names column where phone is phone
            $names = $this->splitter($string);
            $this->Mpesa_model->updateUser('names',$names[0],$phone);
            $this->Mpesa_model->updateUser('completed','2',$phone);
            $response = "CON Enter your Date Of Birth \n";
        }
        if($status === '2' ){
            //update names column where phone is phone
            $dob = $this->splitter($string);
            $this->Mpesa_model->updateUser('dob',$dob[1],$phone);
            $this->Mpesa_model->updateUser('completed','3',$phone);
            $response = "CON Enter your Email Address \n";
        }
        if($status === '3' ){
            //update names column where phone is phone
            $email = $this->splitter($string);
            $this->Mpesa_model->updateUser('email',$email[2],$phone);
            $this->Mpesa_model->updateUser('completed','4',$phone);
            $response = "CON Enter Your ID Number \n";
        }
        if($status === '4' ){
            //update names column where phone is phone
            $email = $this->splitter($string);
            $this->Mpesa_model->updateUser('id_no',$email[3],$phone);
            $this->Mpesa_model->updateUser('completed','5',$phone);
            $response = "CON Enter Your Nationality \n";
        }
        if($status === '5' ){
            //update names column where phone is phone
            $email = $this->splitter($string);
            $this->Mpesa_model->updateUser('nationality',$email[4],$phone);
            $this->Mpesa_model->updateUser('completed','6',$phone);
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

        // $this->ussdLoans();exit();
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
           $this->Ncr_model->buildUssd($text,$phone);
         }
    }
    public function getCustomer($phone){
        $cus = $this->Ncr_model->getLocaleByID($phone)[0]->location;
        echo '<pre>';
        var_dump($cus);
    }
    public function getStations(){
        $locales = $this->Ncr_model->getLocations();
        $string = '';
        foreach($locales as $l){
            $string .= $l->id."." .$l->location." \n";
        }
        return $string;
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
        $this->ussdLoans();
        // $this->ussdNcr();
        exit();
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
            $this->Mpesa_model->pushStk($phoneNumber,100);

        } else if ($text == "2") {
            // Business logic for first level response
            // This is a terminal request. Note how we start the response with END
            $response = "END Thank you for your donation of kshs 200, Please check your phone to complete the transaction";
            $this->Mpesa_model->pushStk($phoneNumber,200);


        }

        else if ($text == "3") {
            // Business logic for first level response
            // This is a terminal request. Note how we start the response with END
            $response = "END Thank you for your donation of kshs 500, Please check your phone to complete the transaction";
            $this->Mpesa_model->pushStk($phoneNumber,500);


        }
        else if ($text == "4") {
            // Business logic for first level response
            // This is a terminal request. Note how we start the response with END
            $response = "END Thank you for your donation of kshs 1000, Please check your phone to complete the transaction";
            $this->Mpesa_model->pushStk($phoneNumber,1000);


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
                $this->Mpesa_model->pushStk($phoneNumber,$amt);

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
    public function ussdLoans(){
        $sessionId   = $_POST["sessionId"];
        $serviceCode = $_POST["serviceCode"];
        $phone = $_POST["phoneNumber"];
        $phoneNumber = $str = ltrim($phone, '+');
        $text        = $_POST["text"];

        $string = 'Session ID : '.$sessionId.' Service Code : '.$serviceCode.' Phone Number : '.$phoneNumber.' Text : '.$text.PHP_EOL;
        $myfile = fopen("/usr/share/nginx/html/pos/application/controllers/loans.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $string);
        fclose($myfile);

        if ($text == "") {
            // This is the first request. Note how we start the response with CON
            $response  = "CON Welcome to super tribe solutions.   \n";
            $response .= "1.Lipakopo. \n";
            $response .= "2.Boresha Soko. \n";
            $response .= "3.Safiri Pamoja. \n";
            $response .= "4.Shopiwa. \n";
            $response .= "5.Gottasave. \n";
        }
        if(count($this->splitter($text)) =="1"){
            if ($text == "1") {
                // This is the first request. Note how we start the response with CON
                $response  = "CON Select your loan company. \n";
                
                $response .= "1.Tala. \n";
                $response .= "2.Branch. \n";
                $response .= "3.Zenka. \n";
                $response .= "4.Okash. \n";
                $response .= "5.Opesa. \n";
                $response .= "6.Aspira. \n";
                $response .= "7.Stawika. \n";
                $response .= "8.Berry. \n";
                $response .= "9.Credithela. \n";
                $response .= "10.Timiza. \n";
                $response .= "11.Mshwari. \n";
            }
        }
        if(count($this->splitter($text)) =="2"){
            // This is the first request. Note how we start the response with CON
            $response  = "CON Enter your phone number.   \n";
        }
        if(count($this->splitter($text)) =="3"){
            // This is the first request. Note how we start the response with CON
            $response  = "CON Please pay KSH25 for your loan period to be extended. \n";
            $response .= "1. Yes \n";
            $response .= "2.Cancel \n";
        }
        if(count($this->splitter($text)) =="4"){
            // This is the first request. Note how we start the response with CON
            $arr = $this->splitter($text);
            if($arr[3] == "1"){
                $response  = "END Please Complete M-PESA Transaction by entering your service PIN. \n";
                $this->Ussd_Mpesa_model->pushStk($arr[2],'25','xyzz','http://157.230.164.75:8040/pos/Jenga/transResult');
            }
            if($arr[3] == "2")
            {
                $response  = "END Thank you. \n"; 
            }
        }
        // else{
        //     $response  = "CON Please pay x amount for your loan period to be extended. \n";
        //     $response .= "1. Yes \n";
        //     $response .= "2.Cancel \n";
        // }
        header('Content-type: text/plain');
        echo $response;
    }
    public function splitterD(){
        $string = 'David Kipkemboi*31/08/1988*davidkla12@gmail.com*27526422*Nairobi*Syokimau*Katani*1';
        $array_name = explode ('*', $string);
        var_dump($array_name);
    }
   
}