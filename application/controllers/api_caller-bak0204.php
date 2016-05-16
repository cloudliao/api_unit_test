<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api_caller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("firephp");
        $this->load->library('parser');
        $this->load->model("api_caller_model");
    }

    public function new_category_list_mutli_local($si_seq='A13613')
    {
        $url = "http://localhost:9002/category_api/new_category_list_mutli";
        // $data = json_decode('{"si_seq":"A13592","ws_seq":"AW000001","wc_seq":"AWC000001","is_attribute":1,"is_category":1,"is_init_attribute":1,"rows":30,"stats_fl":["SM_PRICE"],"check_cp_seq":[],"search_price":"","check_av_seq":["400483"],"page":1,"sort":[["IS_SALEQTY","desc"],["score","desc"]],"debug":"uitox.debug"}');
        // $si_seq = 'A13616';
        $data = array
        (
            'si_seq'            => $si_seq,
            'ws_seq'            => 'AW000001',
            'wc_seq'            => 'AWC000001',
            // 'is_attribute'      => 1,
            // 'is_category'       => 1,
            // 'check_av_seq'      => array('401938', '400916', '400995'),
            // 'stats_fl'          => array('SM_PRICE'),
            // 'search_price'      => [1, 10000],
            // 'is_init_attribute' => 1,
            'debug'             => 'uitox.debug'
        );
        $result = $this->_call_api($url, $data, "new-tw");
    }

    public function new_category_list_mutli_beta1($si_seq='B513')
    {
        $url = "http://www-api.beta1.ux/category_api/new_category_list_multi";
        // $data = json_decode('{"si_seq":"A13592","ws_seq":"AW000001","wc_seq":"AWC000001","is_attribute":1,"is_category":1,"is_init_attribute":1,"rows":30,"stats_fl":["SM_PRICE"],"check_cp_seq":[],"search_price":"","check_av_seq":["400483"],"page":1,"sort":[["IS_SALEQTY","desc"],["score","desc"]],"debug":"uitox.debug"}');
        // $si_seq = 'A13616';
        $data = array
        (
            'si_seq'            => $si_seq,
            'ws_seq'            => 'BW000001',
            'wc_seq'            => 'BWC000001',
            // 'is_attribute'      => 1,
            // 'is_category'       => 1,
            // 'check_av_seq'      => array('401938', '400916', '400995'),
            // 'stats_fl'          => array('SM_PRICE'),
            // 'search_price'      => [1, 10000],
            // 'is_init_attribute' => 1,
            // 'debug'             => 'uitox.debug'
        );
        // $result = $this->_call_api($url, $data, "new-ux-sg-beta");
        $result = $this->_call_api($url, $data, "new-ux-sg-beta");
    }


    public function new_get_show_main_mutli_local()
    {
        $url = "http://localhost:9002/search_api/new_get_show_main_mutli";
        $data = array
        (
            'ws_seq'            => 'AW000001',
            'q'                 => 'iphone',
            'is_attribute'      => 1,
            'is_category'       => 1,
            'is_init_attribute' => 1,
            'search_price'      => [1, 100],
            'debug'             => 'uitox.debug'
        );
        // $data = json_decode('{"q":"iphone","ws_seq":"AW000001","wc_seq":"AWC000001","rows":4,"page":1,"return_fl":["SM_SEQ","SM_PRICE","SSM_PRICE","SM_NAME","SM_PIC","IS_ORGI_ITEM","SM_PIC_SIZE","COLOR","IS_FIRST_IN_DT"],"debug":"uitox.debug"}');
        $result = $this->_call_api($url, $data, "new-tw");
    }


    public function campaign_list_multi_local()
    {
        $url = "http://localhost:9002/campaign_api/campaign_list_multi";
        // $data = json_decode('{"1_ACTTYPE":"201411A2800000641","ws_seq":"AW000001","wc_seq":"AWC000001","is_attribute":1,"is_category":1,"is_init_attribute":1,"rows":36,"stats_fl":["SM_PRICE"],"check_cp_seq":[],"search_price":"","check_av_seq":[],"page":1,"sort":[["IS_SALEQTY","desc"],["score","desc"]],"debug":""}');
        $data = array
        (
            'ws_seq'    => 'AW000001',
            'wc_seq'    => 'AWC000001',
            '1_ACTTYPE' => '201501A0800000040',
            'search_price' => [1, 10000]
        );
        // $data = json_decode('{"0_ACTTYPE":"201412B0200000001","ws_seq":"BW000019","wc_seq":"BWC000011","is_attribute":1,"is_category":1,"is_init_attribute":0,"rows":36,"stats_fl":["SM_PRICE"],"check_cp_seq":false,"search_price":"","check_av_seq":["ID400008"],"page":"1","sort":[["IS_SALEQTY","desc"],["score","desc"]],"debug":"uitox.debug"}');
        $result = $this->_call_api($url, $data, "new-tw");
    }

    public function category_test_dev1()
    {
        $url = 'http://www-api.dev1.ux/category_api/robot_test';
        $ret = $this->_call_api($url, [], 'new-ux-tw');
    }

    public function category_test_beta1()
    {
        $url = 'http://www-api.beta1.ux/category_api/robot_test';
        $config = array
        (
            'ws_seq'         => 'AW000001',
            // 'supplier'       => [],
            // 'category'       => ['200002'],
            'attr_value'     => ['408074', '408069'],
            // 'attr_value'     => ['403272'],
            // 'date_start'     => '2013-10-01 00:00:00',
            // 'date_end'       => '2014-10-01',
            // 'sm_price_start' => '10',
            // 'sm_price_end'   => '10000',
            // 'q'              => 'iphone'
        );
        $data = array('config' => json_encode($config));
        $ret = $this->_call_api($url, $data, 'new-ux-tw-beta');
    }

    public function category_test_local()
    {
        $url = 'http://localhost:9002/category_api/robot_test';
        $config = array
        (
            'ws_seq'         => 'AW000001',
            // 'supplier'       => [],
            // 'category'       => ['200002'],
            // 'attr_value'     => ['408074', '408069'],
            // 'attr_value'     => ['403272'],
            // 'date_start'     => '2013-10-01 00:00:00',
            // 'date_end'       => '2014-10-01',
            // 'sm_price_start' => '10',
            // 'sm_price_end'   => '10000',
            'q'              => '測試',
            // 'sm_seq'            => ['201409AM120000001'],
            // 'rows'             => 1,
            'debug'             => 1,
        );
        $data = array('config' => json_encode($config));
        $ret = $this->_call_api($url, $data, 'new-tw');
    }

    public function category_test_preview()
    {
        $url = 'http://www-api.idc1.ux/category_api/robot_test';
        $config = array
        (
            'ws_seq'         => 'AW000029',
            // 'supplier'       => [],
            // 'category'       => ['200938'],
            // 'attr_value'     => ['408074', '408069'],
            // 'attr_value'     => ['403272'],
            // 'date_start'     => '2013-10-01 00:00:00',
            // 'date_end'       => '2014-10-01',
            // 'sm_price_start' => '10',
            // 'sm_price_end'   => '10000',
            // 'q'              => '測試',
            'q'              => 'ipad',
            // 'sm_seq'            => ['201409AM120000001'],
            // 'rows'             => 1,
            'debug'             => 1,
        );
        $data = array('config' => json_encode($config));
        $ret = $this->_call_api($url, $data, 'new-tw-preview');
        // $ret = $this->_call_api($url, $data, 'new-tw-online');
    }

    public function modify_solr_data_local()
    {
        $url = 'http://localhost:9003/search_real_time_api/modify_solr_data';
        $data = array
        (
            'ws_seq' => 'AW000001',
            // 'sm_seq' => ['201409AM120000001']
            'sm_seq' => ['201409AM120000001', '201411AM140000002', '201411AM140099999']
        );
        $ret = $this->_call_api($url, $data, 'new-tw');
    }

    public function modify_solr_data_dev1()
    {
        $url = 'http://search-api.dev1.ux/search_real_time_api/modify_solr_data';
        $data = array
        (
            'ws_seq' => 'AW000001',
            'sm_seq' => ['201409AM120000001']
        );
        $ret = $this->_call_api($url, $data, 'new-ux-tw');
    }

    public function del_solr_data_dev1()
    {
        $url = 'http://search-api.dev1.ux/search_real_time_api/delete_solr_data';
        $data = array
        (
            'ws_seq' => 'AW000001',
            'sm_seq' => ['201501AM300000002']
        );
        $ret = $this->_call_api($url, $data, 'new-ux-tw');
    }

    // public function Grace_preview_custom_page_menu()
    // {
    //     $url = "http://pmadmin-api.idc1.ux/custom_page_menu_api/test/1";
    //     $data = array
    //     (
    //         'cp_seq' => '200002',
    //         'debug'  => 'uitox.debug'
    //     );

    //     $this->_call_api($url, $data, 'new-tw-preview');
    // }

    /*********************  內部函式區  ***********************/
    public function index()
    {
        $ignore_list = array
        (
            'index',
            'get_instance'
        );

        $arr = get_class_methods(__CLASS__);
        $data = array();
        $this->api_caller_model->get_foreach($data, $arr, $ignore_list);
        $this->parser->parse('api_caller_view', $data);
    }

    private function _call_api($url, $data, $country="tw")
    {
        return $this->api_caller_model->show_api($url, $data, $country);
    }
}