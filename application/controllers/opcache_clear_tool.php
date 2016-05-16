<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Opcache_clear_tool extends CI_Controller
{
    // 網站
    public $site_array = array
    (
        'asap_soon_www' => array
        (
            'name'  => '台灣/新加坡 - WWW',
            'url'   => 'http://www%02d.idc1.ux/opcache_clear',
            'proxy' => '10.1.24.98:8080'
        ),
        // 'feiniu_www' => array
        // (
        //     'name'  => '上海 - WWW',
        //     'url'   => 'http://www%02d.idc1.fn/opcache_clear',
        //     'proxy' => '10.1.24.98:8080'
        // ),
        'asap_soon_search' => array
        (
            'name'  => '台灣/新加坡 - SEARCH',
            'url'   => 'http://search%02d.idc1.ux/opcache_clear',
            'proxy' => '10.1.24.98:8080'
        ),
        // 'feiniu_search' => array
        // (
        //     'name'  => '上海 - SEARCH',
        //     'url'   => 'http://search%02d.idc1.fn/opcache_clear',
        //     'proxy' => '10.1.24.98:8080'
        // )
    );


    public function __construct()
    {
        parent::__construct();
        $this->load->library("parser");
    }


    public function index()
    {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL & ~E_NOTICE);

        // 取得參數
        $fmSite       = (array) $this->input->get('fmSite');

        // 組出 site select
        $i = 0;
        foreach ($this->site_array as $site => $site_info)
        {
            $i++;
            $checked = in_array($site, $fmSite) ? "checked" : "";
            $siteSelect .= "<label><input type='checkbox' id='fmSite_{$i}' name='fmSite[]' value='{$site}' {$checked}> {$site_info['name']}</label><br>";
        }
        $siteSelect .= "</select>";

        // 取得結果
        if (count($fmSite) > 0)
        {
            foreach ($fmSite as $site)
            {
                $result .= $this->clear_opcache($site);
            }
            $reload_checked = ($fmAutoReload) ? "checked" : "";
        }
        else
        {
            $result = "請選擇你要清除的站！";
        }

        // 畫面輸出
        $this->parser->parse_string($this->get_view(), array
        (
            'siteSelect' => $siteSelect,
            'result'     => $result,
            'js'         => $this->get_js()
        ));


    }

    public function clear_opcache($site)
    {
        return false;

        $name  = $this->site_array[$site]['name'];
        $url   = $this->site_array[$site]['url'];
        $proxy = $this->site_array[$site]['proxy'];

        $result = "<div>清除 OPCACHE: <b>$name</b></div>";
        for ($i=1; ;$i++)
        {
            // 取得檔案
            $touch_url = sprintf($url, $i);
            $conf_content = $this->get_file($touch_url, $proxy);

            // 顯示結果
            if ($conf_content === FALSE)
            {
                $msg = "連線失敗";
                if ($fail++ == 3) break;;
            }
            else
            {
                $msg =  (strpos($conf_content, "opcache_reset is OK") !== FALSE) ? "OK" : "Fail";
            }
            $result .= "<div><b>$i.</b> <a href='$touch_url' target='_blank'>$touch_url</a>........{$msg}!</div>";
        }
        $result .= "<div>Complete at  &nbsp;[" . date("H:i:s") . "]</div>";
        $result .= "<div style='border-bottom:1px solid #ccc; margin: 10px 0 15px 0;'></div>";
        return $result;
    }


    public function get_js()
    {
        $json_site_array = json_encode($this->site_array);
        return <<<HTML
            <script src='http://www.dev1.ux/c/js/common/jquery/jquery.js'></script>
            <script>
                var GLOBAL =
                {
                    sites          : $json_site_array,
                    is_ajax_status : 0,
                    index          : 0,
                    fail_times     : 0,
                    polling_times  : 0,
                    url            : 'http://www[i].idc1.ux/opcache_clear'
                };

                var jsonp_caller =
                {
                    get_ajax : function(url)
                    {
                        GLOBAL.ajax_done = false;
                        GLOBAL.polling_times = 0;

                        $.ajax
                        ({
                            url      : url,
                            async    : false,
                            dataType : 'jsonp',
                            complete : function(XMLHttpRequest)
                            {
                                // console.log('complete');
                                opcache_tool.show_result(XMLHttpRequest.status);
                                GLOBAL.is_ajax_status = XMLHttpRequest.status;
                                GLOBAL.ajax_done = true;
                            }
                        });

                        window.setTimeout(jsonp_caller.ajax_polling, 50);
                    },

                    ajax_polling : function()
                    {
                        // console.log('polling...');

                        if (GLOBAL.polling_times++ > 7)
                        {
                            // console.log('GLOBAL.polling_times = ', GLOBAL.polling_times);
                            if (GLOBAL.fail_times++ > 3)
                            {
                                var result_html = '<div>Complete at  &nbsp;[11:46:21]</div><BR>';
                                $(result_html).appendTo('#result');

                                opcache_tool.clear_queue();
                            }
                            else
                            {
                                opcache_tool.show_result(0);
                                opcache_tool.clear();
                            }
                            return false;
                        }

                        // console.log('GLOBAL.ajax_done = ', GLOBAL.ajax_done);
                        if ( ! GLOBAL.ajax_done)
                        {
                            window.setTimeout(jsonp_caller.ajax_polling, 50);
                            return false;
                        }

                        opcache_tool.clear();
                    }
                };


                var opcache_tool =
                {
                    show_result : function(status)
                    {
                        var status_msg = (status == 200) ? 'OK' : 'FAIL';
                        var result_html = '<div><b>' + GLOBAL.index + '.</b> <a href="' + GLOBAL.curr_url + '">' + GLOBAL.curr_url + '</a>..........' + status_msg + '</div>';
                        $(result_html).appendTo('#result');
                    },

                    clear : function()
                    {
                        GLOBAL.index++;
                        // console.log('GLOBAL.is_ajax_status = ' + GLOBAL.is_ajax_status);
                        if (GLOBAL.is_ajax_status != 200)
                        {
                            if (++GLOBAL.fail_times == 3)
                            {
                                alert('opcache fail!');

                                var result_html = '<div>Complete at  &nbsp;[11:46:21]</div>';
                                $(result_html).appendTo('#result');

                                return false;
                            }
                        }

                        var format_i = (GLOBAL.index >= 10) ? GLOBAL.index : '0' + GLOBAL.index;
                        var url = GLOBAL.url.replace('%02d', format_i);
                        GLOBAL.curr_url = url;
                        // console.log('url = ', url);
                        jsonp_caller.get_ajax(url);
                    },

                    clear_site : function(site)
                    {
                        var result_html = '<div>清除 OPCACHE: <b>' + GLOBAL.sites[site].name + '</b></div>'
                        $(result_html).appendTo('#result');

                        GLOBAL.url = GLOBAL.sites[site].url;
                        GLOBAL.index          = 0;
                        GLOBAL.fail_times     = 0;
                        GLOBAL.polling_times  = 0;

                        // console.log(GLOBAL.url);
                        opcache_tool.clear();
                    },

                    clear_queue : function()
                    {
                        if (GLOBAL.clear_queue.length)
                        {
                            var site = GLOBAL.clear_queue.shift();
                            opcache_tool.clear_site(site);
                        }
                    },

                    clear_all : function()
                    {
                        GLOBAL.clear_queue = [];
                        $('[id^="fmSite_"]').each(function(index)
                        {
                            if (this.checked)
                            {
                                GLOBAL.clear_queue.push(this.value);
                            }
                        });

                        opcache_tool.clear_queue();
                    }
                };

                $(function ()
                {
                    $('#fmBtnClear').bind('click', opcache_tool.clear_all);
                });
            </script>
HTML;
    }

    public function get_view()
    {
        return <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8">
            <title>OPCACHE 清除</title>
            {js}
            <style>
            body
            {
                padding: 5px;
            }
            #result
            {
                margin        : 10px;
                padding       : 20px 30px;
                border        : 1px solid #ccc;
                background    : #eef;
                font-size     : 14px;
            }
            #msgBox
            {
                margin        : 10px;
                padding       : 20px 30px;
                font-size     : 14px;
            }
            .content
            {
                width:100%;
                height: 50px;
            }
            .result_content
            {
                border:1px solid #ccc;
                padding: 3px;
            }
            #sitesBox
            {
                border: 1px solid #ccc;
                border-radius: 5px;
                background: #EFE;
                padding: 5px;
                margin: 5px 0;
                width:200px;
            }
            </style>
            </head>
            <body>
            <pre>
            ==  OPCACHE 清除  ==
            </pre>
            <form id='form1' method='GET'>
                <div>
                    <input id=action name=action type=hidden>
                    Site: <div id='sitesBox'>{siteSelect}</div>
                    <input type='submit' id='fmSubmit' name='fmSubmit' value='清除 OPCACHE'>
                    <input type='button' id='fmBtnClear' name='fmBtnClear' value='清除 OPCACHE'>
                </div>
                <div id='result'>{result}</div>
            </form>
            </body>
            </html>
HTML;
    }

    /**
     * 取得檔案 (使用 curl proxy)
     */
    public function get_file($file, $proxy="", $timeout=1)
    {
        $curl = curl_init($file);
        if (substr($url, 0, 5) == "https")
        {
            curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        else
        {
            curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);
        }
        curl_setopt($curl, CURLOPT_PROXY, $proxy);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($curl);

        // 檢查連線是否成功
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($response_code != "200")
        {
            return FALSE;
        }
        else
        {
            return $data;
        }
    }
}

/* End of file opcache_clear_tool.php */
/* Location: ./application/controllers/opcache_clear_tool.php */