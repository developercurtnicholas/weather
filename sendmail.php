
<?php

////DUE TO THE LIMITATION OF NOT HAVING A REAL WEB HOST WITH CPANEL TO RUN A CRON JOB 
/////THIS PHP SCRIPT TO SEND THE MAIL CAN BE RAN EVERY DAY VIA A BATCH SCRIPT RAN BY WINDOWS SCHEDULER

header('Content-type: text/html');
require_once("./mailer/PHPMailer-master/class.phpmailer.php");
require_once("./mailer/PHPMailer-master/class.smtp.php");
require_once("mailsender.php");

//Max time out
ini_set('max_execution_time', 500);

echo "starting....";
$data = $_POST['JSON'];

$list = $data['list'];

print_r($data['city']['name']."\n");

$logic = new SendingLogic();
$logic->emailSchedule($list);






class SendingLogic{

	//All days that emails were already sent for
	private $days = array();
	private $rainyDays = array();
	private $clearDays = array();
	private $sender;

	function __construct(){
		$this->sender = new MailSender();
	}

	function formatDate($date){

		$theDate = date("d.m.Y",$date);
		$mydate = strtotime($theDate."");
		$formatedDate = date('F jS Y', $mydate);
		$dayofWeek = date("l",$mydate);

		$finalResult = $dayofWeek."  ".$formatedDate;

		return $finalResult;
	}

		//Works out how to email each group based on weather
	function emailSchedule($list){


		$weather = $list[0]['weather'][0]['main'];

		//First day of forecast
		$mil = $list[0]['dt'];

		//First Day of forecast
		$date = date('d-m-y',$mil);
		//Total amount of weather information
		$listSize = count($list);

		//Current day
		$cDay = $date; 

		//if it didn't rain on the previous day
		$rained = false;


		for($i = 0; $i < $listSize; $i++){

			$iDay = date('d-m-y',$list[$i]['dt']);

			//Check to see if it's the same day
			if($cDay == $iDay ){

				//Check if it rains on that day
				if($list[$i]['weather'][0]['main'] == "Rain"){

					$rained = true;

				}
			}
			//Different day (Picks up when the day changes)
			if($cDay != $iDay || ($i == $listSize - 1)){

				//IF IT WILL RAIN
				if($rained){
					$rained = false;
					$previous = $this->formatDate($list[$i-1]['dt']);

					array_push($this->rainyDays,$previous);
				}//IF IT WILL NOT RAIN
				else{
					$previous = $this->formatDate($list[$i-1]['dt']);
					array_push($this->clearDays,$previous);
				}

				$cDay = $iDay;

			}

		}

		$body = "";
		$it = "";
		$emps = json_decode(file_get_contents("./db/file/employees.json"));

		//print_r($emps);
		$count = count($emps);
		$data = $_POST['JSON'];

		


		//Construct message body
		foreach ($this->rainyDays as $key => $v) {
			$body .= "4 hours of work on ".$v." Due to rain <br>"; 
			$it .= "Do not go out on the streets as usual on - ".$v."<br>";
		}
		foreach ($this->clearDays as $key => $v) {
			$body .= "Full 8 hours of work ".$v."<br>";
			$it .= "You have the go ahead to go out on - ".$v."<br>";
		}


		//Divide and conquer >:)
		for($i = 0; $i < $count;$i++){

			if($emps[$i]->city == $data['city']['name']){
				$mail = $emps[$i]->email;
				if(!is_null($mail) && $emps[$i]->role != "IT"){
					

					$this->sendEmail($body,$mail);
					
				}else if(!is_null($mail) && $emps[$i]->role == "IT"){
					
					$this->ITGuyEmail($it,$mail);
					
				}
			}
		}
	}

	function sendEmail($bd,$to){
		echo "Sending email to ".$to."\n";
			$sender = new MailSender();
			$sender->add($to);
			$sender->setSubject("Work Schedule For Rest Of Week");
			$sender->setBody("Good day<br><br>Based on the weather forecast, working days are scheduled as follows<br><br>".$bd);
			$sender->sendMail();
	}
	function ITGuyEmail($bd,$to){
		echo "Sending IT GUY  ".$to."\n";
			$sender = new MailSender();
			$sender->add($to);
			$sender->setSubject("IT Personnel");
			$sender->setBody("Good day Based on the weather, follow the following work schedule: <br><br>".$bd);
			$sender->sendMail();
	}
}

?>
