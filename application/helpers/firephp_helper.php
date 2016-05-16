<?php
/**
 * 把訊息記入 firebug
 * @author Leo Kuo
 */
function fb_log($arg1, $arg2="")
{
    static $init;
    static $firephp;

    // 產生實體
    if (!$init)
    {
        ob_start();
        $SELF_PATH = dirname(__FILE__);
        include_once ("$SELF_PATH/FirePHPCore/fb.php");
        $firephp = FirePHP::getInstance(true);

        // 調整設定 (拿掉最深 5 層的限制)
        $firephp->setOption('maxObjectDepth', 20);
        $firephp->setOption('maxArrayDepth', 20);

        $init = true;
    }

    // 多參數寫法
    $arg_num = func_num_args();
    if ($arg_num == 1)
    {
        $firephp->log(func_get_arg(0));
    }
    else
    {
        $firephp->group(func_get_arg(0));
        for ($i=1; $i<$arg_num; $i++)
        {
            $firephp->log(func_get_arg($i));
        }
        $firephp->groupEnd();
    }

    // 加到 output buffer
    if ($arg_num == 1)
    {
        $fb_data = array('args' => func_get_arg(0));
    }
    else
    {
        $title = func_get_arg(0);
        $args = array();
        for ($i=1; $i<$arg_num; $i++)
        {
            $args[] = func_get_arg($i);
        }
        $fb_data = array('title' => $title, 'args' => $args);
    }
    fb_output_buffer::push($fb_data);
}

/**
 * debug 資訊的 buffer
 * @author Leo Kuo
 */
class fb_output_buffer
{
    public static $output = array();

    public static function push($data)
    {
        self::$output[] = $data;
    }

    public static function output_html()
    {
        echo "<div style='display:none'><pre>" . print_r(self::$output, TRUE). "</pre></div>";
    }

    public static function output_cookie()
    {
        echo "<div style='display:none'><pre>" . print_r(self::$output, TRUE). "</pre></div>";
    }
}

/**
 * 註冊 php 結束時的 callback
 * @author Leo Kuo
 */
register_shutdown_function(function ()
{
    fb_output_buffer::output_html();
});
?>