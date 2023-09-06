<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.sparkpost.com/api/v1/suppression-list");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "Authorization: 3dec808848252dc06072dfdf85b74c8cd04cafbb";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result_bounce = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
$array_bounce = json_decode($result_bounce, true);
print_r($array_bounce );
/*foreach ($array_bounce [results] as $bounce) {
	if ($bounce[source] == 'Bounce Rule') {
		echo $bounce[recipient];
	}
}*/
?>