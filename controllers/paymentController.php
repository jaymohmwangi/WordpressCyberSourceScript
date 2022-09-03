<?php
include_once 'config/constants.php';
include_once 'lib/cybersource.php';
include_once 'model/paymentModel.php';
class paymentController{
    public function initiate($get){
        //store the payment
        $model=(new paymentModel());
        $payment=$model->selectOne($get->invoice);
      if(empty($payment)):
        $model->insert([
            "first_name"=>$get->first_name,
            "last_name"=>$get->last_name,
            "invoice_id"=>$get->invoice,
            "business"=>$get->business,
            "amount"=>$get->amount,
            "return_url"=>$get->return_url,
            "cancel_return"=>$get->cancel_return,
            "currency_code"=>$get->currency_code,
            "notify_url"=>$get->notify_url,
            "request"=>json_encode($get),
        ]);
      endif;
        $payment=(object)$payment;
     if(property_exists($payment,'status') && $payment->status==="PENDING"):
            $model->update([
                "first_name"=>$get->first_name,
                "last_name"=>$get->last_name,
                "business"=>$get->business,
                "amount"=>$get->amount,
                "return_url"=>$get->return_url,
                "cancel_return"=>$get->cancel_return,
                "currency_code"=>$get->currency_code,
                "notify_url"=>$get->notify_url,
                "request"=>json_encode($get),
            ],$payment->id);
        endif;
        if(property_exists($payment,'status') && $payment->status==="PAID"):
            header('Location:'.$payment->cancel_return);
        endif;
       //build form post data
        $params['access_key'] = ACCESS_KEY;
        $params['profile_id'] = PROFILE_ID;
        $params['transaction_uuid'] = uniqid();
        $params['signed_date_time'] = gmdate('Y-m-d\TH:i:s\Z');
        $params['signed_field_names'] = "device_fingerprint_id,profile_id,access_key,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,payment_method,transaction_type,auth_trans_ref_no,currency,merchant_descriptor,override_custom_receipt_page,amount,reference_number";
        $params['unsigned_field_names'] = 'bill_to_forename,bill_to_surname,bill_to_email,bill_to_phone,customer_ip_address,bill_to_address_country,bill_to_address_line1,bill_to_address_city';
        $params['payment_method'] = "card";
        $params['transaction_type'] = "sale";
        $params['auth_trans_ref_no'] = $get->invoice;
        $params['currency'] = $get->currency_code;
        $params['locale'] = "en-us";
        $params['merchant_descriptor'] = "PokeaPay";
        $params['bill_to_address_country'] = $get->country;
        $params['bill_to_address_line1'] = $get->address1;
        $params['bill_to_address_line2'] = $get->address2;
        $params['bill_to_address_city'] = $get->city;
        $params['bill_to_address_state'] = $get->state;
        $params['bill_to_address_postal_code'] = $get->zip;
        $params['override_custom_receipt_page'] =  "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."/ipn.php";
        $params['device_fingerprint_id'] = session_id();
        $params['customer_ip_address'] = @$_SERVER['REMOTE_ADDR'];
        $params['amount'] = $get->amount;
        $params['reference_number'] = $get->invoice;
        $params['bill_to_forename'] = $get->first_name;
        $params['bill_to_surname'] = $get->last_name;
        $params['bill_to_email'] = $get->email;
        $params['bill_to_phone'] = $get->phone ?? "0722705138";
        $params['card_type'] ="001";
        $params['card_number'] = '';
        $params['card_cvn'] = '';
        $params['card_expiry_date'] ='';
        $params['signature'] = (new cybersource())->sign($params);
        $params['main_number'] = $params['bill_to_phone'];
        return $params;
    }

    public function callback($req){
        $invoice= $req->auth_trans_ref_no;
        $model=(new paymentModel());
        $payment=(object)$model->selectOne($invoice);
        if(property_exists($payment,'status') && $payment->status==="PENDING"):
            $reason_code= $req->reason_code;
            $status_desc= $req->message;
            $status="FAILED";
            $redirect_url=$payment->cancel_return;
            if($reason_code===100){
                $status="PAID";
                $url_components = parse_url($payment->return_url);
                parse_str($url_components['query'], $params);
                $url=$payment->notify_url."&donation-id=".$params['donation-id'];
                $this->notifyCallBack($url);
                $redirect_url=$payment->return_url;
            }
            $model->update([
                "response"=>json_encode($req),
                "status"=>$status,
                "status_desc"=>$status_desc
            ],$payment->id);
            header('Location:'.$redirect_url);
        endif;
    }
    public function notifyCallBack($url){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>$url ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

}