<?php

$data = $_POST['JSON'];
$list = $data['list'];
$count = count($list);


//final content to return
$content = "";

//Title with city name
$title = "<h3 id='title'>".$data['city']['name']."'s Weather Forecast<br><br>";

$content .= $title;

$card = "";

$condition = "";

//Loop through json data and build result to return
for($i = 0; $i < $count; $i++){

	$condition = $list[$i]['weather'][0]['main'];
	$date = $list[$i]['dt'];
	$temp = $list[$i]['main']['temp'] - 273.15;

	if($condition == "Rain"){


		$card = cardBuilder(formatDate($date),$condition,$temp,"rainy.png");
		$content.= $card;
	}

	if($condition == "few clouds" || $condition == "Clouds"){

		$card = cardBuilder(formatDate($date),$condition,$temp,"few.png");
		$content.= $card;		
	} 

	if($condition == "scatterd clouds"){

		$card = cardBuilder(formatDate($date),$condition,$temp,"cloudy.png");
		$content.= $card;
	}

	if($condition == "Clear"){

		$card = cardBuilder(formatDate($date),$condition,$temp,"sunny.png");
		$content.= $card;
	}
}

//Builds card structure
function cardBuilder($date,$description,$temperature,$pic){

	return "<div class='card'>".
					"<p class='day'>".$date."</p>".
					"<div class='imgholder'>".
						"<img src='res/".$pic."'>".
					"</div>".
					"<p>".$description."</p>".
					"<p>".$temperature."&deg"."</p>". 
			"</div>";
}

function formatDate($date){

	$theDate = date("d.m.Y",$date);
	$mydate = strtotime($theDate."");
	$formatedDate = date('F jS Y', $mydate);
	$dayofWeek = date("l",$mydate);

	$formatedTime = formatTime($date);

	$finalResult = $formatedDate."<br><br>".$dayofWeek."  ".$formatedTime;

	return $finalResult;
}
function formatTime($time){

	return date('h:i:s a',$time);
}
echo $content;

?>