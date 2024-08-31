
     <?php
     error_reporting(E_ALL);
     ini_set('display_errors', 1);

            $f = dd_q("SELECT * FROM setting");
            $dt = $f->fetch(PDO::FETCH_ASSOC);
            $voucher_hash = str_replace("https://gift.truemoney.com/campaign/?v=", "",$link);
            $phone = $dt['wallet'];
            $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://gift.truemoney.com/campaign/vouchers/'.$voucher_hash.'/redeem',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(array('mobile' => $phone,'voucher_hash' => $voucher_hash)),
                CURLOPT_HTTPHEADER => array(
                    'accept: application/json',
                    'content-type: application/json',
                    
                ),
                CURLOPT_USERAGENT => "xdnz_hello"
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response);
            if (isset($result->status->code)) {
                $codestatus = $result->status->code;
                $member = $result->data->voucher->member;
                if($member < 2){
                    if ($codestatus == "VOUCHER_OUT_OF_STOCK") {
                        $message['status'] = "error";
                        $message['info'] = "อั๋งเปานี้ถูกใช้งานไปแล้ว";
                    }elseif ($codestatus == "VOUCHER_NOT_FOUND") {
                        $message['status'] = "error";
                        $message['info'] = "ไม่พบอั๋งเปานี้!!";
                    }elseif ($codestatus == "VOUCHER_EXPIRED"){
                        $message['status'] = "error";
                        $message['info'] = "อั๋งเปาหมดอายุ!!";
                    }elseif ($codestatus == "SUCCESS"){
                        $balance = $result->data->voucher;
                        $ownerprofile = $result->data->owner_profile;
                            //code add point here
                        $pf = dd_q("SELECT * FROM users WHERE id = ? ", [$_SESSION['id']]);
                        $profile = $pf->fetch(PDO::FETCH_ASSOC);
                        $amount = $balance->redeemed_amount_baht;
                        $amount = str_replace(",","",$amount);;
                        $val = (int) $amount;
                        if($dt['fee'] == "on"){
                            $fee = 0.023 * $val;
                            if($fee > 10){
                                $fee = 10;
                            }
                            $val = $val - $fee;

     php>

