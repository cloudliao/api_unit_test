<?php
/**
 * 測試
 * 沒有過驗證則失敗
 *
 */

/**
 * 測試
 * 只測 input & output 是否一致，回傳失敗則記 log
 *
 * @category UITOX
 * @package  TEST
 * @author   cloud liao <cloud_liao@asus.com>
 * @license  http://www.uitox.com
 * @link     http://www.uitox.com
 */
class Uitox_api_test
{
    /**
     * Test Case 的 config
     *
     * @var array();
     */
    public $header = array();

    protected $platform_id = '';

    protected $version = '';

    protected $api_status = array();

    public function __construct()
    {
        $this->_CI = &get_instance();
        //parent::__construct();
        $this->_CI->load->model('pub');
    }

    public function get_ini_config($data)
    {
        if ($data->category)
        {
            $define_param = $data->category . '_API_DOMAIN';

        }
    }

    /**
     * 抓取 平台編號後要進行
     */
    public function set_header_array()
    {
    	$this->header[] = '';
    	$this->header[] = 'Content-Type: application/json; charset=UTF-8';
    	$this->header[] = 'BC_CODE:' . BC_CODE;

    }


    public function do_test($data)
    {

        $this->get_ini_config($data);

        $this->set_header_array();


        echo "================================================================================================\n";
		echo "That API you call：\n";
		echo $api_url         = _VIEW_DOMAIN . '/' . $handler_name . '/' . $method;
		echo " \n";
		//echo var_export($api_array) . "\n";

		$result = $this->pub->curl_post($api_url, $api_array, $this->header);
		echo "================================================================================================\n";
		$result = json_decode($result, TRUE);

		echo "This is RESULT: \n";
		$this->test_result($result);
		echo "================================================================================================\n";


    }


    public function test_result($result)
    {
        print_r($result);

        $this->set_log();

    }

    protected function error_print($status = 'INFO', $message = '', $method = '')
    {
        //http 的 error
        if ($status == 'HTTP_ERROR')
        {
            echo "$message\n";

            return FALSE;
        }

        //uxapi 回傳的失敗



    }

}
