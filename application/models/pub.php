<?php

class Pub extends CI_Model
{

    
    /**
     * 取得 APIs 所需的函式庫
     * @param string $postURL
     * @param string $postData
     * @return string
     */
    public function curl_post($postURL, $postData, $sec = null, $headers="")
    {
        if(isset($_SERVER['SERVER_NAME']) == false AND isset($_SERVER['REQUEST_URI']) == false)
        {
            $curlopt_referer = gethostname() . '/' . $this->uri->segment(1) . '/' . $this->uri->segment(2); // // FOR JOB SERVER
        }
        else
        {
            $curlopt_referer = $_SERVER['SERVER_NAME'] . '/' . $this->uri->segment(1) . '/' . $this->uri->segment(2);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postURL); // 設定所要傳送網址

//curl_setopt($ch,CURLOPT_HEADER, true); // 不顯示網頁
        curl_setopt($ch, CURLOPT_POST, 1); // 開啟回傳
        curl_setopt($ch, CURLOPT_REFERER, $curlopt_referer); // 將來源網址傳送出去 , 對方使用 $_SERVER['HTTP_REFERER'] 接收
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // 將post資料塞入
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 開啟將網頁內容回傳值
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // echo "CI_VIEW_SOURCE_DOMAIN = " . $headers['CI_VIEW_SOURCE_DOMAIN'] . "<br>";
        // if ($headers['CI_VIEW_SOURCE_DOMAIN'] == "uitox.com")
        // {
        //     // curl_setopt($ch, CURLOPT_PROXY, $proxy_server);
        //     echo "開啟 proxy 模式(10.1.24.98:8080)";
        // }



        if($sec >= 1)
        {
            curl_setopt($ch, CURLOPT_TIMEOUT, $sec);
        }

        $http_header[] = 'application/x-www-form-urlencoded; charset=UTF-8';
        $http_header[] = 'Expect:';


        if(
                defined("COUNTRY") && defined("LANGUAGE") && defined("CURRENCY") &&
                defined("COUNTRY_REGION") && defined("BC_CODE") && defined("TIMEZONE") && defined("VIEW_SOURCE_DOMAIN") &&
                defined("API_SOURCE_DOMAIN") && defined("INSIDE_SOURCE_DOMAIN") && defined("FIXED_SOURCE_DOMAIN")
        )
        {

            $http_header[] = 'VIEW_COUNTRY: ' . COUNTRY;
            $http_header[] = 'VIEW_LANGUAGE: ' . LANGUAGE;
            $http_header[] = 'VIEW_CURRENCY: ' . CURRENCY;
            $http_header[] = 'VIEW_COUNTRY_REGION:' . COUNTRY_REGION;
            $http_header[] = 'VIEW_BC_CODE:' . BC_CODE;
            $http_header[] = 'VIEW_TIMEZONE:' . TIMEZONE;

            $http_header[] = 'CI_VIEW_SOURCE_DOMAIN:' . VIEW_SOURCE_DOMAIN;
            $http_header[] = 'CI_API_SOURCE_DOMAIN:' . API_SOURCE_DOMAIN;
            $http_header[] = 'CI_INSIDE_SOURCE_DOMAIN:' . INSIDE_SOURCE_DOMAIN;
            $http_header[] = 'CI_FIXED_SOURCE_DOMAIN:' . FIXED_SOURCE_DOMAIN;



// openlog('PUB_CURL_LOG', LOG_PID | LOG_PERROR, LOG_LOCAL1);
// syslog(LOG_WARNING, __FILE__ . var_export($http_header, true));
// closelog();

            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        }
        else if ($headers)
        {
            $http_header[] = 'VIEW_COUNTRY: ' . $headers['VIEW_COUNTRY'];
            $http_header[] = 'VIEW_LANGUAGE: ' . $headers['VIEW_LANGUAGE'];
            $http_header[] = 'VIEW_CURRENCY: ' . $headers['VIEW_CURRENCY'];
            $http_header[] = 'VIEW_COUNTRY_REGION:' . $headers['VIEW_COUNTRY_REGION'];
            $http_header[] = 'VIEW_BC_CODE:' . $headers['VIEW_BC_CODE'];
            $http_header[] = 'VIEW_TIMEZONE:' . $headers['VIEW_TIMEZONE'];

            $http_header[] = 'CI_VIEW_SOURCE_DOMAIN:' . $headers['CI_VIEW_SOURCE_DOMAIN'];
            $http_header[] = 'CI_API_SOURCE_DOMAIN:' . $headers['CI_API_SOURCE_DOMAIN'];
            $http_header[] = 'CI_INSIDE_SOURCE_DOMAIN:' . $headers['CI_INSIDE_SOURCE_DOMAIN'];
            $http_header[] = 'CI_FIXED_SOURCE_DOMAIN:' . $headers['CI_FIXED_SOURCE_DOMAIN'];
            $http_header[] = 'VIEW_IS_NEW_EVNIRONMENT:' . $headers['VIEW_IS_NEW_EVNIRONMENT'];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);

        }



        $getPost = curl_exec($ch); // 執行網頁

        $info = curl_getinfo($ch);

        curl_close($ch); // 關閉網頁

        return $getPost;
    }


    /**
     *  寫入syslog
     * @param log_name: log紀錄名稱
     * @param log_str:  log紀錄字串
     * @return string
     */
    public function set_log($log_name, $log_str)
    {
        if($log_name == '' || $log_str == '')
        {
            return false;
        }
        openlog($log_name, LOG_PID | LOG_PERROR, LOG_LOCAL1);
        syslog(LOG_WARNING, $log_str);
        closelog();
        return true;
    }


}