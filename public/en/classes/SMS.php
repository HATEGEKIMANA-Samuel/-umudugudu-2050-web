<?php
class SMS
{
    public static function getPin()
    {
        return rand(10000, 99999);
    }
    public static function send($to, $content = 'no content')
    {
        //header("Access-Control-Allow-Origin: *");
        $to = '25' . $to;
        //$content='This is your Login Pin: '.self::getPin();
        $url = "http://bulksms.mtn.co.rw:3060/send?username=rmsltd&password=R4wq8m&to=" . urlencode($to) . "&content=" . urlencode($content);
        //$html_brand = "www.yahoo.com";
        $ch = curl_init();

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_MAXREDIRS      => 10,
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            //   echo "Return code is {$httpCode} \n".curl_error($ch);
            //   echo '403 Forbidden. Inspect the body of the response for further details - 
            //     for example, you may have insufficient credits remaining';
            return false;
        } else {
            // echo "<pre>" . htmlspecialchars($response) . "</pre>";
            return true;
        }

        curl_close($ch);

        //  $ch=curl_init();

        //   //STEP2
        //   curl_setopt_array($ch,array(
        //   CURLOPT_URL=> $url,
        //   CURLOPT_RETURNTRANSFER=>TRUE,
        //   CURLOPT_TIMEOUT, 20,
        //   URLOPT_CONNECTTIMEOUT,10,
        //   CURLOPT_HEADER=>FALSE));
        // //STEP 3
        //   $data=curl_exec($ch);
        //   print_r(curl_getinfo($ch));
        //   //STEP4
        //   return $data;
        //   curl_close($ch);
        //  //return file_get_contents($url);
    }
}
