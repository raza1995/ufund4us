<?
require_once("../configuration/dbconfig.php");
$iCountryId = $_GET['countryid'];
$sSData = $oregister->getstate2($iCountryId);
$iSRecords = count($sSData);
?>

<option value="">Select state</option>
<?
for($s=0;$s<$iSRecords;$s++)
{
?>
<option value="<?=$sSData[$s]['name']?>"><?=$sSData[$s]['name']?></option>
<?
}
?>