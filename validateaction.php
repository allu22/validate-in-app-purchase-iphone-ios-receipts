<?php
	
    function validateReceipt($receipt, $isSandbox = true){
        if(strpos($receipt,'{') !== false){
            $receipt = base64_encode($receipt);
        }

        if ($isSandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        }
        else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        $postData = json_encode(
            array('receipt-data' => $receipt)
        );
       

        $ch = curl_init($endpoint);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        if ($errno != 0) {
            throw new Exception($errmsg, $errno);
        }

        $data = json_decode($response);

        if (!is_object($data)) {
            throw new Exception('Invalid response data');
        }
 
        if (!isset($data->status) || $data->status != 0) {
//			print 'Status Code: '. $data->status . '<br/>';
            throw new Exception('Invalid receipt');
        }

        return $data->receipt;
    }

?>