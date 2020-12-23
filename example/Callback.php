<?php

require "../src/shipy.php";

$shipy = new Shipy("API_SECRET_KEY"); // Creating a new instance for Shipy class.

/**
 * startCallback
 *
 * Declares the start of callback file. Checks for missing parameters Shipy sent as well.
 *
 * @return May throw exception when Shipy sent missing parameters.
 */

$shipy->startCallback();

/**
 * getCallbackInfo
 *
 * Getting payment info that Shipy sent.
 *
 * @return An array of info that Shipy sent.
 *
 * Example return:
 * Array ([
 * 	"returnID" => "MYRETURNID",
 * 	"paymentID" => "PAYMENT ID THAT SHIPY SENT.",
 * 	"paymentType" => "PAYMENT TYPE. Can be {credit_card, mobile}",
 * 	"paymentAmount" => "PAID AMOUNT.",
 * 	"paymentCurrency" => "PAYMENT CURRENCY.",
 * 	"paymentHash" => "PAYMENT HASH THAT SHIPY SENT."
 * 	])
 */

$return = $shipy->getCallbackInfo();



// Everything is secure. Now you can do your database actions etc. To get payment information, you can use the $return variable that we have set in line 35.




/**
 * endCallback
 *
 * Declares the end of callback file. If you don't use this method, Shipy will send you requests every 5 minutes.
 *
 * @return This will return "OK".
 */

$shipy->endCallback();

?>