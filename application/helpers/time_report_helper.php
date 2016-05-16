<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 效能報表
 * @author Leo.Kuo <leo.kuo@uitox.com>
 */
class time_report
{
    public static $check_point_count = 0;
    public static $time_log = array();
    public static $_CI;

    public static function check_ci_instance()
    {
        if (!self::$_CI)
        {
            self::$_CI = & get_instance();
        }
    }

    public static function mark_start()
    {
        self::check_ci_instance();
        self::$_CI->benchmark->mark('time_report_start');
    }

    public static function mark_end()
    {
        self::check_ci_instance();
        self::$_CI->benchmark->mark('time_report_end');
    }

    public static function mark($title)
    {
        self::check_ci_instance();
        self::$check_point_count++;
        $time_tag = 'time' . self::$check_point_count;
        self::$_CI->benchmark->mark($time_tag);
        self::$time_log[] = $title;
    }

    /**
     * 顯示每個程式片段的時間
     * @author Leo.Kuo
     */
    public static function show_time_log()
    {
        $total_time = self::$_CI->benchmark->elapsed_time('total_execution_time_start', 'time_report_end') * 1000;
        echo "<style> .time_report_row:hover{background:#EEF;} </style>";
        echo "<div style='z-index:999; border:1px solid #ccc; background:#EFE; margin:80px 20px 0px 20px; padding:10px;'>";
        foreach (self::$time_log as $i => $action)
        {
            if ($i == 0)
            {
                $t_start = 'total_execution_time_start';
                $t_end   = 'time1';
            }
            else
            {
                $t_start = 'time' . ($i);
                $t_end   = 'time' . ($i+1);
            }

            $runtime = self::$_CI->benchmark->elapsed_time($t_start, $t_end) * 1000;
            $percent = ($runtime / $total_time) * 100;

            $total_runtime += $runtime;
            $total_percent += $percent;

            echo "<div class='time_report_row'>
                    <div style='float:left; width:400px;'>{$action}</div>
                    <div style='float:left; width:200px; text-align:right;'><b>" . sprintf("%.0f", $runtime) . "</b> ms</div>
                    <div style='float:left; width:200px; text-align:right;'><b>" . sprintf("%02.2f", $percent) . "</b>%</div>
                    <div style='clear:both;'></div>
                  </div>";
        }
        echo "<div style='border-top:1px solid #ccc;' class='time_report_row'>
                <div style='float:left; width:400px;'>統計</div>
                <div style='float:left; width:200px; text-align:right;'><b>" . sprintf("%.0f", $total_runtime) . "</b> ms</div>
                <div style='float:left; width:200px; text-align:right;'><b>" . sprintf("%02.2f", $total_percent) . "</b>%</div>
                <div style='clear:both;'></div>
              </div>";
        echo "<div class='time_report_row'>
                <div style='float:left; width:400px;'>總時間</div>
                <div style='float:left; width:200px; text-align:right;'><b>" . self::$_CI->benchmark->elapsed_time('total_execution_time_start', 'time_report_end') . "</b>秒</div>
                <div style='float:left; width:200px; text-align:right;'></div>
                <div style='clear:both;'></div>
              </div>";
        echo "<div style='border-top:1px solid #ccc;' class='time_report_row'>
                <div style='float:left; width:400px;'>記憶體使用</div>
                <div style='float:left; width:200px; text-align:right;'><b>" . self::memory_use_now() . "</div>
                <div style='float:left; width:200px; text-align:right;'></div>
                <div style='clear:both;'></div>
              </div>";
        echo "</div>";
    }

    public function memory_use_now()
    {
        $level = array('Bytes', 'KB', 'MB', 'GB');
        $n = memory_get_usage();
        for ($i=0, $max=count($level); $i<$max; $i++)
        {
            if ($n < 1024)
            {
                $n = round($n, 2);
                return "{$n} {$level[$i]}";
            }
            $n /= 1024;
        }
    }
}

/* End of file time_report_helper.php */
/* Location: ./new_application/helpers/time_report_helper.php */