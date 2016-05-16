<?php
class list_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("api_caller_model");
    }

    public function index()
    {
        return get_class_methods(__CLASS__);
    }

    private function _call_api($url, $data, $country="tw", $host="www-api", $env="local")
    {
        $this->api_caller_model->show_api($url, $data, $country, $host, $env);
    }

    /**
     * 取得後臺樹的各頁面關鍵字數量
     * (各環境參數設定 sample)
     */
    public function get_tree_keyword_num($env)
    {
        $time = new DateTime();
        $url = "show_keyword_job_api/get_tree_number";

        //local 與 dev 可擇一設定
        $data = array(
            'local' => array(
                'ws_seq'    => 'AW000001',
                'psw_type'  => '1',
                'time'      => $time->format('Y-m-d H:i:s')
            ),
            'beta' => array(
                'ws_seq'    => 'AW000001',
                'psw_type'  => '1',
                'time'      => '2015-02-01 13:02:00'
            ),
            'preview' => array(
                'ws_seq'    => 'AW000029',
                'psw_type'  => '1',
                'time'      => '2015-02-04 13:02:00'
            )
        );
        $this->_call_api($url, $data, Api_caller::COUNTRY_TW, Api_caller::HOST_PMADMIN_API, $env);
    }

    /**
     * 取得時區
     * :9001 (pmadmin-api)
     */
    public function brian_timezone_dev($env)
    {
        $url = "show_brian_api/get_timezone/AW000001";
        $data = array();
        $this->_call_api($url, $data, Api_caller::COUNTRY_TW, Api_caller::HOST_PMADMIN_API, $env);
    }

    public function get_category_list_local_13($env, $si_seq='A13601')
    {
        $url = 'category_api/category_list';
        $data = array
        (
            'si_seq'            => $si_seq,
            'ws_seq'            => 'AW000013',
            'wc_seq'            => 'AWC000013',
            'debug'             => 'uitox.debug'
        );

        $ret = $this->_call_api($url, $data, Api_caller::COUNTRY_TW, Api_caller::HOST_WWW_API, $env);
    }

    // 測試 bc_code
    public function test_api_show_def_dev($env)
    {
        $url =  'test_api/show_def';
        $this->_call_api($url, [], Api_caller::COUNTRY_SG, Api_caller::HOST_WWW_API, $env);
    }

    // 取得 SITE_MENU 樹狀結構
    public function get_si_tree_local($env, $si_seq="A12444")
    {
        $url = "site_menu_api/get_site_menu_tree";
        $data = array
        (
            'si_seq' => $si_seq,
            'debug'  => 'uitox.debug'
        );

        $this->_call_api($url, $data, Api_caller::COUNTRY_TW, Api_caller::HOST_WWW_API, $env);
    }
}