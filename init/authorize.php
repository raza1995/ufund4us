<?php

//$conf['Billing']['AuthorizeNet']['TransKey'] = AUTHORIZE_TRANSACTION_KEY;
//$conf['Billing']['AuthorizeNet']['Login'] = AUTHORIZE_LOGIN;
//$conf['Billing']['AuthorizeNet']['Password'] = AUTHORIZE_PASSWORD;

class AuthorizeNet_Billing
{
	var $delim_char;
	var $delim_data;
	var $encaps_char;
	
	var $debug;
	var $version;
	var $test_mode;
	var $login;
	var $password;
	var $trans_key;
	var $trans_type;
	var $transaction_id;
	var $desc;
	var $cc_num;
	var $exp_date;
	var $ccecheck;
	var $amount;
	var $ccv_num;
	var $email_customer;
	var $customer_email;
	var $merchant_email;
	var $email_header;
	var $email_footer;
        
	var $bank_aba_code;
	var $bank_acct_num;
	var $bank_acct_type;
	var $bank_name;
	var $bank_acct_name;
	var $echeck_type = 'WEB';
	var $recurring_billing = 'FALSE';

	var $billing_first_name;
	var $billing_last_name;
	var $billing_company;
	var $billing_address;
	var $billing_city;
	var $billing_state;
	var $billing_zip;
	var $billing_country;
	var $billing_phone;
	var $billing_fax;
	
	var $shipping_first_name;
	var $shipping_last_name;
	var $shipping_company;
	var $shipping_address;
	var $shipping_city;
	var $shipping_state;
	var $shipping_zip;
	var $shipping_country;
	
	var $cust_id;
	var $cust_tax_id;
	var $invoice;
	var $description;
	var $customer_ip;
	
	var $authNetURL;
	var $UserAgent;
	
	/* *****************************
	* AuthorizeNet  Constructor *
	***************************** */
	
	function AuthorizeNet_Billing ($debugset=NULL) {
	        $this->test_mode = "FALSE";
	        $this->debug = '';
	        $this->version = "3.1";
	        $this->delim_char = "|";
	        $this->delim_data = "TRUE";
	        $this->encaps_char = "";
	        if(TEST_MODE != '1'){
	        	$this->authNetURL = "https://secure.authorize.net/gateway/transact.dll";
	        }else{
	        	$this->authNetURL = "https://test.authorize.net/gateway/transact.dll";
	        }
	        $this->UserAgent = "X-AuthorizeNetPHpClass";
	
	}
	
	/* *****************************
	* AuthorizeNet    Test Mode *
	***************************** */
	
	function SetTestMode ($test_mode, $error_code=NULL) {
	        /* Valid Test Modes:
	        VISATEST
	        DISCOVERTEST
	        AMEXTEST
	        MASTERCARDTEST
	        ERRORTEST
	        */
	
	        if ($test_mode == "ERRORTEST") {
	                $this->cc_num = "4222222222222";
	
	                $currenttime = date("Y-m-d", time());
	                list($cyear, $cmonth, $cday) = sscanf($currenttime, "%04s-%02s-%02s");
	                $expyear = $cyear+1;
	                $this->exp_date = "$cmonth-$expyear";
	                $this->amount = "$error_code";
	                $test_mode = "TRUE";
	        }
	
	        if ($test_mode == "VISATEST") {
	                $this->cc_num = "4007000000027";
	
	                $currenttime = date("Y-m-d", time());
	                list($cyear, $cmonth, $cday) = sscanf($currenttime, "%04s-%02s-%02s");
	                $expyear = $cyear+1;
	                $this->exp_date = "$cmonth-$expyear";
	                $this->amount = "2.22";
	                $test_mode = "TRUE";
	        }
	
	        if ($test_mode == "DISCOVERTEST") {
	                $this->cc_num = "6011000000000012";
	
	                $currenttime = date("Y-m-d", time());
	                list($cyear, $cmonth, $cday) = sscanf($currenttime, "%04s-%02s-%02s");
	                $expyear = $cyear+1;
	                $this->exp_date = "$cmonth-$expyear";
	                $this->amount = "2.22";
	                $test_mode = "TRUE";
	        }
	
	        if ($test_mode == "AMEXTEST") {
	                $this->cc_num = "370000000000002";
	
	                $currenttime = date("Y-m-d", time());
	                list($cyear, $cmonth, $cday) = sscanf($currenttime, "%04s-%02s-%02s");
	                $expyear = $cyear+1;
	                $this->exp_date = "$cmonth-$expyear";
	                $this->amount = "2.22";
	                $test_mode = "TRUE";
	        }
	
	        if ($test_mode == "MASTERCARDTEST") {
	                $this->cc_num = "5424000000000015";
	
	                $currenttime = date("Y-m-d", time());
	                list($cyear, $cmonth, $cday) = sscanf($currenttime, "%04s-%02s-%02s");
	                $expyear = $cyear+1;
	                $this->exp_date = "$cmonth-$expyear";
	                $this->amount = "2.22";
	                $test_mode = "TRUE";
	        }
	
	        $this->test_mode = $test_mode;
	        if ($this->debug)
	        if ($test_mode) {
	                echo "TestMode: True\n";
	        } else {
	                echo "TestMode: False\n";
	        }
	        return $test_mode;
	}
	
	/* *****************************
	* AuthorizeNet   Debug Mode *
	***************************** */
	
	function SetDebug ($debug_mode) {
	        $this->debug = $debug_mode;
	        if ($debug_mode) echo "Debug set to True\n";
	        return $debug_mode;
	}
	
	/* *****************************
	* AuthorizeNet  Credentials *
	***************************** */
	
	function SetCredentials ($login_var, $pass_var, $transkey_var) {
	        $this->login = $login_var;
	        $this->password = $pass_var;
	        $this->trans_key = $transkey_var;
	        if ($this->debug) echo "Credentials Set = Login ID: $login_var; Password: $pass_var; Transaction Key: $transkey_var\n";
	
	        if (($login_var == "") || ($pass_var == "") || ($transkey_var == "")) {
	                if ($this->debug) echo "Credentials Failure!\n";
	                return FALSE;
	        } else {
	                if ($this->debug) echo "Credentials Success!\n";
	                return TRUE;
	        }
	}
	
	/* **********************************
	* AuthorizeNet  Transaction Type *
	********************************** */
	
	function SetTransactionType ($type_var, $transid_var=NULL) {
	        /* Valid Types are:
	        AUTH_CAPTURE  # Authorize and Charge the Credit Card (Default)
	        AUTH_ONLY     # Authorize but DOES NOT charge the CC
	        CAPTURE_ONLY  # Charges a CC that has already been Authorized by other means (auth number required)
	        CREDIT        # Credit/Refund money to customers CC
	        VOID	      # Cancels a charge/credit that has not yet been settled
	        PRIOR_AUTH_CAPTURE # Charges a CC that has already been Authorized by AUTH_ONLY
	
	        */
	
	        if ($type_var == NULL) $type_var = "AUTH_CAPTURE";
	
	        if ($this->debug) echo "Transaction Type = $type_var\n";
	
	        if (($type_var == "CAPTURE_ONLY") || ($type_var == "CREDIT") || ($type_var == "VOID") || ($type_var == "PRIOR_AUTH_CAPTURE") || ($type_var == "AUTH_ONLY") || ($type_var == "AUTH_CAPTURE")) {
	                // Set Transaction Type
	                $this->trans_type = $type_var;
	                if (($type_var == "CAPTURE_ONLY") || ($type_var == "CREDIT") || ($type_var == "VOID") || ($type_var == "PRIOR_AUTH_CAPTURE"))
	                if ($transid_var != NULL) {
	                        // Set Transaction ID
	                        $this->transaction_id = $transid_var;
	                        if ($this->debug) echo "Transaction ID Found!\n";
	                        return TRUE;
	                } else {
	                        if ($this->debug) echo "Missing Transaction ID!\n";
	                        return FALSE;
	                }
	        } else {
	                if ($this->debug) echo "Unexpected Variable: $type_var\n";
	                return FALSE;
	        }
	}
	
	/* *****************************
	* AuthorizeNet Credit/Check *
	***************************** */
	
	function SetMethodType ($type_var) {
	        /* Valid Types:
	        CC      # Credit Card (Default)
	        ECHECK  # Electronic Check
	        */
	
	        if ($type_var == "")  $type_var = "CC";
	
	        if ($this->debug) echo "Method Type (Credit Card or Echeck): $type_var\n";
	
	        if (($type_var == "CC") || ($type_var == "ECHECK")) {
	                $this->ccecheck = $type_var;
	                if ($this->debug) echo "Method Type: $type_var\n";
	                return TRUE;
	        } else {
	                if ($this->debug) echo "Error! Method Type: $type_var\n";
	                return FALSE;
	        }
	}
	
	/* *****************************
	* AuthorizeNet       Amount *
	***************************** */
	
	function SetAmount ($amount_var) {
	
	        if (($this->test_mode == "TRUE") && ($this->amount != "")) return $this->amount;
	
	        if ($this->debug) echo "Amount: $amount_var\n";
	
	        if ($amount_var == "") {
	                if ($this->debug) echo "No Amount Set!\n";
	                return FALSE;
	        } else {
	                $amount_var = str_replace("$", '', $amount_var);
	                $this->amount = $amount_var;
	                if ($this->debug) echo "Amount Set: $amount_var\n";
	                return TRUE;
	        }
	}
	
	/* *****************************
	* AuthorizeNet  Description *
	***************************** */
	
	function SetTransactionDescription ($desc_var) {
	
	        if ($this->debug) echo "Transaction Description: $desc_var\n";
	
	        $this->desc = $desc_var;
	        return TRUE;
	}
	
	/* *****************************
	* AuthorizeNet    CC Number *
	***************************** */
	
	function SetCCNumber ($ccnum_var) {
	
	        if (($this->test_mode == "TRUE") && ($this->cc_num != "")) return $this->cc_num;
	
	        if ($this->debug) echo "Original CC Number: $ccnum_var\n";
	
	        $ccnum_var = str_replace(' ', '', $ccnum_var);
	        $ccnum_var = str_replace("-", '', $ccnum_var);
	        $ccnum_var = str_replace("/", '', $ccnum_var);
	
	        if ($this->debug) echo "Modified CC Number: $ccnum_var\n";
	
	        $this->cc_num = $ccnum_var;
	        return $ccnum_var;
	}
	
	/* *****************************
	* AuthorizeNet     Exp Date *
	***************************** */
	
	function SetExpDate ($exp_var) {
	        /* Valid Date Formats:
	        MMYY
	        MM/YY
	        MM-YY
	        MMYYYY
	        MM/YYYY
	        MM-YYYY
	        YYYY-MM-DD
	        YYYY/MM/DD
	        */
	
	        if (($this->test_mode == "TRUE") && ($this->exp_date != "")) return $this->exp_date;
	
	        if ($this->debug) echo "Expiration Date: $exp_var\n";
	
	        $this->exp_date = $exp_var;
	        return $exp_var;
	}
	
	/* ************************************
	* AuthorizeNet Process Transaction *
	************************************ */
	
	function ProcessTransaction () {	
	
	        $post_array = array(
	        "x_Test_Request" => "$this->test_mode",
	        "x_Login" => "$this->login",
	        "x_Tran_Key" => "$this->trans_key",
	        "x_Version" => "$this->version",
	        "x_Type" => "$this->trans_type",
	        "x_Method" => "$this->ccecheck",
	        "x_Delim_Char" => "$this->delim_char",
	        "x_Delim_Data" => "$this->delim_data",
	        "x_Encap_Char" => "$this->encaps_char",
	        "x_Amount" => "$this->amount",
	        "x_Description" => "$this->desc",
	        "x_Card_Num" => "$this->cc_num",
	        "x_Exp_Date" => "$this->exp_date",
	        "x_first_name" => "$this->billing_first_name",
	        "x_last_name" => "$this->billing_last_name",
	        "x_address" => "$this->billing_address",
	        "x_company" => "$this->billing_company",
	        "x_city" => "$this->billing_city",
	        "x_state" => "$this->billing_state",
	        "x_zip" => "$this->billing_zip",
	        "x_country" => "$this->billing_country",
	        "x_phone" => "$this->billing_phone",
	        "x_fax" => "$this->billing_fax",
	        "x_ship_to_first_name" =>   "$this->shipping_first_name",
	        "x_ship_to_last_name" => "$this->shipping_last_name",
	        "x_ship_to_address" => "$this->shipping_address",
	        "x_ship_to_company" => "$this->shipping_company",
	        "x_ship_to_city" => "$this->shipping_city",
	        "x_ship_to_state" => "$this->shipping_state",
	        "x_ship_to_zip" => "$this->shipping_zip",
	        "x_ship_to_country" => "$this->shipping_country",
	        "x_customer_ip" => "$this->customer_ip",
	        "x_cust_id" => "$this->cust_id",
	        "x_email" => "$this->customer_email",
	        "x_email_customer" => "$this->email_customer",
	        "x_merchant_email" => "$this->merchant_email",
	        "x_invoice_num" => "$this->invoice",
	        "x_description" => "$this->description",
			"x_duplicate_window" => "0",
	        );
	
	        if (($this->trans_type == "CAPTURE_ONLY") || ($this->trans_type == "CREDIT") || ($this->trans_type == "VOID") || ($this->trans_type == "PRIOR_AUTH_CAPTURE"))
	        $post_array["x_Trans_ID"] = "$this->transaction_id";
	
	
	        if ($this->debug) {
	                echo "Post Array -\n";
	                print_r($post_array);
	        }
	
	        $data = "";
	
	        reset($post_array);
	        while (list ($key, $val) = each($post_array)) {
	                $data .= $key . "=" . urlencode($val) . "&";
	        }
	        $data = preg_replace("/&$/", '', $data); //Strip the trailing '&'
	
	        if ($this->debug) echo "URL: $data\n";
	
	        /* Initialize CURL */
	        $AuthNetConn = curl_init();
	
	        /* Set CURL Options */
	        /* enable for godaddy hosting
	        curl_setopt($AuthNetConn, CURLOPT_HTTPPROXYTUNNEL, true); 
			curl_setopt($AuthNetConn, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
			curl_setopt($AuthNetConn, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
			*/
	        curl_setopt($AuthNetConn, CURLOPT_URL, $this->authNetURL);
	        curl_setopt($AuthNetConn, CURLOPT_USERAGENT, $this->UserAgent);
			curl_setopt($AuthNetConn, CURLOPT_SSLVERSION,6);
	        curl_setopt($AuthNetConn, CURLOPT_POST, 1);
	        curl_setopt($AuthNetConn, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($AuthNetConn, CURLOPT_RETURNTRANSFER, 1);
	        
	
	        /* Execute CURL and return values */
	        $return_string = curl_exec($AuthNetConn);
	
	        /* Close connection to Secure Server */
	        curl_close($AuthNetConn);
	
	        return $return_string;
	}
	
	/* *****************************
	* AuthorizeNet Get Response *
	***************************** */
	
	function GetResponseReason ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) print_r($response_code);
	
	        return $response_code[3];
	
	}
	
	/* **********************************
	* AuthorizeNet Approval Response *
	********************************** */
	
	function ApprovalResponse ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) print_r($response_code);
	
	        if ($response_code[0] == 1) {
	                return "APPROVED";
	        }
	
	        if ($response_code[0] == 2) {
	                return "DECLINED";
	        }
	
	        if ($response_code[0] == 3) {
	                return "ERROR";
	        }
	}
	
	/* **********************************
	* AuthorizeNet    Transaction ID *
	********************************** */
	
	function GetTransactionID ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) echo "$response_code[6]";
	
	        $this->transaction_id = $response_code[6];
	
	        return $response_code[6];
	
	}
	
	/* **********************************
	* AuthorizeNet     Approval Code *
	********************************** */
	
	function GetApprovalCode ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) echo "Approval Code: $response_code[4]";
	
	        return $response_code[4];
	
	}
	
	/* **********************************
	* AuthorizeNet    Email Customer *
	********************************** */
	
	function EmailCustomer ($enabled, $email=NULL, $merchant_email=NULL) {
	
	        if ($this->debug) echo "Email Customer: $enabled - $email\n";
	
	        $this->email_customer = $enabled;
	        $this->customer_email = $email;
	        $this->merchant_email = $merchant_email;
	
	        return $email;
	}
	
	/* ************************************
	* AuthorizeNet Customer IP Address *
	************************************ */
	
	function SetCustomerIP ($ip) {
	
	        if ($this->debug) echo "Customer IP Address: $ip\n";
	
	        $this->customer_ip = $ip;
	
	        return $ip;
	}
	
	/* **********************************
	* AuthorizeNet   Email Head/Foot *
	********************************** */
	
	function EmailHeaderFooter ($header, $footer) {
	
	        if ($this->debug) echo "Email Header: $header\n";
	        if ($this->debug) echo "Email Footer: $footer\n";
	
	        $this->email_header = $header;
	        $this->email_footer = $footer;
	
	}
	
	/* **********************************
	* AuthorizeNet  Customer Billing *
	********************************** */
	
	
	function CustomerBilling ($first_name, $last_name, $address=NULL, $city=NULL, $state=NULL, $zip=NULL, $phone=NULL, $fax=NULL, $country=NULL, $company, $cust_id=NULL, $cust_tax_id=NULL, $invoice, $description) {
	
	        if ($this->debug) echo "Set Billing Info\n\n\n";
	
	        $this->billing_first_name = $first_name;
	        $this->billing_last_name = $last_name;
	        $this->billing_address = $address;
	        $this->billing_company = $company;
	        $this->billing_city = $city;
	        $this->billing_state = $state;
	        $this->billing_zip = $zip;
	        $this->billing_phone = $phone;
	        $this->billing_fax = $fax;
	        $this->billing_country = $country;
	        $this->cust_id = $cust_id;
	        $this->cust_tax_id = $cust_tax_id;
	        $this->invoice = $invoice;
	        $this->description = $description;
	
	
	}
	
	/* **********************************
	* AuthorizeNet Customer Shipping *
	********************************** */
	
	function CustomerShipping ($first_name, $last_name, $address=NULL, $city=NULL, $state=NULL, $zip=NULL, $country=NULL, $company) {
	
	        if ($this->debug) echo "Set Billing Info\n";
	
	        $this->shipping_first_name = $first_name;
	        $this->shipping_last_name = $last_name;
	        $this->shipping_address = $address;
	        $this->shipping_company = $company;
	        $this->shipping_city = $city;
	        $this->shipping_state = $state;
	        $this->shipping_zip = $zip;
	        $this->shipping_country = $country;
	
	}
	
	/* **********************************
	* AuthorizeNet Customer Shipping *
	********************************** */
	
	function CopyBillingToShipping () {
	
	        if ($this->debug) echo "Copy Billing To Shipping\n";
	
	        $this->billing_first_name = $this->shipping_first_name;
	        $this->billing_last_name = $this->shipping_last_name;
	        $this->billing_address = $this->shipping_address;
	        $this->billing_company = $this->shipping_company;
	        $this->billing_city = $this->shipping_city;
	        $this->billing_state = $this->shipping_state;
	        $this->billing_zip = $this->shipping_zip;
	        $this->billing_country = $this->shipping_country;
	
	}
	
	/* **********************************
	* AuthorizeNet      AVS Response *
	********************************** */
	
	function GetAVSResponse ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) echo "AVS Response: $response_code[5]";
	
	        return $response_code[5];
	
	}
	
	/* ***********************************
	* AuthorizeNet Card Code Response *
	*********************************** */
	
	function GetCardCodeResponse ($response) {
	
	        if ($this->debug) echo "Response: $response\n";
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) echo "CVS Response: $response_code[38]";
	
	        return $response_code[38];
	
	}
	
	/* **********************************
	* AuthorizeNet  AVS Response Text*
	********************************** */
	
	function GetAVSResponseText ($avs_code) {
	
	        if ($this->debug) echo "AVS Code: $avs_code\n";
	
	        switch($avs_code) {
	                case "A":
	                return "Address (Street) Matches, ZIP does not.";
	                break;
	                case "B":
	                return "Address information not provided for AVS check.";
	                break;
	                case "E":
	                return "AVS Error.";
	                break;
	                case "G":
	                return "Non-US Card Issuing Bank.";
	                break;
	                case "N":
	                return "No Match on Address (Street) or ZIP.";
	                break;
	                case "P":
	                return "AVS not applicable for this transaction.";
	                break;
	                case "R":
	                return "Retry - System unavailable or timed out.";
	                break;
	                case "S":
	                return "Service not supported by issuer.";
	                break;
	                case "U":
	                return "Address information is unavailable.";
	                break;
	                case "W":
	                return "9 digit ZIP Matches, Address (Street) does not.";
	                break;
	                case "X":
	                return "Address (Street) and 9 digit ZIP match.";
	                break;
	                case "Y":
	                return "Address (Street) and 5 digit ZIP match.";
	                break;
	                case "Z":
	                return "5 digit ZIP matches, Address (Street) does not.";
	                break;
	
	        }
	}
	
	/* **********************************
	* AuthorizeNet    Set CVS Number *
	********************************** */
	
	function SetCardCode ($cvs_num) {
	
	        if ($this->debug) echo "Card Code Number: $cvs_num\n";
	
	        $this->ccv_num = $cvs_num;
	
	        return $cvs_num;
	
	}
	
	/* **********************************
	* AuthorizeNet  CVS Response Text*
	********************************** */
	
	function GetCardCodeResponseText ($cvs_code) {
	
	        if ($this->debug) echo "CVS Code: $cvs_code\n";
	
	        switch($cvs_code) {
	                case "M":
	                return "Match";
	                break;
	                case "N":
	                return "No Match";
	                break;
	                case "P":
	                return "Not Processed";
	                break;
	                case "S":
	                return "Should Have Been Present";
	                break;
	                case "U":
	                return "Issuer Unable To Process Request";
	                break;
	        }
	}
	
	/* **********************************
	* AuthorizeNet Generate MD5 Hash *
	********************************** */
	
	function GenerateMD5Hash ($hash_value) {
	
	        if ($this->debug) echo "Hash Value: $hash_value\n";
	
	        $whole_hash_value = $hash_value . $this->login . $this->transaction_id . $this->amount;
	
	        $hash_str = strtoupper(md5($whole_hash_value));
	
	        return $hash_str;
	
	}
	
	/* **********************************
	* AuthorizeNet Validate MD5 Hash *
	********************************** */
	
	function ValidateMD5Hash ($hash_value, $response) {
	
	        $response_code = explode($this->delim_char, $response);
	
	        if ($this->debug) echo "Hash Value: $hash_value\n";
	        if ($this->debug) echo "Recieved Hash Value: $response_code[37]\n";
	        if ($this->debug) print_r($response_code);
	
	        if ($hash_value == $response_code[37]) {
	                return TRUE;
	        } else {
	                return FALSE;
	        }
	}

	/* SET ECHECK FUNCTION */
	public function setECheck($bank_aba_code, $bank_acct_num, $bank_acct_type, $bank_name, $bank_acct_name){
		$this->bank_aba_code = $bank_aba_code;
		$this->bank_acct_num = $bank_acct_num;
		$this->bank_acct_type = $bank_acct_type;
		$this->bank_name = $bank_name;
		$this->bank_acct_name = $bank_acct_type;
	}
	
	/* ************************************
	* AuthorizeNet Process Echeck *
	************************************ */
	
	function ProcessEcheckTransaction () {	
	
		$post_array = array(
		"x_Test_Request" => "$this->test_mode",
		"x_Login" => "$this->login",
		"x_Tran_Key" => "$this->trans_key",
		"x_Version" => "$this->version",
		"x_Type" => "$this->trans_type",
		"x_Method" => "$this->ccecheck",
		"x_Delim_Char" => "$this->delim_char",
		"x_Delim_Data" => "$this->delim_data",
		"x_Encap_Char" => "$this->encaps_char",
		"x_Amount" => "$this->amount",
		"x_recurring_billing" => "FALSE",		
		"x_Description" => "$this->desc",
		"x_bank_aba_code" => $this->bank_aba_code,
		"x_bank_acct_num" => $this->bank_acct_num,
		"x_bank_acct_type" => $this->bank_acct_type,
		"x_bank_name" => $this->bank_name,
		"x_bank_acct_name" => $this->bank_acct_type,
		"x_echeck_type" => $this->echeck_type,
		"x_first_name" => "$this->billing_first_name",
		"x_last_name" => "$this->billing_last_name",
		"x_address" => "$this->billing_address",
		"x_company" => "$this->billing_company",
		"x_city" => "$this->billing_city",
		"x_state" => "$this->billing_state",
		"x_zip" => "$this->billing_zip",
		"x_country" => "$this->billing_country",
		"x_phone" => "$this->billing_phone",
		"x_fax" => "$this->billing_fax",
		"x_ship_to_first_name" =>   "$this->shipping_first_name",
		"x_ship_to_last_name" => "$this->shipping_last_name",
		"x_ship_to_address" => "$this->shipping_address",
		"x_ship_to_company" => "$this->shipping_company",
		"x_ship_to_city" => "$this->shipping_city",
		"x_ship_to_state" => "$this->shipping_state",
		"x_ship_to_zip" => "$this->shipping_zip",
		"x_ship_to_country" => "$this->shipping_country",
		"x_customer_ip" => "$this->customer_ip",
		"x_cust_id" => "$this->cust_id",
		"x_email" => "$this->customer_email",
		"x_email_customer" => "$this->email_customer",
		"x_merchant_email" => "$this->merchant_email",
		"x_invoice_num" => "$this->invoice",
		"x_description" => "$this->description",
		"x_duplicate_window" => "0",
		);
	
		if (($this->trans_type == "CAPTURE_ONLY") || ($this->trans_type == "CREDIT") || ($this->trans_type == "VOID") || ($this->trans_type == "PRIOR_AUTH_CAPTURE"))
		$post_array["x_Trans_ID"] = "$this->transaction_id";
		
		
		if ($this->debug) {
		        echo "Post Array -\n";
		        print_r($post_array);
		}
		
		$data = "";
		
		reset($post_array);
		while (list ($key, $val) = each($post_array)) {
		        $data .= $key . "=" . urlencode($val) . "&";
		}
		$data = preg_replace("/&$/", '', $data); //Strip the trailing '&'
		
		if ($this->debug) echo "URL: $data\n";
		
		/* Initialize CURL */
		$AuthNetConn = curl_init();
		
		/* Set CURL Options */
		/* enable for godaddy hosting
		curl_setopt($AuthNetConn, CURLOPT_HTTPPROXYTUNNEL, true); 
		curl_setopt($AuthNetConn, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
		curl_setopt($AuthNetConn, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
		*/
		curl_setopt($AuthNetConn, CURLOPT_URL, $this->authNetURL);
		curl_setopt($AuthNetConn, CURLOPT_USERAGENT, $this->UserAgent);
		curl_setopt($AuthNetConn, CURLOPT_SSLVERSION,6);
		curl_setopt($AuthNetConn, CURLOPT_POST, 1);
		curl_setopt($AuthNetConn, CURLOPT_POSTFIELDS, $data);
		curl_setopt($AuthNetConn, CURLOPT_RETURNTRANSFER, 1);
		
		
		/* Execute CURL and return values */
		$return_string = curl_exec($AuthNetConn);
		
		/* Close connection to Secure Server */
		curl_close($AuthNetConn);
		
		return $return_string;
	}

}

?>

