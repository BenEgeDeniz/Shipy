<?php

require "../src/shipy.php";

$shipy = new Shipy("API_SECRET_KEY"); // Creating a new instance for Shipy class.

$paymentInfo = [ // Setting payment info with required details.
    "usrIp" => "1.1.1.1",
    "usrName" => "John Doe",
    "usrAddress" => "Example st. Block 7",
    "usrPhone" => "5555555555",
    "usrEmail" => "john@doe.com",
    "amount" => 10,
    "returnID" => "MYRETURNID",
    "currency" => "TRY",
    "pageLang" => "TR",
    "mailLang" => "TR",
    "installment" => 0
];

/**
 * createPaymentRequest
 *
 * Creating payment request to Shipy.
 *
 * @param $paymentType Paymnet type. Can be set as {credit_card, mobile, eft}
 * @param $paymentInfo Payment information for Shipy to process.
 *
 * @return void
 */

$shipy->createPaymentRequest("credit_card", $paymentInfo);

/**
 * goToPayment
 *
 * Redirecting client to payment page.
 *
 * @return void
 */

$shipy->goToPayment();

?>
