<?php
$curl = curl_init();
 
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://pi-test.sagepay.com/api/v1/merchant-session-keys",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '{ "vendorName": "sandbox" }',
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic aEpZeHN3N0hMYmo0MGNCOHVkRVM4Q0RSRkxodUo4RzU0TzZyRHBVWHZFNmhZRHJyaWE6bzJpSFNyRnliWU1acG1XT1FNdWhzWFA1MlY0ZkJ0cHVTRHNocktEU1dzQlkxT2lONmh3ZDlLYjEyejRqNVVzNXU=",
    "Cache-Control: no-cache",
    "Content-Type: application/json"
  ),
));
 
$response = curl_exec($curl);

$err = curl_error($curl);
 
curl_close($curl);


echo "Step 1:";
$json = json_decode($response, true);
print_r($json);
$merchantSessionKey = $json['merchantSessionKey'];


$curl = curl_init();
 
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://pi-test.sagepay.com/api/v1/card-identifiers/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '{
  "cardDetails": {
    "cardholderName": "Card Holder",
    "cardNumber": "4929000000006",
    "expiryDate": "1120",
    "securityCode": "123"
  }
}',
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$json['merchantSessionKey'],
    "Cache-Control: no-cache",
    "Content-Type: application/json"
  ),
));
 
$response = curl_exec($curl);

$err = curl_error($curl);
 
curl_close($curl);



$json = json_decode($response, true);
echo "<br/><br/>";
echo "Step 2:";
print_r($json);
$cardIdentifier = $json['cardIdentifier'];


if($cardIdentifier!="" && $merchantSessionKey!=""){
  $curl = curl_init();
 
  //$merchantSessionKey = "7A5F7C74-9739-426D-B839-7764395CE6FD";
  //$cardIdentifier = "FC6FE40C-B447-4CE7-8432-DACD40628ACA";
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://pi-test.sagepay.com/api/v1/transactions",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '{' .
                          '"transactionType": "Payment",' .
                          '"paymentMethod": {' .
                          '    "card": {' .
                          '        "merchantSessionKey": "'.$merchantSessionKey.'",' .
                          '        "cardIdentifier": "'.$cardIdentifier.'"' .
                          '    }' .
                          '},' .
                          '"vendorTxCode": "demotransaction' . time() . '",' .
                          '"amount": 10000,' .
                          '"currency": "GBP",' .
                          '"description": "Demo transaction",' .
                          '"apply3DSecure": "UseMSPSetting",' .
                          '"customerFirstName": "Sam",' .
                          '"customerLastName": "Jones",' .
                          '"billingAddress": {' .
                          '    "address1": "407 St. John Street",' .
                          '    "city": "London",' .
                          '    "postalCode": "EC1V 4AB",' .
                          '    "country": "GB"' .
                          '},' .
                          '"entryMethod": "Ecommerce"' .
                        '}',
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic aEpZeHN3N0hMYmo0MGNCOHVkRVM4Q0RSRkxodUo4RzU0TzZyRHBVWHZFNmhZRHJyaWE6bzJpSFNyRnliWU1acG1XT1FNdWhzWFA1MlY0ZkJ0cHVTRHNocktEU1dzQlkxT2lONmh3ZDlLYjEyejRqNVVzNXU=",
    "Cache-Control: no-cache",
    "Content-Type: application/json"
  ),
));
 
$response = curl_exec($curl);
$err = curl_error($curl);
 
curl_close($curl);

$json = json_decode($response, true);
echo "<br/><br/> Step 3:";
print_r($json);
}

?>




<!DOCTYPE html>
<html>
<body>
  <form method="post" action="">
    <label>Name:</label><div><input data-card-details="cardholder-name" name="cardholder-name" value="test"></div>
    <label>Card:</label><div><input data-card-details="card-number" name="card-number" value="4929000000006"></div>
    <label>Expiry:</label><div><input data-card-details="expiry-date" name="expiry-date" value="0121"></div>
    <label>CVV:</label><div><input data-card-details="security-code" name="security-code" value="123"></div>
 
    <input type="hidden" name="card-identifier">
    <div><input type="submit"></div>
  </form>
  <script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>
  <script>
    document.querySelector('[type=submit]')
            .addEventListener('click', function(e) {
                e.preventDefault(); // to prevent form submission
                sagepayOwnForm({ merchantSessionKey: "<?php echo $merchantSessionKey; ?>" })
                  .tokeniseCardDetails({
                      cardDetails: {
                         cardholderName: document.querySelector('[data-card-details="cardholder-name"]').value,
                         cardNumber: document.querySelector('[data-card-details="card-number"]').value,
                         expiryDate: document.querySelector('[data-card-details="expiry-date"]').value,
                         securityCode: document.querySelector('[data-card-details="security-code"]').value
                      },
                      onTokenised : function(result) {
                        if (result.success) {
                          document.querySelector('[name="card-identifier"]').value = result.cardIdentifier;
                          document.querySelector('form').submit();
                        } else {
                          alert(JSON.stringify(result));
                        }
                      }
                  });
            }, false);
  </script>
</body>