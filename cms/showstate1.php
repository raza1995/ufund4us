<?
require_once("../configuration/dbconfig.php");
$iCountryId = $_GET['cid'];
$sSData = $oregister->getstate($iCountryId);
$iSRecords = count($sSData);
?>

<select name="fld_state" id="fld_state"  class="form-control colorMeBlue noValue" required>
<option value="">Select state</option>
<?
for($s=0;$s<$iSRecords;$s++)
{
?>
<option value="<?=$sSData[$s]['name']?>"><?=$sSData[$s]['name']?></option>
<?
}
?>
</select>

<script>
$('#fld_state').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  $iStateId = this.value;
	  $.ajax({url: "showcity1.php?sid="+$iStateId, success: function(result){ //alert(result);
        $("#divCity").html(result);
    }});
});
</script>