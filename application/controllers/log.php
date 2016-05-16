<?php
class Log extends CI_Controller
{
    /**
     * 顯示的筆數
     */
    const NUM_ROWS = 500;
    
    /**
     * 日期 - 留空表示當天日期(format : 2015-01-01)
     */
    const DATE_TIME = '';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('parser');
        //$this->_CI  = & get_instance();
    }
    
    public function index()
    {
        //
    }
    
    public function error_log()
    {
        // 排除的規則
        $must_not = array('NAXSI_FMT');

        // 必要的條件
        $must_arr = array(
            'hosttype:("www","search","www-api","search-api","pmadmin-api","vendor","stage-www")'
        );
        
        // 執行 API
        $res_json = $this->_post_api(__FUNCTION__, $this->_get_post_params($must_arr, $must_not));
        
        // 頁面顯示
        $this->_show_view($res_json);
    }

    public function php_log()
    {
        // 排除的規則
        $must_not = array('hosttype:("shop","email-api","pos-api","member","stockm-api","stage-email-api")');
        
        // 必要的條件
        $must_arr = array(
            'log_level:("ERR")'
        );
        
        // 執行 API
        $res_json = $this->_post_api(__FUNCTION__, $this->_get_post_params($must_arr, $must_not));
        
        // 頁面顯示
        $this->_show_view_php_log($res_json);
    }

    /**
     * 必要的過濾條件
     * 
     * @param array $params
     * @return boolean
     */
    private function _must(array $params)
    {
        $must_params = $arr = array();
        
        // 必要的參數
        $must_params[0]['range']['@timestamp'] = array(
            'from' => 1426607999000,
            'to' => time() * 1000
        );
        
        // 自訂的參數
        foreach ($params as $key => $val)
        {
            $arr['fquery']['query']['query_string']['query'] = $val;
            $arr['fquery']['_cache'] = TRUE;

            $must_params[$key+1] = $arr;
        }

        return $must_params;
    }

    /**
     * 排除的規則
     * 
     * return array
     */
    private function _must_not(array $params)
    {
        $must_not =array();
        foreach ($params as $key => $val)
        {
            $must_not[$key]['fquery']['query']['query_string']['query'] = $val;
            $must_not[$key]['fquery']['_cache'] = TRUE;
        }
        return $must_not;
    }
    
    /**
     * 取得日期
     * 
     * return string
     */
    private function _get_date_time()
    {
        if (self::DATE_TIME)
        {
            return self::DATE_TIME;
        }

        $mDay = new DateTime();
        return $mDay->format('Y-m-d');
    }
    
    /**
     * Error_log 頁面顯示
     * 
     * @param json $res_json API 回傳的值
     * 
     * return void
     */
    private function _show_view($res_json)
    {
        $view_data = array();
        $i = $j =0;
        $res = json_decode($res_json, TRUE);

        $view_data['total'] = 'Total : ' . $res['hits']['total'] . "<br>";
        foreach ($res['hits']['hits'] as $hits)
        {
            $i += 1;
            $view_data['item'][$j]['number'] = $i;
            $view_data['item'][$j]['hosttype'] = $hits['_source']['hosttype'];
            $view_data['item'][$j]['message'] = htmlspecialchars($hits['_source']['@message'], ENT_QUOTES);
            $j += 1;
        }

        // output
        $this->parser->parse('error_log_view', $view_data);
    }
    
    /**
     * Php_log 頁面顯示
     * 
     * @param json $res_json API 回傳的值
     * 
     * return void
     */
    private function _show_view_php_log($res_json)
    {
        $view_data = array();
        $i = $j =0;
        $res = json_decode($res_json, TRUE);

        $view_data['total'] = 'Total : ' . $res['hits']['total'] . "<br>";
        foreach ($res['hits']['hits'] as $hits)
        {
            $i += 1;
            $view_data['item'][$j]['number'] = $i;
            $view_data['item'][$j]['time'] = $hits['_source']['time_iso8601'];
            $view_data['item'][$j]['level'] = $hits['_source']['log_level'];
            $view_data['item'][$j]['hosttype'] = $hits['_source']['hosttype'];
            $view_data['item'][$j]['class'] = $hits['_source']['class_name'];
            $view_data['item'][$j]['method'] = $hits['_source']['method_name'];
            $view_data['item'][$j]['message'] = htmlspecialchars($hits['_source']['log_str'], ENT_QUOTES);
            $j += 1;
        }

        // output
        $this->parser->parse('php_log_view', $view_data);
    }
    
//    public function test()
//    {
//        $must_arr = array(
//            'hosttype:("www","search","www-api","search-api")',
//            'hosttype:("pmadmin-api","vendor","stage-www")'
//        );
//        
//        $arr = $this->_must($must_arr);
//        var_dump($arr);
//    }
    
    /**
     * 取得要 POST 的所有設定的條件與參數
     * 
     * @param array $must     必要的參數
     * @param array $must_not 排除的設定
     * @return array
     */
    private function _get_post_params(array $must, $must_not = array())
    {
        $params = array(
            'query' => array(
                'filtered' => array(
                    'filter' => array(
                        'bool' => array(
                            'must' => $this->_must($must),
                            'must_not' => $this->_must_not($must_not)
                        )
                    ),
                    'query' => array(
                        'bool' => array(
                            'should' => array(
                                '0' => array(
                                    'query_string' => array(
                                        'query' => '*'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'highlight' => array(
                'fields' => array(),
                'fragment_size' => 2147483647,
                'pre_tags' => array('@start-highlight@'),
                'post_tags' => array('@end-highlight@')
            ),
            'size' => self::NUM_ROWS,
            'sort' => array(
                array(
                    '@timestamp' => array(
                        'order' => 'desc',
                        'ignore_unmapped' => TRUE
                    )
                ),
                array(
                    '@timestamp' => array(
                        'order' => 'desc',
                        'ignore_unmapped' => TRUE
                    )
                )
            )
        );
        
        return $params;
    }

    /**
     * post API
     * 
     * @param array $query_word 送出查詢的所有條件與參數
     * 
     * return JSON
     */
    private function _post_api($function, array $query_word)
    {
        // 取得日期
        $date_time = $this->_get_date_time();
        $url = 'http://kibana01.idc1.ux/' . $function . '-' . $date_time . '/_search';

        $data_string = json_encode($query_word);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json; charset=UTF-8',
            'Content-length:' . strlen($data_string)
        ));
        curl_setopt($ch, CURLOPT_PROXY, '10.1.24.98');
        curl_setopt($ch, CURLOPT_PROXYPORT, '8080');
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
?>