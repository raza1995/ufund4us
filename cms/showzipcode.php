<?
require_once("../configuration/dbconfig.php");
$zipcode = $_GET['zid'];
$json = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$zipcode.'');
$array = json_decode($json, true); //Decoding on JSON Format
$array2 = (array)$array; //Convert Object to Array
$counter = count($array[results][0][address_components]);
if ($counter == 5) {
	$postalcode = $array[results][0][address_components][0][long_name];
	$city = $array[results][0][address_components][1][long_name];
	$state = $array[results][0][address_components][3][long_name];
	$state_short = $array[results][0][address_components][3][short_name];
	$country = $array[results][0][address_components][4][long_name];
	$country_short = $array[results][0][address_components][4][short_name];
} elseif ($counter == 4) {
	$postalcode = $array[results][0][address_components][0][long_name];
	$city = $array[results][0][address_components][1][long_name];
	$state = $array[results][0][address_components][2][long_name];
	$state_short = $array[results][0][address_components][2][short_name];
	$country = $array[results][0][address_components][3][long_name];
	$country_short = $array[results][0][address_components][3][short_name];
}

if ($country == 'United States') {
	$result['postalcode'] = $postalcode;
} else {
	$result['postalcode'] = '00000000';
}
$result['cityid'] = $city;
$result['city'] = $city;
$result['stateid'] = $state;
$result['state'] = $state;
$result['state_short'] = $state;
$result['countryid'] = $country;
$result['country'] = $country;
$result['country_short'] = $country_short;
echo json_encode($result);

?>