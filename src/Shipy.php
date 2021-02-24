<?php

/**
 * ShipyAPI - Shipy PHP ödeme APIsi için bir sınıf.
 * Geliştirici: TimberLock
 * Geliştirici Web Sitesi: benegedeniz.com
 */
class Shipy
{
	private $apiKey;
	private $ccPayURL;
	private $eftPayURL;
	private $mbPayPage;
	private $isCallbackStarted = false;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

	public function createPaymentRequest(string $paymentType, array $paymentInfo)
	{
		$validPayments = ["credit_card", "mobile", "eft"];

		if (!in_array($paymentType, $validPayments))
			throw new Exception("Error Processing Request: Payment type must be 'mobile', 'credit_card' or 'eft'.", 1);

		$paymentInfo['apiKey'] = $this->apiKey;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.shipy.dev/pay/" . $paymentType);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentInfo));

		$result = curl_exec($ch);

		curl_close($ch);

		if ($paymentType == "credit_card")
		{
			$result = json_decode($result, true);

			if ($result['status'] == "success")
    			$this->eftPayURL = $result['link'];
    		else
    			print("Shipy Error: " . $result["message"]);
    	}

    	if ($paymentType == "eft")
		{
			$result = json_decode($result, true);

			if ($result['status'] == "success")
    			$this->ccPayURL = $result['link'];
    		else
    			print("Shipy Error: " . $result["message"]);
    	}

    	if ($paymentType == "mobile")
    	{
    		$this->mbPayPage = $result;
    	}
	}

	public function goToPayment()
	{
		if (!empty($this->ccPayURL))
			header("Location: " . $this->ccPayURL);
		else if (!empty($this->eftPayURL))
			header("Location: " . $this->eftPayURL);
		else if (!empty($this->mbPayPage))
			print($this->mbPayPage);
		else
			throw new Exception("Error Processing Request: You must create a payment before going to payment page.", 1);
	}

	public function startCallback()
	{
		if(isset($_SERVER["HTTP_CLIENT_IP"]))
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if (isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
			$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
		else
			$ip = $_SERVER["REMOTE_ADDR"];

		if ($ip != "144.91.111.2")
			throw new Exception("Error Processing Request: Request sent by wrong IP: " . $ip, 1);

		if (!isset($_POST["returnID"]) || !isset($_POST["paymentType"]) || !isset($_POST["paymentAmount"]) || !isset($_POST["paymentHash"]) || !isset($_POST["paymentID"]) || !isset($_POST["paymentCurrency"]))
			throw new Exception("Error Processing Request: Shipy did not send required parameters. Report this to Shipy ASAP!", 1);

		$this->isCallbackStarted = true;
	}

	public function getCallbackInfo()
	{
		if ($this->isCallbackStarted === false)
			throw new Exception("Error Processing Request: Callback isn't started at all!", 1);
			
		$apiKey = $this->apiKey;

		$returnID = $_POST["returnID"];
		$paymentID = $_POST["paymentID"];
		$paymentType = $_POST["paymentType"];
		$paymentAmount = $_POST["paymentAmount"];
		$paymentCurrency = $_POST["paymentCurrency"];
		$paymentHash = $_POST["paymentHash"];

		$hashtr = $paymentID . $returnID . $paymentType . $paymentAmount . $paymentCurrency . $apiKey;
		$hashbytes = mb_convert_encoding($hashtr, "ISO-8859-9");
		$hash = base64_encode(sha1($hashbytes, true));

		if($hash != $paymentHash) 
			throw new Exception("Error Processing Request: paymentHash is not valid.", 1);

		return $_POST;
	}

	public function endCallback()
	{
		if ($this->isCallbackStarted === false)
			throw new Exception("Error Processing Request: Callback isn't started at all!", 1);
		
		echo "OK";
	}
}

?>
