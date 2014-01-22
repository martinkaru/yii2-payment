<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1" />
        <title>Example payment usage - Nordea Emulaator - pangalink.net</title>
    </head>
    <body>
<?php

// THIS IS AUTO GENERATED SCRIPT
// (c) 2011 - 2014 Kreata OÜ www.pangalink.net

// STEP 1. Setup signing
// =====================

$secret = "iKEG7gdIgQTFXiaOlzXypqziM4S4SgTV";

// STEP 2. Define payment information
// ==================================

$fields = array(
        "SOLOPMT_VERSION" => "0003",
        "SOLOPMT_STAMP"  => "13",
        "SOLOPMT_RCV_ID" => "10507901",
        "SOLOPMT_LANGUAGE" => "4",
        "SOLOPMT_AMOUNT" => "35",
        "SOLOPMT_DATE"   => "EXPRESS",
        "SOLOPMT_RETURN" => "http://ivo-linux/opus/yii2-payment/examples/demoapp/ret.php",
        "SOLOPMT_CANCEL" => "http://ivo-linux/opus/yii2-payment/examples/demoapp/ret.php",
        "SOLOPMT_REJECT" => "http://ivo-linux/opus/yii2-payment/examples/demoapp/ret.php",
        "SOLOPMT_CONFIRM" => "YES",
        "SOLOPMT_KEYVERS" => "0001",
        "SOLOPMT_CUR"    => "EUR",
        "SOLOPMT_MSG"    => "Comment",
);

// STEP 3. Generate data to be signed
// ==================================

$data = $fields["SOLOPMT_VERSION"] . "&" .
        $fields["SOLOPMT_STAMP"] . "&" .
        $fields["SOLOPMT_RCV_ID"] . "&" .
        $fields["SOLOPMT_AMOUNT"] . "&" .
        $fields["REF"] . "&" .
        $fields["SOLOPMT_DATE"] . "&" .
        $fields["SOLOPMT_CUR"] . "&" .
        $secret . "&";

/* $data = "0003&13&10507901&35&&EXPRESS&EUR&iKEG7gdIgQTFXiaOlzXypqziM4S4SgTV&"; */

// STEP 4. Sign the data with MD5 to generate MAC code
// ========================================================

$fields["SOLOPMT_MAC"] = strtoupper(md5($data));

// STEP 5. Generate POST form with payment data that will be sent to the bank
// ==========================================================================
?>
        <form method="post" action="https://pangalink.net/banklink/nordea">

            <!-- include all values as hidden form fields -->
<?php foreach($fields as $key => $val):?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($val); ?>" />
<?php endforeach; ?>

            <!-- when the user clicks "Pay" form data is sent to the bank -->
            <input type="submit" value="Pay" />

        </form>
    </body>
</html>
