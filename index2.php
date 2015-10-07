<?php
echo 'hello friend!';
ini_set('memory_limit', '-1');
error_reporting(0);
echo "starting...\n";
myFunction('https://api.jivesoftware.com/analytics/v2/export/activity/lastday?count=500');

echo '<br/>done';


function CallAPI($method, $url, $data = false, $header=null)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 0);
   
 //echo $curl;
    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
function getToken(){
$token = CallAPI('POST','https://api.jivesoftware.com/analytics/v1/auth/login?clientId=44miyqhrssdwsel069q43kybk64r10n4.i&clientSecret=mwc0et7xfia4d3jrb7b62ybshsxcbnlt.s');

return array("Authorization: $token");

}


function myFunction($link){
$header = getToken();
$res = CallAPI('GET',$link, false, $header);

$var = json_decode($res);
$path = dirname(__FILE__). "/files/";
$file = $path."Page_".$var->paging->currentPage.".json";
echo $file;

echo $var->paging->currentPage;
file_put_contents($file, json_encode($var->list));
//@todo put logs here $var->paging->next 
file_put_contents('script.log', "link: ". $var->paging->next . " Page: ".$var->paging->currentPage . "\n" , FILE_APPEND);
	if($var->paging->currentPage != $var->paging->totalPage){
		echo $var->paging->next;
		myFunction($var->paging->next);
	}	
}
?>
