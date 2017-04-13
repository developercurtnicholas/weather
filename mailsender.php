
<?php
 class MailSender{



	//SMTP Host
	private $HOST = "ssl://smtp.gmail.com";
	private $USERNAME = "thebossmanja876@gmail.com";
	private $Password = "theboss123";
	private $mail;

	function __construct(){
		$this->mail = new PHPMailer();
	}

	function sendMail(){

		$this->mail->IsSMTP();
		$this->mail->SMTPDebug  = 1;
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = "ssl";
		$this->mail->Host = $this->HOST;
		$this->mail->Port = 465;
		$this->mail->IsHTML(true);
		$this->mail->Username = $this->USERNAME;
		$this->mail->FromName = "The Boss";
		$this->mail->Password = $this->Password;

		if(!$this->mail->Send()){
			echo "Mail has not been sent". $this->mail->ErrorInfo;
		}else{
			echo "Mail sent "."\n";
		}
	}

	function add($email){
		$this->mail->AddAddress($email);
	}

	function setSubject($sub){
		$this->mail->Subject = $sub;
	}
	function setBody($body){
		$this->mail->Body = $body;
	}
}

?>