<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api_caller_tool extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("firephp");
        $this->load->library('parser');
        $this->load->model("api_caller_model");
    }

    public function index()
    {
        $fmURL      = $this->input->get('fmURL');
        $fmSite     = $this->input->get('fmSite');
        $fmIsNewEnv = $this->input->get('fmIsNewEnv');
        $fmData     = $this->input->get('fmData');

        $url  = ($fmURL) ? $fmURL : '';
        $data = ($fmData) ? json_decode($fmData) : '';
        $country = ($fmSite) ? $fmSite : 'tw';

        // 判斷是否為新環境
        $is_new_evnironment = 0;
        if ($fmIsNewEnv)
        {
            $is_new_evnironment = 1;
        }

        $temp['mode'] = 'tool';
        $temp['fmURL'] = $fmURL;
        $temp['fmData'] = $fmData;
        $temp['fmSite'] = $fmSite;
        $temp['fmIsNewEnv'] = $fmIsNewEnv;

        $temp['title'] = "輸入參數：";
        $temp['param'] = $this->api_caller_model->print_r_fix($data, TRUE);

        $temp['json_title'] = "參數的 JSON 格式：";
        $temp['json_param'] = "<div>" .json_encode($data, TRUE) . "</div>";

        $temp['start'] = "====  呼叫開始  ====";
        $this->benchmark->mark('call_api');
        $ret = $this->api_caller_model->call_api($url, $data, $country, 0, $is_new_evnironment);
        $ret_info = $this->api_caller_model->get_ret_info($ret);
        $clear_json  = $ret_info['clear_json'];
        $prefix_info = $ret_info['prefix_info'];
        fb_log(json_decode($clear_json));
        $temp['times'] = "執行時間：<b> " . $this->api_caller_model->runtime_format($this->benchmark->elapsed_time('call_api')) . "</b><br>";
        $temp['result'] = $this->api_caller_model->print_r_fix(json_decode($clear_json), TRUE);
        if ($ret != $clear_json)
        {
            $temp['result'] = "<div style='color:#f00; margin-bottom:20px;'>警告: 目前 API 有使用 echo 直接輸出額外的 DEBUG 資訊，完工前需拿掉!</div>"
                              . "<div style='border-top:1px dashed #ccc; margin:10px 0; padding:10px 0; border-bottom:1px dashed #ccc; margin:10px 0; padding:10px 0;'><pre>$prefix_info</pre></div>"
                              . $temp['result'];
        }
        $temp['origin'] = "原始訊息：";
        $temp['mesg'] = $ret;
        $temp['end'] = "====  呼叫完畢  ====";

        $temp['api_form'] = $this->parser->parse('api_form_view', $temp, true);
        $this->parser->parse('api_result_view', $temp);
    }

    /*********************  內部函式區  ***********************/
    private function _call_api($url, $data, $country="tw")
    {
        $this->api_caller_model->show_api($url, $data, $country);
    }
}

