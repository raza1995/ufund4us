<?
require_once("../configuration/dbconfig.php");
$iCityId = $_GET['sid'];
$sCiData = $oregister->getcity($iCityId);
$iCiRecords = count($sCiData);
?>

<select name="fld_city" id="fld_city"  class="form-control colorMeBlue noValue" required>
<option value="">Select city</option>
<?
for($ci=0;$ci<$iCiRecords;$ci++)
{
?>
<option value="<?=$sCiData[$ci]['name']?>" <? if($aUserDetail['fld_city'] == $sCiData[$ci]['name']){?> selected<? }?>><?=$sCiData[$ci]['name']?></option>
<?
}
?>
</select>