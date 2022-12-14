<?php
 Class cybersource{

     function sign($params) {
         return $this->signData($this->buildDataToSign($params), SECRET_KEY);
     }

     function signData($data, $secretKey) {
         return base64_encode(hash_hmac(HMAC_SHA256, $data, $secretKey, true));
     }

     function buildDataToSign($params) {
         $signedFieldNames = explode(",", $params["signed_field_names"]);
         foreach ($signedFieldNames as $field) {
             $dataToSign[] = $field . "=" . $params[$field];
         }

         return implode(",", $dataToSign);
     }

 }