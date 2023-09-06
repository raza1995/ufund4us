<?php

class CreditCardValidationSolution {

    /**
     * The credit card number with all non-numeric characters removed.
     * @var  string
     */
    var $CCVSNumber = '';

    /**
     * The first four digits of the card.
     * @var  string
     */
    var $CCVSNumberLeft = '';

    /**
     * The card's last four digits.
     * @var  string
     */
    var $CCVSNumberRight = '';

    /**
     * The name of the type of card presented.
     *
     * <p>Automatically determined from the first four digits of the
     * card number.</p>
     *
     * @var  string
     */
    var $CCVSType = '';

    /**
     * The card's expiration date.
     *
     * <p>Presented only if the <var>RequireExp</var> parameter is
     * <kbd>Y</kbd> and there are no other problems with the card
     * number, this variable contains the expiration date in
     * <samp>MMYY</samp> format.</p>
     *
     * @var  string
     */
    var $CCVSExpiration = '';

    /**
     * String explaining the first problem detected, if any.
     * @var  string
     */
    var $CCVSError = '';


	/*
     *
     * @param   string   $Number      the number of the credit card to
     *                                  validate.
     * @param   string   $Language    the ISO 639-1 two letter code of
     *                                  the language for error messages.
     * @param   array    $Accepted    credit card types you accept.  If
     *                                  not used in function call, all
     *                                  known cards are accepted.  Set
     *                                  it before calling the function: <br /><kbd>
     *                                  $A = array('Visa', 'JCB');
     *                                  </kbd><br />
     *                                       Known types:        <ul>
     *                                  <li> American Express    </li>
     *                                  <li> Australian BankCard </li>
     *                                  <li> Carte Blanche       </li>
     *                                  <li> Diners Club         </li>
     *                                  <li> Discover/Novus      </li>
     *                                  <li> JCB                 </li>
     *                                  <li> MasterCard          </li>
     *                                  <li> Visa                </li></ul>
     * @param   string   $RequireExp  should the expiration date be
     *                                  checked?  Y or N.
     * @param   integer  $Month       the card's expiration month
     *                                  in M, 0M or MM foramt.
     * @param   integer  $Year        the card's expiration year in YYYY format.
     * @return  boolean  TRUE if everything is fine.  FALSE if problems.

     */
    function validateCreditCard($Number, $Language='en', $Accepted='',
                                  $RequireExp='N', $Month='', $Year='') {

        $this->CCVSNumber      = '';
        $this->CCVSNumberLeft  = '';
        $this->CCVSNumberRight = '';
        $this->CCVSType        = '';
        $this->CCVSExpiration  = '';
        $this->CCVSError       = '';

        /* Import the language preferences. */

        $Path = dirname(__FILE__);
        if ( !file_exists(SITE_ROOT . "includes/ccv.php") )
		{
			/*
			$CCVSErrNumberString = 'Credit Card Number is invalid. Please enter a valid Credit Card Number.';
			$CCVSErrVisa14       = 'Visa usually has 16 or 13 digits, but you entered 14';
			$CCVSErrUnknown      = 'First four digits indicate an invalid or unknown Credit Card type.';
			$CCVSErrAccepted     = 'Programmer improperly used the Accepted argument';
			$CCVSErrNoAccept     = 'We don\'t accept %s cards';
			$CCVSErrShort        = 'Credit Card Number is missing %s digit(s)';
			$CCVSErrLong         = 'Credit Card Number has %s too many digit(s)';
			$CCVSErrChecksum     = 'Credit Card Number failed the checksum test as it is invalid. Please enter a valid Credit Card Number';
			$CCVSErrMonthString  = 'Expiration Month is invalid.';
			$CCVSErrMonthFormat  = 'Expiration Month has invalid format. Please use mm/yyyy format for card expiration.';
			$CCVSErrYearString   = 'Expiration Year is invalid.';
			$CCVSErrYearFormat   = 'Expiration Year has invalid format. Please use mm/yyyy format for card expiration.';
			$CCVSErrExpired      = 'Your Credit Card has expired';
			*/
			
			$CCVSErrNumberString = getMessages('checkoutCCVSErrNumberString');
			$CCVSErrVisa14       = getMessages('checkoutCCVSErrVisa14');
			$CCVSErrUnknown      = getMessages('checkoutCCVSErrUnknown');
			$CCVSErrAccepted     = getMessages('checkoutCCVSErrAccepted');
			$CCVSErrNoAccept     = getMessages('checkoutCCVSErrNoAccept');
			$CCVSErrShort        = getMessages('checkoutCCVSErrShort');
			$CCVSErrLong         = getMessages('checkoutCCVSErrLong');
			$CCVSErrChecksum     = getMessages('checkoutCCVSErrChecksum');
			$CCVSErrMonthString  = getMessages('checkoutCCVSErrMonthString');
			$CCVSErrMonthFormat  = getMessages('checkoutCCVSErrMonthFormat');
			$CCVSErrYearString   = getMessages('checkoutCCVSErrYearString');
			$CCVSErrYearFormat   = getMessages('checkoutCCVSErrYearFormat');
			$CCVSErrExpired      = getMessages('checkoutCCVSErrExpired');
			
        }else{
        	include(SITE_ROOT . "includes/ccv.php");
        }

        /* Catch malformed input. */

        if ( empty($Number) || !is_string($Number) ) {
            $this->CCVSError = $CCVSErrNumberString;
            return FALSE;
        }

        /* Ensure number doesn't overrun. */
        $Number = substr($Number, 0, 30);

        /* Remove non-numeric characters. */
        $this->CCVSNumber = preg_replace('#[^0-9]#', '', $Number);

        /* Set up variables. */

        $this->CCVSNumberLeft  = substr($this->CCVSNumber, 0, 4);
        $this->CCVSNumberRight = substr($this->CCVSNumber, -4);
        $NumberLength          = strlen($this->CCVSNumber);
        $DoChecksum            = 'Y';

        /* Determine the card type and appropriate length. */

        if ( ($this->CCVSNumberLeft >= 3000) && ($this->CCVSNumberLeft <= 3059) ) {
            $this->CCVSType = 'Diners Club';
            $ShouldLength = 14;
        } elseif ( ($this->CCVSNumberLeft >= 3600) && ($this->CCVSNumberLeft <= 3699) ) {
            $this->CCVSType = 'Diners Club';
            $ShouldLength = 14;
        } elseif ( ($this->CCVSNumberLeft >= 3800) && ($this->CCVSNumberLeft <= 3889) ) {
            $this->CCVSType = 'Diners Club';
            $ShouldLength = 14;

        } elseif ( ($this->CCVSNumberLeft >= 3400) && ($this->CCVSNumberLeft <= 3499) ) {
            $this->CCVSType = 'American Express';
            $ShouldLength = 15;
        } elseif ( ($this->CCVSNumberLeft >= 3700) && ($this->CCVSNumberLeft <= 3799) ) {
            $this->CCVSType = 'American Express';
            $ShouldLength = 15;

        } elseif ( ($this->CCVSNumberLeft >= 3088) && ($this->CCVSNumberLeft <= 3094) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;
        } elseif ( ($this->CCVSNumberLeft >= 3096) && ($this->CCVSNumberLeft <= 3102) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;
        } elseif ( ($this->CCVSNumberLeft >= 3112) && ($this->CCVSNumberLeft <= 3120) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;
        } elseif ( ($this->CCVSNumberLeft >= 3158) && ($this->CCVSNumberLeft <= 3159) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;
        } elseif ( ($this->CCVSNumberLeft >= 3337) && ($this->CCVSNumberLeft <= 3349) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;
        } elseif ( ($this->CCVSNumberLeft >= 3528) && ($this->CCVSNumberLeft <= 3589) ) {
            $this->CCVSType = 'JCB';
            $ShouldLength = 16;

        } elseif ( ($this->CCVSNumberLeft >= 3890) && ($this->CCVSNumberLeft <= 3899) ) {
            $this->CCVSType = 'Carte Blanche';
            $ShouldLength = 14;

        } elseif ( ($this->CCVSNumberLeft >= 4000) && ($this->CCVSNumberLeft <= 4999) ) {
            $this->CCVSType = 'Visa';
            if ($NumberLength > 14) {
                $ShouldLength = 16;
            } elseif ($NumberLength < 14) {
                $ShouldLength = 13;
            } else {
                $this->CCVSError = $CCVSErrVisa14;
                return FALSE;
            }

        } elseif ( ($this->CCVSNumberLeft >= 5100) && ($this->CCVSNumberLeft <= 5599) ) {
            $this->CCVSType = 'MasterCard';
            $ShouldLength = 16;

        } elseif ($this->CCVSNumberLeft == 5610) {
            $this->CCVSType = 'Australian BankCard';
            $ShouldLength = 16;

        } elseif ($this->CCVSNumberLeft == 6011) {
            $this->CCVSType = 'Discover/Novus';
            $ShouldLength = 16;

        } else {
            $this->CCVSError = sprintf($CCVSErrUnknown, $this->CCVSNumberLeft);
            return FALSE;
        }


        /* Check acceptance. */

        if ( !empty($Accepted) ) {
            if ( !is_array($Accepted) ) {
                $this->CCVSError = $CCVSErrAccepted;
                return FALSE;
            }
            if ( !in_array($this->CCVSType, $Accepted) ) {
                $this->CCVSError = sprintf($CCVSErrNoAccept, $this->CCVSType);
                return FALSE;
            }
        }


        /* Check length. */

        if ($NumberLength <> $ShouldLength) {
            $Missing = $NumberLength - $ShouldLength;
            if ($Missing < 0) {
                $this->CCVSError = sprintf($CCVSErrShort, abs($Missing));
            } else {
                $this->CCVSError = sprintf($CCVSErrLong, $Missing);
            }
            return FALSE;
        }


        /* Mod10 checksum process... */

        if ($DoChecksum == 'Y') {

            $Checksum = 0;

            /*
             * Add even digits in even length strings
             * or odd digits in odd length strings.
             */
            for ($Location = 1 - ($NumberLength % 2); $Location < $NumberLength; $Location += 2) {
                $Checksum += substr($this->CCVSNumber, $Location, 1);
            }

            /*
             * Analyze odd digits in even length strings
             * or even digits in odd length strings.
             */
            for ($Location = ($NumberLength % 2); $Location < $NumberLength; $Location += 2) {
                $Digit = substr($this->CCVSNumber, $Location, 1) * 2;
                if ($Digit < 10) {
                    $Checksum += $Digit;
                } else {
                    $Checksum += $Digit - 9;
                }
            }

            /* Checksums not divisible by 10 are bad. */

            if ($Checksum % 10 != 0) {
                $this->CCVSError = $CCVSErrChecksum;
                return FALSE;
            }

        }


        /* Expiration date process... */

        if ($RequireExp == 'Y') {
		

            if ( empty($Month) || !is_string($Month) ) {
                $this->CCVSError = $CCVSErrMonthString;
                return FALSE;
            }

            if ( !preg_match('/^(0?[1-9]|1[0-2])$/', $Month) ) {
                $this->CCVSError = $CCVSErrMonthFormat;
                return FALSE;
            }

            if ( empty($Year) || !is_string($Year) ) {
                $this->CCVSError = $CCVSErrYearString;
                return FALSE;
            }
			

            if ( !preg_match('/^[0-9]{2}$/', $Year) ) {
                $this->CCVSError = $CCVSErrYearFormat;
                return FALSE;
            }
				
			

            if ( $Year < date('y') ) {
                $this->CCVSError = $CCVSErrExpired;
                return FALSE;
            } elseif ( $Year == date('y') ) {
                if ( $Month < date('m') ) {
                    $this->CCVSError = $CCVSErrExpired;
                    return FALSE;
                }
            }
			
			
			//$this->CCVSError = $this->CCVSType;
            $this->CCVSExpiration = sprintf('%02d', $Month) . substr($Year, -2);

        }

        return TRUE;

    }
}

?>