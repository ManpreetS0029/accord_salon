<?php

/*** Important info **/
//First is please urlencode every message
//For new whether use \n with no space or use enter for new space (See below example)
/****First way use simple enter keyboard key */
/*$msg = urlencode("5th Anni. Offer @ACCORD SALON
33% off on 1000 to 10000
Or 33% + 15% above 10000
Offer ends Aug 19th, 2018
Book:0161-4100432 | SCO 107, Phase-1 Dugri, LDH."); */
/****** Secod way  user \n character but then do not use enter key of keyboard text should be continous in one line ****/
//$msg2 = urlencode("Such fin 55\nwell done");
namespace App\CustomClasses;
    
/*class MessageModel
{
    var $message = '';
    var $to = array();
    function MessageModel( $msg, $arrToPhones = array() )
    {
        if( !is_array($arrToPhones) )
        {
            trigger_error('Phone numbers should be in array' );
            return;
        }
        $this->message = $msg;
        $this->to = $arrToPhones;  
    }

    function getArray()
    {
        $arr = array( 'message' => $this->message, 'to' => $this->to );
        return $arr;
    }
}


class MessageContainer
{
    var $messageModel = array();
    function addMessage( MessageModel $messageModel )
{
    $this->messageModel[] = $messageModel;
}
    function clearMessages()
    {
        $this->messageModel = array();
    }

 function getArray()
    {
    $arr = array();
    foreach( $this->messageModel as $item ) {
        $arr[] = $item->getArray();
   }
   return $arr;
    }
} */

class Message_Class
{

    var $curl;
    var $apiKey;
    var $url = "http://api.msg91.com/api/";
    var $type = 1; //route = 1 promotional messages, route = 4 transactional messages
    var $countryCode = '91';
    var $sender = '';

    function __construct ( $apiKey, $sender, $type = 1, $countryCode = 91 )
    {
        if( trim($sender) == '' )
        {
            trigger_error('Sender should not be empty');
            return;
        }
        if( trim($apiKey) == '' )
        {
            trigger_error('Authentication key should not be empty');
            return;
        }

        if( trim($type) == '' )
        {
            trigger_error('Route (Type) should not be empty.');
            return;
        }
        else if( $type != '4' && $type != '1')
        {
            trigger_error('Invalid Route (Type). Should be 1 for promotional or 4 for transactional messages.');
        }

        if( trim($countryCode) == '' )
        {
            trigger_error('Country Code should not be empty.');
            return;
        }

        if( trim($sender) == '' )
        {
            trigger_error('Send should not be empty.');
            return;
        }
        else  if( !$this->isValidSenderID($sender)  )
        {
            trigger_error('Invalid Sender. Sender should be only six characters long.');
            return;
        }

        $this->apiKey =  trim($apiKey);
        $this->type =  trim($type);
        $this->countryCode =  trim($countryCode);
        $this->sender = $sender;
        
    }

    protected function isValidSenderID( $text )
    {
        if( strlen($text) > 6 )
        {
            return false;
        }

        return true;
    }

    protected function getCurlObj()
    {
        if( $this->curl == null  )
        {
            $this->curl = curl_init();
          
        }

        return $this->curl;
    }

    protected function curlExec()
    {
         
        $arr = array();
        $response = curl_exec($this->getCurlObj() );
          $err = curl_error( $this->getCurlObj() );

          if ($err) {
            $arr['error'] = '1';
            $arr['msg'] = $err;
             
          } else {
            $arr['error'] = '0';
            $arr['response'] =  $response;
            
          }

          return $arr;
    }



    public function getBalanaceOfMessages()
    {
        $url = $this->url.'balance.php?type='.$this->type.'&authkey='.$this->apiKey;
        curl_setopt( $this->getCurlObj() ,CURLOPT_URL, $url );

        curl_setopt_array( $this->getCurlObj(), array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
          ));

          
          return $this->curlExec();

    }

    public function getOptOut()
    {
        $url = $this->url.'optout.php?authkey='.$this->apiKey;
        curl_setopt( $this->getCurlObj() ,CURLOPT_URL, $url );

        curl_setopt_array( $this->getCurlObj(), array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));


        return $this->curlExec();

    }

    /**
     * Response:
     * If Valid: Valid
     * if not valid : 207
     */
    public function checkIfValidApiKey($apiKey = '')
    {
        $apiKey = $apiKey == '' ? $this->apiKey : $apiKey;
        $url = $this->url.'validate.php?authkey='.$apiKey;
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
          ));
          
          return $this->curlExec();
    }
    

  /*  public function getDeliveryReport()
    {
        $curl =  getCurlObj() ;
        $url = 
    curl_setopt_array($curl, array(
  CURLOPT_URL => "http://yourdomain.com/dlr/pushUrl.php?date=%24YYMMDDhhmm&number=%24number&desc=%24desc&status=%24status&reqid=%24request%20id",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));
    } */

    public function longCodeBalance()
    {

       $url = $this->url.'longcodeBalance.php?type='.$this->type.'&authkey='.$this->apiKey;
        curl_setopt_array($this->getCurlObj() , array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));
        return $this->curlExec();

    }


    public function sendMsg( $msg, $arrPhones, $sender = '', $route = '', $countryCode = '', $apiKey = ''  )
    {
        if(trim($msg) == '' )
        {
            trigger_error("Message should not be empty. ");
            return;
        }
        if( !is_array($arrPhones ) )
        {
            trigger_error("Phones should be in array format. ");
            return;
        }

        $url = $this->url.'v2/sendsms';

        $apiKey = (trim($apiKey) != '' ?  trim($apiKey) : $this->apiKey);
        $route =  trim($route) != '' ?  trim($route) : $this->type;
        $countryCode =  trim($countryCode) != '' ?  trim($countryCode) : $this->countryCode;
        $sender =  trim($sender) != '' ?  trim($sender) : $this->sender;

        if( !$this->isValidSenderID($sender) )
        {
            trigger_error("Invalid Sender. Should be not more then six characters long");
            return;
        }

        $messageString = "[{ \"message\" : \"".urlencode($msg)."\", \"to\" : ".json_encode($arrPhones)."  }]";

         $postFields = "{ \"sender\": \"".$sender."\", \"route\": \"".$route."\", \"country\": \"".$countryCode."\", \"sms\": ".$messageString."  }";

        //  echo "{ \"sender\": \"".$sender."\", \"route\": \"".$route."\", \"country\": \"".$countryCode."\", \"sms\": [ { \"message\": \"Message1\", \"to\": [ \"98260XXXXX\", \"98261XXXXX\" ] }, { \"message\": \"Message2\", \"to\": [ \"98260XXXXX\", \"98261XXXXX\" ] } ] }";


        curl_setopt_array($this->getCurlObj(), array(
            CURLOPT_URL =>  $url ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>  $postFields ,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "authkey: ".$apiKey,
                "content-type: application/json"
            ),
        ));

        return $this->curlExec();

    }
    protected function sendSMSByPost( $arrMsg, $sender = '', $route = '', $countryCode = '', $apiKey = '' )
    {
        if( !is_array($arrMsg ) )
        {
            trigger_error("Message paramater should be in array format. ");
            return;
        }

        $url = $this->url.'v2/sendsms';
        $apiKey = trim($apiKey) != '' ?  trim($apiKey) : $this->apiKey;
        $route =  trim($route) != '' ?  trim($route) : $this->type;
        $countryCode =  trim($countryCode) != '' ?  trim($countryCode) : $this->countryCode;
        $sender =  trim($sender) != '' ?  trim($sender) : $this->sender;

        if( !$this->isValidSenderID($sender) )
        {
            trigger_error("Invalid Sender. Should be not more then six characters long");
            return;
        }
          $postFields = "{ \"sender\": \"".$sender."\", \"route\": \"".$route."\", \"country\": \"".$countryCode."\", \"sms\": ".json_encode($arrMsg)."  }";
echo "<br />";echo "<br />";
      //  echo "{ \"sender\": \"".$sender."\", \"route\": \"".$route."\", \"country\": \"".$countryCode."\", \"sms\": [ { \"message\": \"Message1\", \"to\": [ \"98260XXXXX\", \"98261XXXXX\" ] }, { \"message\": \"Message2\", \"to\": [ \"98260XXXXX\", \"98261XXXXX\" ] } ] }";
        
          curl_setopt_array($this->getCurlObj(), array(
  CURLOPT_URL =>  $url ,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>  $postFields ,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTPHEADER => array(
    "authkey: ".$apiKey,
    "content-type: application/json"
  ),
));

          return $this->curlExec();
    }

}

/**** Message send example **/
/***** Can add multiple messages to send multiple phones ******/
/*** Can send one message to many phones *****/
/*
$arrMsg = array();
$msg = urlencode("5th Anni. Offer @ACCORD SALON
33% off on 1000 to 10000
Or 33% + 15% above 10000
Offer ends Aug 19th, 2018
Book:0161-4100432 | SCO 107, Phase-1 Dugri, LDH.");

$msg2 = "Such fin 55\nwell done";



$msg91 = new Message_Class( '231519AqsTJvFRuy5b717b05', 'ACCORD', '4' );
$res =  $msg91->sendSMSByPost( $arrMsg ); */







 
