<?php
//updated by pkruger@speeduneed.com for virtuemart
/* (c) 2005 Adam G-H, adam@phenaproxima.net
This class is freeware. Feel free to modify it and use it in any way you see fit, however I retain the copyrights
and I would appreciate it if you send changes/bugfixes/ports to me. */

/* System requirements:
-Payflow Pro SDK from VeriSign (get it at http://manager.verisign.com), still in its original
directory structure
-A Linux server -- anybody want to write a Windows port?
-A copy of PHP that is NOT running in safe mode (you need to be able to run the system() and putenv() functions)
*/

/* Class PFPro - a Payflow Pro payment processing class that doesn't require built-in Payflow Pro support!
It does, however, rely on the presence of a properly-packaged pfpro executable, on a *nix system, with safe
mode disabled. Sorry, IIS folks. */

/* VERSION 1.01
This version adds a function called setComments. setComments lets you set the comment fields of the VeriSign
transaction. A Payflow Pro transaction can have up to two comments, so you can call setComments with either
one or two arguments, representing the first and second comments, respectively. */

class PFPro {
    var $cvv_level;
    var $avs_level;
    var $user;
    var $password;
    var $partner;
    var $vendor;
    var $price;
    var $exp_date;
    var $cvv;
    var $card;
    var $address;
    var $zip;
    var $trx_result = array();
    var $comments = array();
    var $error;
    var $host;
    var $path = 'includes/payflowpro/bin';

    function PFPro($request, $host, $passkey)
    {

	$path = $this->path;
        if(!is_dir($path)) die("$path does not exist or is not a directory");
        else chdir($path);

        putenv("LD_LIBRARY_PATH=.:../lib/:\${LD_LIBRARY_PATH:-};");
        putenv("PFPRO_CERT_PATH=../certs/");

    	$this->user = $request['USER'];
    	$this->password = $passkey;
    	$this->partner = $request['PARTNER'];
    	$this->vendor = $request['VENDOR'];
    	$this->price = $request['AMT'];
    	$this->exp_date = $request['EXPDATE'];
    	$this->cvv = $request['CVV2'];
    	$this->card = $request['ACCT'];
    	$this->address = $request['STREET'];
    	$this->zip = $request['ZIP'];
    	$this->comments = $request['COMMENT1'];
	    $this->host = $host;

	if($this->process() == false)
	    $this->error .= "<b>Error:</b>" . $this->getLastMessage();
	else
	{
	/*
	    if(isInternational())
	        $this ->setSecurity("none", "none");
	    else
	        $this->setSecurity("medium", "medium");
	*/
	    if($this->fraudCheck() == false)
	    {
	        //AVS and/or CVV checks failed
	        if($this->void() == false)
	            $this->error .= "<b>Error voiding transaction</b>: " . $this->getLastMessage();
	        else
	            $this->error .= "<b>Declined:</b> AVS or CVV check failed";
	    }
/*	    else
	    {
	        $this->error .= "<b>Transaction Approved, now refunding you because I think we are in test mode</b><br />";
	        if($this->credit() == false)
	           $this->error .= "<b>refund failed<b>: " . $this->getLastMessage();
	        else
	            $this->error .= "Refunded. Spend your money elsewhere...";
	    }
*/
}



    }


    function getLastMessage()
    {
        if($this->trx_result['RESPMSG']) return $this->trx_result['RESPMSG'];
        else return false;
    }

    function getPNREF()
    {
        if($this->trx_result['PNREF']) return $this->trx_result['PNREF'];
        else return false;
    }

    function setSecurityLevel($avs = "medium", $cvv = "medium")
    {
        $this->cvv_level = $cvv;
        $this->avs_level = $avs;
    }

    function setComments($comment_1 = '', $comment_2 = '')
    {
        if($comment_1) $this->comments[0] = $comment_1;
        if($comment_2) $this->comments[1] = $comment_2;
    }

    function run_trx($param_array, $timeout)
    {
        /* THIS FUNCTION HAS YET TO BE DEBUGGED!! */

        if(!is_array($param_array)) die("Parameter list sent to run_trx is not an array");

        $keys = array_keys($param_array);
        $values = array_values($param_array);
        if(sizeof($keys) != sizeof($values)) die("Size error comparing keys and values during run_trx");

        $parms = array();
        for($i=0; $i<sizeof($keys); $i++) {
            $key = $keys[$i];
            $value = $values[$i];
            array_push($parms, "$key=$value");
        }

        $param_list = implode("&", $parms);

        if($this->card == "4111111111111111") $host = "test-payflow.verisign.com";
        else $host = "payflow.verisign.com";
        $return_str = exec("./pfpro $host 443 \"$param_list\" $timeout");
        parse_str($return_str, $this->trx_result);
    }

    function setCustomerInfo($address, $zip)
    {
        $this->address = $address;
        $this->zip = $zip;
    }

    function setPaymentInfo($card, $exp, $cvv, $price)
    {
        $this->card = $card;
        $this->exp_date = $exp;
        $this->cvv = $cvv;
        $this->price = $price;
    }

    function process($timeout = 60)
    {
        $params = array(
            'TRXTYPE' => 'S',
            'TENDER' => 'C',
            'USER' => $this->user,
            'PWD' => $this->password,
            'PARTNER' => $this->partner,
            'VENDOR' => $this->vendor,
            'STREET' => $this->address,
            'ZIP' => $this->zip,
            'AMT' => $this->price,
            'CVV2' => $this->cvv,
            'ACCT' => $this->card,
            'EXPDATE' => $this->exp_date
        );

        //Add comments if present
        if($this->comments[0]) $params['COMMENT1'] = $this->comments[0];
        if($this->comments[1]) $params['COMMENT2'] = $this->comments[1];

        $this->run_trx($params, $timeout);

        if($this->trx_result['RESULT'] != 0) return false;
        else return true;
    }

    function isInternational()
    {
        if($this->trx_result['IAVS'] == "Y") return true;
        else return false;
    }

    function void($pnref, $timeout = 60)
    {
        if(!$pnref) $pnref = $this->trx_result['PNREF'];

        $params = array(
            'TRXTYPE' => 'V',
            'TENDER' => 'C',
            'USER' => $this->user,
            'PWD' => $this->password,
            'PARTNER' => $this->partner,
            'VENDOR' => $this->vendor,
            'ORIGID' => $pnref
        );
        $this->run_trx($params, $timeout);

        if($this->trx_result['RESULT'] != 0) return false;
        else return true;
    }

    function credit($pnref = null, $timeout = 60)
    {
        if(!$pnref) $pnref = $this->trx_result['PNREF'];

        $params = array(
            'TRXTYPE' => 'C',
            'TENDER' => 'C',
            'USER' => $this->user,
            'PWD' => $this->password,
            'PARTNER' => $this->partner,
            'VENDOR' => $this->vendor,
            'ORIGID' => $pnref
        );
        $this->run_trx($params, $timeout);

        if($this->trx_result['RESULT'] != 0) return false;
        else return true;
    }

    function check_cvv()
    {
        if(!$this->trx_result['CVV2MATCH']) return false;
        else {
            if($this->cvv_level == "none")
                return true;
            else if($this->cvv_level == "medium" && $this->trx_result['CVV2MATCH'] == "N")
                return false;
            else if($this->cvv_level == "full" && $this->trx_result['CVV2MATCH'] != "Y")
                return false;
            else
                return true;
        }
    }

    function check_avs()
    {
        if(!$this->trx_result['AVSADDR'] || !$this->trx_result['AVSZIP']) return false;
        else {
            if($this->avs_level == "none")
                return true;
            else if($this->avs_level == "light" && $this->trx_result['AVSADDR'] == "N" && $this->trx_result['AVSZIP'] == "N")
                return false;
            else if($this->avs_level == "medium" && ($this->trx_result['AVSADDR'] == "N" || $this->trx_result['AVSZIP'] == "N"))
                return false;
            else if($this->avs_level == "full" && ($this->trx_result['AVSADDR'] != "Y" || $this->trx_result['AVSZIP'] != "Y"))
                return false;
            else
                return true;
        }
    }

    function fraudCheck()
    {
        if(!$this->trx_result['AVSADDR'] || !$this->trx_result['AVSZIP']) return false;
        else {
            if($this->check_avs() && $this->check_cvv())
                return true;
            else
                return false;
        }
    }
}

?>
