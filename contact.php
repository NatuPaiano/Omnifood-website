<?php
/* You Mail ID */
define('TO_EMAIL', 'natupaiano@gmail.com');
class Mailer{

    private $_params;
    private $_errors;

    public function __construct(){
        $this->_params = $this->LoadParams();
        $this->_errors = array();
    }

    public function run(){
        if($this->Validate()){
            $res = $this->SendEmail();
            if($res === true)
                $this->OnSuccess();
            else
                $this->OnError();
        }else
            $this->OnError();
    }

    private function LoadParams(){
        return $_POST['contact'];
    }

    private function Validate(){
        if(!(isset($this->_params['name']) && ($this->_params['name'] != '')))
            $this->_errors['name'] = 'empty_name';
        if(!(isset($this->_params['email']) && $this->_params['email'] != ''))
            $this->_errors['email'] = 'empty_email';
        else{
            $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
            if(!preg_match($email_exp,$this->_params['email']))
                $this->_errors['email'] = 'invalid';
        }

        if(!(isset($this->_params['message']) && $this->_params['message'] != ''))
            $this->_errors['message'] = 'empty_message';

        return (count($this->_errors) == 0);
    }

    private function SendEmail(){
        $headers =

		/* --From and reply options--- */
        //'From: "' . $this->_params['name'] . '" <' . $this->_params['email'] . ">\r\n" .
        //  'Reply-To: "' . $this->_params['name'] . '" <' . $this->_params['email'] . ">\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $subject	= "Message from the website";
		$message="\n\n  Name: ".$this->_params['name']."\n\n  Email: ".$this->_params['email']."\n\n Subject: Enquiry "."\n\n Message: ".$this->_params['message'];

        $to = TO_EMAIL;
        return mail($to, $subject, $message, $headers);
    }

    private function OnSuccess(){
        echo '{"success": true}';
    }

    private function OnError(){
        $response = '{';
        $response .= '"success": false, "errors": [';

        foreach($this->_errors as $key => $value) {
            $response .= "{ \"field\": \"$key\", \"error\": \"$value\"},";
        }
        if(count($this->_errors) > 0)
            $response = substr($response, 0, -1);
        $response .= ']}';

        echo $response;
    }

}
$mailer = new Mailer();
$mailer->run();
?>
