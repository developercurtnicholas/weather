var key = "02192f4e90cdcfdf04d2e4c24efb428b";
var kingston = 3489854;
var mobay = 3489460;
var newKingston = 3489297;
var sendUrl = "sendmail.php";
var contactFailed = false;


	$("#kingston").click(function(){
		getWeatherById(kingston,key,true);
	});

	$("#mobay").click(function(){
		getWeatherById(mobay,key,true);
	});

	$("#emails").click(function(){
		postEmails();
	});


function sendingMails(){
	$container = $("#content");
	$("#content").css("background","none");
				$("#content").css("margin-top","20%");
			$("#content").css("height","100%");
	$container.empty();

	divString = "<img id='loading' class='loading' src='res/loading.gif'/>"+
	"<p class='sentText'>Emails Are Being sent out to all"+
	"workers(Maximum wait time 15 mins)</p>"+"<img id='sentImage' src='res/sent.jpg'>"+
	"<p class='sentText'>Please wait a few minutes...</p>";

	$container.append(divString);
}


//Get the weather forecast for the next 5 days
function getWeatherById(ID,KEY,POST_TO_BACK){
	
	var url = "http://api.openweathermap.org/data/2.5/forecast?id="+ID+"&APPID="+key;
	var backend = "loadDynamicWeather.php";
	//Some ajax to get open weather map api information 
	$.ajax({
		type: 'GET',
		url: url,
		success: function(data){

			if(POST_TO_BACK == true ){
				console.log(data);
				$.post(backend,{"JSON":data},function(data){
					$("#content").html(data);
				}).fail(function(xhr, status, error) {

					//Some error handling
        			alert("Could not contact the backend, please try again later");
    			});

			}else{
				post(data);
			}
		} ,
		error : function(XMLHttpRequest, textStatus, errorThrown){

			//handle errors
			alert("We were unable to contact the open weather map api");
		}
	});
}

function post(d){

	$.post(sendUrl,{"JSON" : d},

		function(data){
			console.log(data);

			$container = $("#content");
			$container.empty();
			$("#content").css("background","url('res/done.png') no-repeat center center");
			$("#content").css("margin-top","20%");
			$("#content").css("height","250px");
		}
	).fail(function(xhr, status, error) {

					//Some error handling
        			alert("Could not contact the backend, Please try again later");
        			contactFailed = true;
    });
} 

function postEmails(){

	getWeatherById(mobay,key,false);
	getWeatherById(kingston,key,false);

	if(!contactFailed){
		sendingMails();
	} 
}