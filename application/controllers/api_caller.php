<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api_caller extends CI_Controller
{
    const LOCAL = 'local';
    const DEV = 'dev';
    const BETA = 'beta';
    const PREVIEW = 'preview';
    const ONLINE = 'online';
    const HOST_WWW_API = 'www-api';
    const HOST_SEARCH_API = 'search-api';
    const HOST_PMADMIN_API = 'pmadmin-api';
    const HOST_VENDOR_API = 'vendor-api';
    const COUNTRY_TW = 'tw';
    const COUNTRY_SG = 'sg';
    const COUNTRY_TH = 'th';

    private $_CI;

    public $call_function;
    public $call_env;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper("firephp");
        $this->load->library('parser');
        $this->load->model("list_model");
        $this->load->model('pub');
        $this->_CI = &get_instance();
    }

    public function index()
	{
        $ignore_list = array
        (
            'index',
            'get_instance'
        );

        // Get function list
        $arr = $this->list_model->index();
        $data = array(
            'controller' => $this->_CI->router->class
        );
        $this->get_foreach($data, $arr, $ignore_list);
        $this->parser->parse('api_caller_view', $data);
	}

    public function result($function, $env)
    {
        $this->call_function = strtoupper($function);
        $this->call_env      = strtoupper($env);
        $this->list_model->$function($env);
    }

    /**
     * 迴圈處理 function list
     *
     * @param type $data
     * @param type $arr
     * @param type $ignore_list
     */
    private function get_foreach(&$data, $arr, $ignore_list)
    {
        // 排序檢查
        $this->_get_list_sort($arr);

        foreach ($arr as $method)
        {
            if (!in_array($method, $ignore_list) && preg_match("/^[^_]/", $method))
            {
                $data['tmp'][] = array(
                    'title' => '<li>
                    <a class="local"   href="/api_caller/result/' . $method . '/' . self::LOCAL . '" target="_blank">Local</a>
                    <a class="dev"     href="/api_caller/result/' . $method . '/' . self::DEV . '" target="_blank">Dev</a>
                    <a class="beta"    href="/api_caller/result/' . $method . '/' . self::BETA . '" target="_blank">Beta</a>
                    <a class="preview" href="/api_caller/result/' . $method . '/' . self::PREVIEW . '" target="_blank">Prev</a>
                    <a class="online"  href="/api_caller/result/' . $method . '/' . self::ONLINE . '" target="_blank">Online</a>
                    <span>' . $method . '</span>
                    </li>'
                );
            }
        }
    }

    /**
     * 將陣列資料排序
     *
     * return void
     */
    private function _get_list_sort(array &$sort_data)
    {
        $list_sort = $this->input->get('sort', TRUE);
        switch($list_sort)
        {
            case 'desc': rsort($sort_data);
                break;
            case 'asc': sort($sort_data);
                break;
            default:
                break;
        }
    }
}