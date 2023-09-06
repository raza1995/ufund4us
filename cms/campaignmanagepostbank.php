<?
require_once("../configuration/dbconfig.php");
require ('../lib/init.php');
\Stripe\Stripe::setApiKey(STRIPE_API_KEY); //Initialize Stripe Gateway
$REQUEST = &$_REQUEST;
$cid = checkAndReturnOnly($REQUEST, 'cid');
$act = checkAndReturnOnly($REQUEST, 'act');
$accid = checkAndReturnOnly($REQUEST, 'accid');
if ($accid != '' && $act == 1) {
	try {
		$account = \Stripe\Account::retrieve("$accid");
		$accounts = $account->__toArray(true);
		$account_counter = count($accounts['external_accounts']['data']);
		if ($account_counter > 0) {
			$account_name = $accounts['external_accounts']['data'][0]['account_holder_name'];
			$bank_name = $accounts['external_accounts']['data'][0]['bank_name'];
			$bank_routing = $accounts['external_accounts']['data'][0]['routing_number'];
		}
	} catch (Stripe_InvalidRequestError $e) {
		// Invalid parameters were supplied to Stripe's API
		$errortype = 2;
	} catch (Stripe_AuthenticationError $e) {
		// Authentication with Stripe's API failed
		$errortype = 3;
	} catch (Stripe_ApiConnectionError $e) {
		// Network communication with Stripe failed
		$errortype = 4;
	} catch (Stripe_Error $e) {
		// Display a very generic error to the user, and maybe send
		// yourself an email
		$errortype = 5;
	} catch (Exception $e) {
		// Something else happened, completely unrelated to Stripe
		$errortype = 6;
	}
	$result['accountname'] = $account_name;
	$result['bankname'] = $bank_name;
	$result['bankrouting'] = $bank_routing;
	echo json_encode($result);
}
?>