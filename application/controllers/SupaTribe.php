<?php
class SupaTribe extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Supatribe_model');
    }
    public function registerDriver(){
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        $phone = $arr['phone'];
        $names = $arr['fullnames'];
        $idNumber = $arr['id_number'];
        $email = $arr['email'];
        $vtype = $arr['vtype'];
        if($this->Supatribe_model->createUser($phone,$names,$email,$idNumber,$vtype,1)){
            // $this->sendTicket($phone);
            echo '{"status":"success"}';
        }
        else{
            echo '{"status":"fail"}';
        }
    }
    public function registerPassenger(){
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        $phone = $arr['phone'];
        $names = $arr['fullnames'];
        $idNumber = $arr['id_number'];
        $email = $arr['email'];
        if($this->Supatribe_model->createUser($phone,$names,$email,$idNumber,2)){
            // $this->sendTicket($phone);
            echo '{"status":"success"}';
        }
        else{
            echo '{"status":"fail"}';
        }
    }
    public function login(){
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        $uname = $arr['username'];
        $password = $arr['password'];
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->Supatribe_model->Authenticate($uname,$password)));
        // echo json_encode($this->Supatribe_model->Authenticate($uname,$password));
    }
    public function getDesignation($email){
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->Supatribe_model->getDeTails($email)));
    }
    public function getPayment($phone){
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->Supatribe_model->checkPayments($phone)));
    }
    public function createTrip(){
       echo  $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        // var_dump($arr['deviceLatitude']);exit();
        $data = array(
            'deviceLatitude'=> $arr['deviceLatitude'],
            'deviceLongitude'=>$arr['deviceLongitude'],
            'destinationLatitude'=>$arr['destination']['latitude'],
            'destinationName'=>$arr['destinationName'],
            'pickUpPoint'=>$arr['pickUpPoint'],
            'pickUpPointLat'=>$arr['pickUpPointLat'],
            'pickUpPoitLong'=>$arr['pickUpPointLong'],
            'destinationLongititude'=>$arr['destination']['longitude'],
            'travelDistance'=>$arr['travelDistance'],
            'duration'=>$arr['duration'],
            'driver'=>$arr['driver'],
            'seats'=>$arr['seats'],
            'dateOfTravel'=>$arr['dateOfTravel'],
            'amountPerSeat'=>$arr['amountPerSeat'],
            'date_c'=>date("Y-m-d h:i:s")
        );
        $this->Supatribe_model->createTrip($data);
        echo "{'message':'trip created successfully'}";
    }
    public function getTrips(){
        $string = file_get_contents('php://input');
        $arr = json_decode($string,true);
        $destination = $arr['destination'];
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->Supatribe_model->GetTrips($destination)));
    }
    public function getDriver($locale){
        //get driver phone number
        $email = $this->Supatribe_model->getDriver($locale)[0]['driver'];
        $driverDetails = $this->Supatribe_model->getUserDetails($email);
        $tripData = array(
            'trip_details'=>$this->Supatribe_model->getDriver($locale),
            'driver_details'=>$driverDetails
        );
        return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($tripData));  
    }
    public function uploadPhoto(){
        var_dump($_GET);
        // rand(0,1000000);
        // echo $this->input->post('deviceID');
        move_uploaded_file($_FILES['file']['tmp_name'],'uploads/'.$_FILES['file']['name']);
        // var_dump($_FILES['file']['name']);
        echo "{'message':'Photo Uploaded successfully'}";
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

} 