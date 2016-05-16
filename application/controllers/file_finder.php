<?php
/**
 * 目錄瀏灠器
 * @author Leo.Kuo
 */
class File_finder extends CI_Controller
{
    const TEMP_DIR = '';
    const TAR_NOT_EXIST = 0;
    const TAR_MAKING = 1;
    const TAR_READY = 2;

    public function __construct()
    {
        parent::__construct();
        ini_set('display_errors', 'On');
        error_reporting(E_ALL & ~E_NOTICE);
        header("Content-type: text/html; charset=utf-8");
        $this->load->library("parser");

        // Online 禁止使用
        if (defined('IS_ONLINE_SITE') && IS_ONLINE_SITE)
        {
            // 但 preview 例外
            if ( ! defined('IS_PREVIEW') || ! IS_PREVIEW)
            {
                echo "該工具禁止在 Online 使用！";
                exit;
            }
        }
    }

    public function get_path()
    {
        echo __FILE__ . "<br>";
        echo __DIR__ . "<br>";
    }

    /**
     * 檢視目錄
     * @author Leo Kuo
     */
    public function index($value='')
    {
        // 目前目錄
        $path = (isset($_GET['path'])) ? $_GET['path'] : '';
        if ($path)
        {
            if (!is_dir($path))
            {
                $content .=  "{$path} 不是一個正確的目錄!";
                exit;
            }
        }
        else
        {
            $path = getcwd();
        }
        $path = $this->_path_format($path);

        // 容量
        $quota = (isset($_GET['quota'])) ? $_GET['quota'] : 0;
        $quota_url = ($quota) ? "&quota=1" : "";

        // --------------------
        $checked = ($quota) ? "checked" : "";
        $content =
             "<div class='curr_path'>
                目前目錄: <b>$path</b>
                <label><input type='checkbox' value='1' onclick='show_quota(this.checked)' {$checked}>顯示容量</label>
              </div>";

        // 查詢目錄
        $parent_html = $dir_html = $file_html = "";
        $items = $this->get_dir_items($path);
        foreach ($items as $item_obj)
        {
            $item = $item_obj['item'];
            $type = $item_obj['type'];
            $file = $item_obj['file'];
            if ($file == '..')
            {
                $item = $this->_find_parent_path($path);
                $url = "?path=" . urlencode($item);
                $parent_html .= "<div class='row_box'>
                                    <div class='folder left'><a href='{$url}{$quota_url}'>...上層目錄</a></div>
                                    <div style='clear:both'></div>
                                 </div>";
            }
            else if (is_dir($item))
            {
                $link = "[<a href='/file_finder/wait_tar?dir=" . urlencode($item) . "'>壓縮並下載</a>]";
                $tar_obj = $this->_get_tar_status($item);
                $dir_file = $tar_obj['tar_file'];
                switch ($tar_obj['status'])
                {
                    case File_finder::TAR_READY:
                        $tar_time = date("Y-m-d H:i:s", filemtime($dir_file));
                        $link .= " | [<a href='/file_finder/download?file=" . urlencode($dir_file) . "' title='tar by {$tar_time}'>下載</a>]";
                        break;
                    case File_finder::TAR_MAKING:
                        $link .= " | [壓縮中請稍候...]";
                        break;
                    case File_finder::TAR_NOT_EXIST:
                        break;
                }

                // tar 檔刪除連結
                if ($tar_obj['status'] != File_finder::TAR_NOT_EXIST)
                {
                    $link .= " | [<a href='/file_finder/kill_tmp_file?dir=" . urlencode($item) . "'>刪除 tar 檔</a>]";;
                }

                $url = "?path=" . urlencode($item);
                $dir_html .= "<div class='row_box'>
                                <div class='folder left'><a href='{$url}{$quota_url}'>{$file}/</a></div>
                                <div class='right'>{$link}</div>
                                <div style='clear:both'></div>
                              </div>";
            }
            else
            {
                $link = "[<a href='/file_finder/download?file=" . urlencode($item) . "'>下載</a>]";
                $file_html .= "<div class='row_box'>
                                    <div class='file left'>{$file}</div>
                                    <div class='right'>{$link}</div>
                                    <div style='clear:both'></div>
                               </div>";
            }
        }

        $dir_html = (!$dir_html && !$file_html) ? "<div>沒有任何目錄或檔案。</div>" : $dir_html;

        $content .=  <<<HTML
            <div>
                <div>$parent_html</div>
                <div>$dir_html</div>
                <div>$file_html</div>
                <div style='clear:both'></div>
            </div>
HTML;

        // ls
        $content .=  "<hr>";
        $content .=  "<div># <b>ls -alh $path</b></div>";
        $content .=  "<pre>" . `ls -alh $path` . "</pre>";


        // 檢視系統容量
        if (isset($_GET['quota']) && $_GET['quota'])
        {
            // 查看目前目錄容量
            $content .=  "<hr>";
            $content .=  "<div># <b>du -sh $path</b></div>";
            $content .=  "<pre>" . `du -sh $path` . "</pre>";

            // 寫入 log
            $log_file = "/tmp/" . $this->_get_abs_download_name($path) . "_quota.log";
            $content .=  "<pre>" . `du -sh $path > $log_file` . "</pre>";

            // 系統容量
            $content .=  "<hr>";
            $content .=  "<div># <b>df -h</b></div>";
            $content .=  "<pre>" . `df -h` . "</pre>";
        }

        $view_data = array
        (
            'content'     => $content,
            'encode_path' => urlencode($path)
        );
        $this->parser->parse_string($this->file_finder_view(), $view_data);
    }

    /**
     * 取得目錄下的所有檔案及目錄
     * @param  string $dir 路徑
     * @return array      所有檔案及目錄
     */
    public function get_dir_items($path)
    {
        $dir = opendir($path);
        $item_array = array();
        while (FALSE !== ($file = readdir($dir)))
        {
            if ($file == '.') continue;

            $item = $this->_path_format("$path/$file");
            $type = (is_file($item)) ? 'file' : 'dir';
            $item_array[] = array
            (
                'type' => $type,
                'file' => $file,
                'item' => $item,
            );
        }
        uasort($item_array, function ($a, $b)
        {
            static $cmp_rules = array
            (
                array('key' => 'type',  'precedence' => 'ASC'),
                array('key' => 'file', 'precedence' => 'ASC'),
            );

            for ($i=0, $i_max=count($cmp_rules); $i<$i_max; $i++)
            {
                $key        = $cmp_rules[$i]['key'];
                $precedence = $cmp_rules[$i]['precedence'];
                if ( ! isset($a[$key]) || $a[$key] == $b[$key]) continue;
                $result = ($a[$key] < $b[$key]) ?  -1 : 1;
                return ($precedence == 'DESC') ? $result * -1 : $result;
            }

            return 0;
        });
        return $item_array;
    }

    public function download()
    {
        $download_dir = FALSE;
        if (isset($_GET['file']))
        {
            $abs_filename = $_GET['file'];
        }
        else if (isset($_GET['dir']))
        {
            $abs_dir = $_GET['dir'];
            if (!is_dir($abs_dir))
            {
                echo "$abs_dir 不是一個目錄！";
                exit;
            }

            $tar_obj = $this->_get_tar_status($abs_dir);
            $abs_filename = $tar_obj['tar_file'];

            // 十秒後跳轉到等待畫面
            $encode_dir = urlencode($abs_dir);
            echo <<<HTML
                <script>
                    window.setTimeout(function()
                    {
                        alert('jump!');
                        window.location.href = "/file_finder/wait_tar?dir={$encode_dir}";
                    }, 2000);
                </script>
HTML;

            // 建立壓縮檔
            exec("tar --exclude=.snapshot -zcvf {$abs_filename} {$abs_dir}", $output, $return_var);
            sleep(1);

            $download_dir = TRUE;
        }

        if (!file_exists($abs_filename))
        {
            echo("File not exist!");
            exit;
        }

        $tmp = explode("/", $abs_filename);

        $srcName = addslashes($tmp[count($tmp)-1]);
        $file_ext = strtolower(substr($srcName, -4));

        switch($file_ext)
        {
               case ".pdf": $ctype="application/pdf";               break;
               case ".swf": $ctype="application/x-shockwave-flash"; break;
               case ".exe": $ctype="application/octet-stream";      break;
               case ".zip": $ctype="application/zip";               break;
               case ".doc": $ctype="application/x-ms-word";         break;
               case ".xls": $ctype="application/vnd.ms-excel";      break;
               case ".ppt": $ctype="application/vnd.ms-powerpoint"; break;
               case ".gif": $ctype="image/gif";                     break;
               case ".png": $ctype="image/png";                     break;
               case ".jpg": $ctype="image/jpg";                     break;
               case ".txt": $ctype="text/plain";                    break;
               case ".wdl": $ctype="text/plain";                    break;
               default:    $ctype="application/force-download";
        }

        $file_size = filesize($abs_filename);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"" . $srcName . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $file_size");
        if ($file_size > 31457280)
            $this->_readfile_chunked($abs_filename);
        else
            readfile($abs_filename);

        // 刪除壓縮檔
        if ($download_dir)
        {
            sleep(1);
            // exec("rm -rf /tmp/{$dir_name}.tar", $output, $return_var);
        }

        exit;
    }

    /**
     * 刪除壓縮暫存檔
     * @param  string $dir_name 檔案名稱
     * @return none
     */
    public function kill_tmp_file()
    {
        $dir = $this->input->get('dir');
        $tar_obj = $this->_get_tar_status($dir);
        $tar_file = $tar_obj['tar_file'];
        if ($tar_obj['status'] != File_finder::TAR_NOT_EXIST)
        {
            exec("rm -rf {$tar_file}", $output, $return_var);
            echo "<pre># rm -rf {$tar_file}</pre>";
            echo "<pre>output = " . print_r($output, TRUE). "</pre>\n";
            echo "<pre>return_var = " . print_r($return_var, TRUE). "</pre>\n";
            echo "<div>" . $this->_get_back_url($dir) . "</div>";
        }
        else
        {
            echo "{$tar_file} : <b>No Such File!</b>";
        }
    }

    /**************************************************************/
    public function file_finder_view()
    {
        return <<<HTML
        <html>
        <head>
        <title>file finder</title>
        <style>
            a {text-decoration: none;}
            a:hover {text-decoration: underline;}
            .base      {margin: 5px auto;}
            .curr_path {border-bottom:1px solid #ccc; padding: 3px; margin:5px;}
            .folder {padding-left:20px; background: url(data:image/gif;base64,R0lGODlhEAAMAOZsAPnVcfzhiaOZhvC8TfvsqvfNZaCVg/zcfeDc1Z93G/fOaPvdgXpwY/fwq/rWdal8FfvmleyyPSEcFvzlleiqNaCWhKaciey0Qe+5SY+EdfPEWPTGXffMY6Sah5WKerSCEPvYf9KhJtmpLaigi/rsos+ZGerNcPzjj/jSdPzkkvbsqZZyH/TFW/bqsPHAXb+KDK6DHuzksteQFvvnmC0nIPvdf/fQavrusPvfhKt/GXdtYPvpoPvom/jytJyRgOepNeOkLN+aIOK4SUE6MeakK/C+UKabifzfivPhklxTSWtjV8eYIduuN/PDVWZdUfboqvrTb/rrpOajKuipMsePDPC7S5KHd/vspZiOfbyOIEtDO4V6bDs0LTAqI+KeJPDfoqifi+He2vTKaJ+Vg1RMQtyWHfvpn/jttqqhjaykj/nwvhgTDv///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAGwALAAAAAAQAAwAAAevgGFpg4MIbIeIiGkxZ2cqSGiGiYholZVgFkYdAhUVBgYZbCNqpGo9BDsTRyAoYi4MbBZqjQ0kEAEHUBxNGCWwArM3UbcHAAUaGD9USmxjag1XMwE1xhpVEUAvSWw+ZwQ8JwsACiwDEVNeH1psWC1mKQsOChsDFxRSZQ9cbB5PEzgObGwoYo9IEBkJaLCx8sWEECYiQizJAiPHgwQrJLDJsEWHEzJDukhYQ7LkGjaBAAA7) no-repeat}
            .file   {padding-left:20px; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAQCAMAAAD6fQULAAAAhFBMVEUAAAC4uLi6urr19PT29vb5+Pj6+vn7+vr8/Pv9/Pz+/v3AwMDx8PDy8vH08vL08/OdnZ329fX39vWsrKz49vb4+Petra2xsbGzs7P8+/u1tbWGhoaQkJDg39/m5eW8vLz49/fz8fG9vb36+fnz8vK/v7/8+/q7u7v19PPFxcXKysp5eXlDKzg7AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOwwAADsMBx2+oZAAAAHxJREFUeF5NzEUWg1AUBNH+Lrhr1GX/+wsPOCE1u5NC8EsBCDrrnPfWv3kE3MxC/5KPEGe9kjPGcVDa3Gfudxy8WkhmYGWlLsRZvZxpiD3aLCdqY61r0YiNDepEiLw8ErsaRbxyWhdIr8RMElOMpyhOnsRKjRjCjQM+/30BvQMM/RABr6YAAAAASUVORK5CYII%3D) no-repeat}
            .row_box .left  {width:400px; float:left;}
            .row_box .right {width:400px; float:left;}
            .row_box:nth-child(even)  {background: #F9F9F9;}
            .row_box:hover            {background: #EFE;}
        </style>
        <script type="text/javascript">
        function show_quota(chk)
        {
            if (chk)
                window.location.href = "?path={encode_path}&quota=1";
            else
                window.location.href = "?path={encode_path}";
        }
        </script>
        </head>
        <body>
            <div class='base'>{content}</div>
        </body>
        </html>
HTML;
    }

    public function wait_tar()
    {
        $dir = $this->input->get('dir');
        $encode_dir = urlencode($dir);
        $tar_obj = $this->_get_tar_status($dir);
        $status = $tar_obj['status'];
        $tar_file = $tar_obj['tar_file'];


        $content = <<<HTML
            <div>壓縮目錄: <b>$dir</b></div>
            <div id='status' style='margin: 20px 0'>
                <div style='margin:10px'><img id='status_img' src='data:image/gif;base64,R0lGODlhLQAtALMKAFxcXIqKiqGhoXNzc8/Pz0VFRbu7uy4uLubm5gAAAP///wAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QTgwRDQzMTEzRDBCMTFFMjlFMDFFMzgyRDUyMjBFOEUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QTgwRDQzMTIzRDBCMTFFMjlFMDFFMzgyRDUyMjBFOEUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBODBENDMwRjNEMEIxMUUyOUUwMUUzODJENTIyMEU4RSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBODBENDMxMDNEMEIxMUUyOUUwMUUzODJENTIyMEU4RSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAUKAAoALAAAAAAtAC0AAAS1UMlJq7046827/+CHjNaIhKJ5TuqKbi0Vv7Aq23TtsuRE/CgT7UcEzUJEYEqIShKMuKYSustZrwqDFhvSerkf7/aSKIMnYgO5bD5nxxZ2262RJ+gcNn7Pdwv+fRh/g4EWg4AXB4p0hwKJiot+iBaQkYUTlQeXFZCbgQGgnhOgpCEFpzSkoR+nrS+qASCtqK+rrLMdA7pcrhQAvxW6wnu/xRTCu3jFwBPIA8TLwcnQAKLW19jYEQAh+QQFCgAKACwDAAcAJwAiAAAEllDJSau9CmlMk+dgpm2cZ4bYSGLmh1oqArbJe43oae98LxFAXw9IFO6IQcVhaeQgCcols3kpRqVUlHSaBWG74LDYYiiPL+X0uZI2Kwrwc9vwhsfH6rp9TbHf+RJ7gIAChYMShYkhAIxGiYYcjJJCjwIgko2UkJGYLwGfQpMUA6QVn6dgpKoUp6BdqqUTrQGpsKautQNNEQAh+QQFCgAKACwDAAMAJwAmAAAEl1DJSWe6NevNr+dgSHmYaG5kcq7Zx4ZH/M5xPbO1fJ/5sa+2n/CHKA5FxeQRlDQqCtAlpYl4QqNSidJ6zWquWG+lKy6bZ4T0eZJurxVttQJAF8cJc3rd687r33p7a39vZQaHhYeKIQONO4qIHI2TN5AGIJOOlZGSmSYCoDeUFAGlFaCoS6WrFKihR6umE64CqrGnr7YBPxEAIfkEBQoACgAsAwADACcAJgAABJdQyUnnuVWlnbuvV0ht5GdmITaR3Okq6TGy7ytWZU0VvO7vvN7PFxQOa8XCkWhcOp/QqPQEqE4/1ey1k7VuK13AN6Mdm886hBotUbvZ7rViQP/GEXN6ffvO69l6e2h/bGMEh4WHiicBjUuKiB+Nk0eQBCaTjpWRkpkvBqBHlBQCpRWgqFKlqxSooVGrphOuBqqxp6+2Ak4RACH5BAUKAAoALAMAAwAjACYAAASUUMlJZ7lVnZ27V1dIbeRnSiE2kdz5pcXIuqZYlVOie0BPd7pgpkf8VYK7CtFnzCGHyyZF2ClKr9isdmsceLkerxicEX/JFPMAXRmz3/C4nB2oy+v4ON4O3wfufHNsCIRyhIcdAopZh4UVipBYjQgZkIuSjo+WNASdRpEUBqIVnaVaoqgUpZ5ZqKMTqwSnrqSsswZSEQAh+QQFCgAKACwDAAMAJgAmAAAEllDJSSe4VZWdu6dXSG3kZ1YhNpHc6abAyLq0WJXToZ9DT3e64KdH/FWCOw/RZ8whh8smRWgqSq/YT2Kb/W2/XdeXGyiHO+NEeX3OgNfmtgcekH/Y9rx+z+83BYB+EoCEgoSBfocCggqFjI+QJgaTjJOWgpaUfpkGNAifV5cUBKQVn6dtpKoUp6BnqqUTrQipsKautQRZEQAh+QQFCgAKACwDAAMAJwAmAAAEmFDJSee4VYGdu69XSG3kZ2YhNpHc6SrpMLLvK1blVOxu4NeZndDkKwIpQt6n+DtKkgUi06lTSgPUrNZz6G5r3fDXFfYqBOhxpnw4o9Nqitj9jn/ecHunru/71QmBfxWBhYMThYIKBox+iQmLjI19hpGShxKSk5iXmJ6fRwSin6KlnqWjmKgEpKkTCLCfsLOes7GYtgiyt1kRACH5BAUKAAoALAMAAwAnACIAAASWUMlJZ7hVjZ27r1dIbeRnZiE2kdzpKmkwsu8rVuUE7K7g15md0OQrAilC3qf4O0qSACLTqVNKBdSs1lPobmvd8NcV9ioM6HGmXDij02qK2P2Of95we6eu7/vVB4F/FYGFgxOFggoEjH6JB4uMjX2GkZKHEpKTmJeYgwmgngqgpCcIp1qkoR+nrVmqCSatqK+rrLOirn0RACH5BAUKAAoALAMAAwAnACUAAASXUMlJp7hVhZ27r1dIbeRnZiE2kdzpKqkwsu8rVuU07K7h15md0OQrAilC3qf4O0qSAyLTqVNKDdSs1gPobmvd8NcV9ioI6HGmDDij02qK2P2Of95we6eu7/vVBYF/FYGFgxOFggoIjH6JBYuMjX2GkZKHEpKTmJeYgwegngqgpJ6koZinB6KlfQmvoq+ynrKwmLUJsbYmEQAh+QQFCgAKACwDAAMAJwAmAAAEmlDJSae5VYmdu69XSG3kZ2YhNpHc6SqpMbLvK1blFOwu4deZndDkKwIpQt6n+DtKkgEi06lTSgnUrNYz6G5r3fDXFfYqEOhxpjw4o9Nqitj9jn/ecHunru/71QCBfxWBhYMThYKHCokAixKGj5KTlBkFl5KXmo+amIudBZmeegelegmoFaWrcaiuFKumaq6pE7EHrbSqsrkJWxEAIfkEBQoACgAsBwADACMAJgAABJhQyTmJpdJozDu1IKWNXvmBhDgaZoum08q2ZoiRk6B7SE97uiCnR/xxgjsM0WekIAXDZRMj7BSn2GwpwNXSuGCvCdwVe8gB87asbrvf8Hh2QJdL6Hg7vi7fD+wKeYCDhIUSAIiAiIsdCY5ei4kYjpRakQAclI+WkpOaPwWhTZUUB6YYoalipqwUqaJerKcTrwWrsqiwtwdiEQAh+QQFCgAKACwEAAMAJgAmAAAElFDJSSW6VZGdu6dXSG3kZ1YhNpHc6abIyLq0WJWTodN8p/+9oOS3E/aIBmMQqGw6hYLo0xWtTk1V6dWTFWw/1q94TC6bg4H0WZJur9vqhJwMD8jv9Pg9sd7z+3NrZQOEgoSHJgeKTYeFHoqQSo0DH5CLko6PljQAnUaRFAWiFZ2lV6KoFKWeU6ijE6sAp66krLMFThEAOw0KDQo8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQoNCjxodG1sIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hodG1sIiA+DQo8aGVhZD48dGl0bGU+DQoNCjwvdGl0bGU+PC9oZWFkPg0KPGJvZHk+DQogICAgPGZvcm0gbmFtZT0iZm9ybTEiIG1ldGhvZD0icG9zdCIgYWN0aW9uPSJEZWZhdWx0LmFzcHgiIGlkPSJmb3JtMSI+DQo8ZGl2Pg0KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iX19WSUVXU1RBVEUiIGlkPSJfX1ZJRVdTVEFURSIgdmFsdWU9Ii93RVBEd1VMTFRFMk1UWTJPRGN5TWpsa1pIL2RWRHRqK1FRdEZiWXRRc0ZoV1RqWkliNVYiIC8+DQo8L2Rpdj4NCg0KICAgIDxkaXY+DQogICAgDQogICAgPC9kaXY+DQogICAgPC9mb3JtPg0KPC9ib2R5Pg0KPC9odG1sPg0K' /></div>
                <div id='status_msg'>
                    壓縮中...<br>
                    取消請按 <a href='/file_finder/kill_tmp_file?dir={$encode_dir}'>這裡</a>.
                </div>
            </div>
            <div>檔案大小: <span id='filesize'>-</span></div>
            <div>修改時間: <span id='filemtime'>-</span></div>
HTML;
        $content.= "<div>" . $this->_get_back_url($dir) . "</div>";

        $content = $this->_get_msg_box($content);
        $js = <<<HTML
            $html
            <script>
                    function \$id(id)    { return (document.getElementById(id)) ? document.getElementById(id) : document.getElementsByTagName(id)[0]; }
                    function \$load(_url, _arg, _onload, _onerror) {gAjax.\$load(_url, _arg, _onload, _onerror);}
                    function \$syncload(_url, _arg) { return gAjax.\$syncload(_url, _arg); }
                    var gAjax = {
                        m_getReq: function()
                        {
                            try { return new XMLHttpRequest(); }
                            catch(e)
                            {
                                var _XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP");
                                for (var i=0; i<_XmlHttpVersions.length; i++)
                                {
                                    try { return new ActiveXObject(_XmlHttpVersions[i]); }
                                    catch (e) {}
                                }
                            }
                        },
                        \$syncload: function(_url, _arg)
                        {
                            var _req = gAjax.m_getReq();

                            _req.open('POST', _url, false);
                            _req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                            _req.send(gAjax.m_getParam(_arg));
                            var _response = _req.responseText.replace(/^\s+/g, '').replace(/\s+$/g, '');
                            return (_response.indexOf("{") == 0) ? eval("(" + _response + ")") : "";
                        },
                        \$load: function(_url, _arg, _onload, _onerror) // asynchronous load
                        {
                            var _req = gAjax.m_getReq();

                            _req.open('POST', _url, true);
                            _req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                            _req.send(gAjax.m_getParam(_arg));

                            _req.onreadystatechange = function()
                            {
                                if (_req.readyState == 4)
                                {
                                    if (_req.status == 200)
                                    {
                                        var _err = 1
                                        // try {
                                            // alert(_req.responseText);
                                            var obj = eval("(" + _req.responseText + ")");
                                            _err = 2
                                            _onload.call(this, obj);
                                        // } catch (e) { (_err == 1) ? gAjax.m_dataError(_req.responseText) : alert("ajax.load() callback '" + _onload + "' internal error!"); }
                                    }
                                    else
                                    {
                                        (_onerror) ? _onerror.call(this) : gAjax.m_defaultError.call(this, _url, _req.status);
                                    }
                                }
                            }
                        },

                        m_getParam: function(_arg)
                        {
                            var _str = "";
                            for (p in _arg)
                            {
                                if (_str != "") _str = _str + "&";
                                _str = _str + p + "=" + _arg[p];
                            }
                            return _str;
                        },
                        m_defaultError: function(_url, _status)
                        {
                            if (_status != 0)
                                alert("error in http request: " + _status + ", " + _url);
                        }
                    };

                    function download()
                    {
                        window.location.href = '/file_finder/download?file=' + encodeURIComponent('{$tar_file}');
                    }

                    function check_tar_status()
                    {
                        var url = '/file_finder/ajax_get_tar_status?dir=' + encodeURIComponent('{$dir}');
                        var obj = \$syncload(url, {});
                        return obj;
                    }

                    function check_tar_status_polling()
                    {
                        var tar_obj = check_tar_status();
                        if (tar_obj.status == 2)
                        {
                            // 更新狀態
                            // 成功訊息
                            //
                            var ok_msg  = "<div>\u76ee\u9304\u58d3\u7e2e\u5df2\u5b8c\u6210\uff01<\/div>";
                            // 如下載並未自動開始，請點選 [<a href='javascript:download()'>這裡</a>] 手動下載
                                ok_msg += "<div>\u5982\u4e0b\u8f09\u4e26\u672a\u81ea\u52d5\u958b\u59cb\uff0c\u8acb\u9ede\u9078 [<a href='javascript:download()'>\u9019\u88e1<\/a>] \u624b\u52d5\u4e0b\u8f09<\/div>";

                            \$id('status_img').src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAYdEVYdFNvZnR3YXJlAHBhaW50Lm5ldCA0LjAuNWWFMmUAAASUSURBVGhD7ZhpyFVVFIY/U7PBHChtDkQcskKKjCYoCiJIVBwQRBA1UX+KSFhg/QmzDMsfSVEUzQOlEKgpZlBmhhFFmGn9qFDLBiyb5+eBb8dic+8959zvHv3zvfBwL+fevc8667x77XVOV696dWx0GlwGU2EuLIL5MBkuhVPhuKsvDIFpsAG+gaPwK/wGv3d//gIePwTPwC3gOMcfM50AZvRu+Az+gX8r8DfsAcdfDrUH7wluhwNQNdgcxzvPVVCLzO5FsBEaBeDt3wvb4XFYCSvgHngW3gLvipZJY/zu7ydBLboGPoE8uz/AWrgOzocB0EinwEjQz17Ej3AbeLzjMsNXw3cQg3VxbYYxUNWT/eCc7s9G8pzj4WEY7oGq0hJmOAb8JdwKJ0OnZcCWyM/BBfsKnAGlZQb1cLSEdrgZnLzT6gPXwkFI5/PcL0GpBBnUckiDxQwbcB3SKtZ7a3085xcwA7ygQlmHLUdpsB7WEnVkWE2CPGAtYuZLBawtLPzRFi66Ojx8IkyHnyEGbIYr3VW3WGtqmkAfWyU6LZOzGI5ADPg9mACVpLdilq3DVctakcywTZV9Sgx4H2jNUpaIsvlJk7jTuXF0Uu6Ad0Ae8Jtgfa4s28u4INya3elymfl2sm8Gl0Ie8EcwGtqSt8bspsnsJfKt2WJvf/EoDPVASdlP3wVuGml+bbgLxkLbsoGPWTC4KC/gKfgT/oDHQH8WyQyvAvvrGPAWuAB6JBdHnNhuLWoBxE7NE3thVpxmGgxrIC5us62Hz4MeayHEoLydUQPBamKW03/Muhn3t1xawt/i//8C79ZZ0BHNgZjpeyHXIHgRPHkM5GmIz4H+zzY0/s8M288UNUHa0Cojfm9ZAieCW3Y6iSdtJINbB7lVngAXpy2lFxYXndl28bayknKNPATPdXM/tBxzCcTqoe+abd969XmIXjUwH143gbZJx+UFcEyRfFj4FtK496GR9f6XTxKxUfoURkAzOZl3I3o2x2r0JJTtXWZC7EVehkJ5i9MA/V3UuOhdnzKidxNejM+BZd959AdtF++e708KdQPEAMykk7WSO6n+ixl3bTwIZep40rngLpzmMGlnQ6EMwE4rDrwRiqS1HgAD/wmWQZWAlT1JzLK2KtU82ej7pByzvRuKVr1K5fBOqBqwzZJP6emc+vpKKK1RsB/SBJY232MU2URZW6u2lsPgDYhZfhVMQml50tmQJhBL4TyoQy7kWCK1WNstsZtBtIn+tv9o9lKmqtwZ82rhXc3bh0py5e6ANKHoOzNTpS1tpAthK8QMu4M+AmVLZFO5Q30MMRtO7rGbwAVa1sM+ONgoLYHvIZ9TX3eskXJhvgaxlxDt8jpYqq6HRhXGCzIQ3+PZR3wA+Uakh21xOxaw8sSng21m3lOIx3xq98X5O+DWa/u5Hj6Er8GFnF90Grsa3B9qkTV8FuyERsFXwexuA+9A7TLr1tUp8C5EX5ZBa2g110NPF3NbMvNXwH3wNrg4fef3FRzu/vT1lsddZJayi6Gu12uV5bZtiRwHPtV7MX76pH0mlNlNe9Wrnqmr6z/+gNP3faHwCQAAAABJRU5ErkJggg%3D%3D";
                            \$id('status_msg').innerHTML = ok_msg;
                            \$id('filesize').innerHTML = tar_obj.filesize;
                            \$id('filemtime').innerHTML = tar_obj.filemtime;

                            // 開始下載
                            download();
                        }
                        else
                        {
                            // 更新狀態
                            \$id('filesize').innerHTML = tar_obj.filesize;
                            \$id('filemtime').innerHTML = tar_obj.filemtime;

                            // 繼續 polling
                            window.setTimeout(function()
                            {
                                check_tar_status_polling();
                            }, 3000)
                        }
                    }
                    check_tar_status_polling.run_time = 0;

                    // 開始壓縮
                    function tar_dir()
                    {
                        var url = '/file_finder/download?dir=' + encodeURIComponent('{$dir}');
                        var _success, _fail;
                        _success = _fail = function (){ /* nothing*/ };
                        \$load(url, {}, _success, _fail);
                    }

                    window.onload = function()
                    {
                        // 開始壓縮
                        tar_dir();

                        // 等待壓縮
                        check_tar_status_polling();
                    };
            </script>
HTML;


        $view_data['content'] = $content . $js;
        $this->parser->parse_string($this->file_finder_view(), $view_data);
    }

    /**
     * 取得 tar 狀態
     */
    public function ajax_get_tar_status()
    {
        $dir = $this->input->get('dir');
        $ret = $this->_get_tar_status($dir);
        echo json_encode($ret);
    }

    //**********************  函式區  ********************

    /**
     * 取得回上一頁連結
     */
    private function _get_back_url($dir = "")
    {
        if (strpos($_SERVER['REQUEST_URI'], "kill_tmp_file"))
        {
            $tmp = explode("/", $dir);
            $pop = array_pop($tmp);
            $parent_dir = implode("/", $tmp);
            return "<div style='margin-top:15px;'>[<a href='/file_finder?path={$parent_dir}'>回上一頁</a>]</div>";
        }
        else if ($_SERVER['HTTP_REFERER'])
        {
            return "<div style='margin-top:15px;'>[<a href='{$_SERVER['HTTP_REFERER']}'>回上一頁</a>]</div>";
        }
        else
        {
            return "<div style='margin-top:15px;'>[<a href='javascript:history.back()'>回上一頁</a>]</div>";
        }
    }

    /**
     * 取得容量
     * @param integer $bytes   檔案 bytes
     */
    public function KMB($bytes, $type='ceil', $decimal='1')
    {
        error_reporting(0);
        if( is_numeric( $bytes ) )
        {
            if ($bytes < 1024)
            {
                if ($type == 'floor')
                    return '0 KB';
                else
                    return '1 KB';
            }

             $position = 0;
             $units = array(" Bytes", " KB", " MB", " GB", " TB" );
             while( $bytes >= 1024 && ($bytes / 1024) >= 1 )
             {
                 $bytes /= 1024;
                 $position++;
             }

             if ($position < 2)
             {
                 if ($type == 'floor')
                     return floor($bytes) . $units[$position];
                 else
                     return ceil($bytes) . $units[$position];
             }
             else
             {
                 if ($type == 'floor')
                     return floor($bytes) . $units[$position];
                 else
                     return round($bytes, $decimal) . $units[$position];
            }
        }
        else
        {
             return "0 KB";
        }
    }

    /**
     * 顯示訊息
     */
    private function _get_msg_box($msg)
    {
        $html  = "<div style='width:500px; margin:230px auto; padding:20px 30px; border:1px solid #ccc; border-radius: 20px; background:#eef;'>";
        $html .= "<div style='padding:5px; margin:5px; text-align:center'>{$msg}</div>";
        $html .= "</div>";
        return $html;
    }

    /**
     * 判斷 tar 檔是否已壓縮完畢
     * @param  string $dir 目錄
     * @return mixed       狀態及檔名
     */
    public function _get_tar_status($dir)
    {
        clearstatcache();

        $dir_name = $this->_get_abs_download_name($dir);
        $tar_file = "/tmp/{$dir_name}.tar";
        if (file_exists($tar_file))
        {
            if (time() > filemtime($tar_file) + 10)
            {
                $status = File_finder::TAR_READY;
            }
            else
            {
                $status = File_finder::TAR_MAKING;
            }
            $file_size = $this->KMB(filesize($tar_file));
            $file_time = date("Y-m-d H:i:s", filemtime($tar_file));
        }
        else
        {
            $status = File_finder::TAR_NOT_EXIST;
        }

        $ret =
        [
            'status'    => $status,
            'tar_file'  => $tar_file,
            'filesize'  => $file_size,
            'filemtime' => $file_time
        ];
        return $ret;
    }

    /**
     * 取得目錄下載的檔名
     * @param  string $name 目錄名稱
     * @return string       下載的檔名
     */
    private function _get_abs_download_name($dir='')
    {
        return substr(str_replace("/", "_", $dir), 1);
    }

    /**
     * 格式化路徑
     */
    private function _path_format($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = str_replace("//", "/", $path);
        return $path;
    }

    /**
     * 取得上層路徑
     */
    private function _find_parent_path($path)
    {
        $tmp = explode('/', $path);
        unset($tmp[count($tmp) -1]);
        $path = implode('/', $tmp);
        if ($path)
            return $path;
        else
            return '/';
    }

    /**
     * 檔案下載
     */
    private function _readfile_chunked($filename)
    {
          $chunksize = 1*(1024*1024); // how many bytes per chunk
          $buffer = '';
          $handle = fopen($filename, 'rb');
          if ($handle === false)  return false;

          while (!feof($handle))
          {
            $buffer = fread($handle, $chunksize);
            print $buffer;
          }
          return fclose($handle);
    }
}


