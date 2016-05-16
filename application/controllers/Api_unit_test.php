<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Api_unit_test extends CI_Controller
{

    /**
     *
     * @var unknown
     */
    public $api_list = array(
        array('code' => 'PMADMIN', 'api_name' => 'PMADMIN'),
        array('code' => 'WWW',     'api_name' => 'WWW'),
        array('code' => 'ERP',     'api_name' => 'ERP'),
        array('code' => 'STOCK',   'api_name' => 'STOCK'),
        array('code' => 'Solr',    'api_name' => 'Solr'),
        array('code' => 'MEMBER',  'api_name' => 'MEMBER'),
        array('code' => 'SHOP',    'api_name' => 'SHOP'),
        array('code' => 'POS',    'api_name' => 'POS'),




    );


    public function __construct()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        parent::__construct();

        $this->load->library('parser');

        $this->load->model('api_unit_test_model');
        $this->load->model('api_caller_model');
        $this->load->model('pub');

    }

    public function phpinfo()
    {
        echo phpinfo();
    }

    public function bash_all_test($category = "")
    {
        if ($category != "")
        {
            $datas = $this->api_unit_test_model->get(array('category' => $category));
        }
        else
        {
            $datas = $this->api_unit_test_model->get_all();
        }

        foreach ($datas as $data)
        {
            $temp = $this->__run_single_test($data, 'job');
            echo "[" . date('Y-m-d_H:i:s'). "] - " . (int) $temp['test_result']['status'] . " - " . $data['pk'] . " - " . $data['input'] . " - " . $data['assert_output'] . " - "  . $temp['api_url'] . "\n";
        }

    }

    /**
     *
     */
	public function index()
	{

        $result = array('api_list' => $this->api_list);

        $grid_view = $this->parser->parse('main_view', array(), TRUE);

        $result['grid_view'] = $grid_view;

	    $this->load->view('api_unit_test_view', $result);
	}

	/**
	 * API 的分類
	 *
	 * @param string $cate
	 */
	public function category($cate)
	{

	    $data = $this->api_unit_test_model->get(array('category' => $cate));

        $result['api_list'] = $this->api_list;
        $result['data'] = $data;

        $result['grid_view'] = $this->parser->parse('grid_test_case_view', $result, TRUE);

	    $this->load->view('api_unit_test_view', $result);
	}

	/**
	 *
	 */
	public function get_test_case_list()
	{
        $condition = $this->input->post();

        $data = $this->api_unit_test_model->get($condition);
        $result = array('api_list' => $this->api_list);
        $result['data'] = $data;

        $grid_view = $this->parser->parse('grid_test_case_view', $result, TRUE);

	}

	/**
	 * 檢視報告
	 * 看多日期的
	 *
	 * @param string $pk PK(自訂的流水號)
	 */
	public function report($pk = "", $date = "")
	{
	    $result['api_list'] = $this->api_list;

        if ($date == "")
        {
            $condition = array('test_case.pk' => urldecode($pk));

            $data = $this->api_unit_test_model->get_report_by_condition($condition);

            $html_result = $this->parser->parse('grid_test_report_view', array('data' => $data), TRUE);

        }
        else
        {
            $condition = array('test_case.pk'           => urldecode($pk),
                               'test_case_log.run_date' => $date,
            );

            $data = $this->api_unit_test_model->get_report_by_condition($condition);

            $temp = json_decode($data[0]['test_log']);
            $html_result = $this->parser->parse('test_case_result_view', $temp, TRUE);

        }

        $result['grid_view'] = $html_result;
        $result['data']      = $data;

	    $this->load->view('api_unit_test_view', $result);
	}

	/**
	 * 性質是 AJAX 或背景跑的時候驅動的 method
	 *
	 * @param string id PK(自訂的流水號) 透過 POST 來的
	 */
	public function run_test()
	{
	    $pk = $this->input->post('id');
	    $data = $this->api_unit_test_model->get(array('pk' => $pk));

	    //TODO: tester
	    $temp = $this->__run_single_test($data[0], '');

	    echo json_encode($temp);

	}

	private function __run_single_test($test_data, $tester)
	{
	    $temp = $this->api_caller_model->do_test($test_data);

	    $test_data['tester'] = $tester;
	    $result = $this->api_caller_model->test_result($temp['mesg'], $test_data['assert_output']);
	    $this->api_caller_model->set_log($result['status'], $test_data, json_encode($temp));

	    $this->api_unit_test_model->set(
	        array('status' => ($result['status']) ? 1: 0),
	        array('pk'     => $pk )
	        );

	    $temp['test_result'] = $result;

	    return $temp;
	}

	/**
	 * 初期先只寫死哪幾個 cell 讀進 DATABASE 中
	 * 改為由 ajax 驅動
	 *
	 * @param string $file
	 */
	public function import_excel($file = "")
	{
	    //將檔案匯入 database
	    //$this->input->file();

	    $dir  = APPPATH . '../uploads/';
	    $name = 'ASUS_EC_Testing_Scenario.' . xlsx;

	    if ($_FILES['files']['tmp_name'] && is_file($_FILES['files']['tmp_name']))
	    {
	       copy($_FILES['files']['tmp_name'], $dir . $name);
	    }

	    $file = $dir . $name;

	    $this->load->library('excel');

	    $objPHPExcel = PHPExcel_IOFactory::load($file);

	    $cell_collection = $objPHPExcel->getSheet(1)->getCellCollection();

	    foreach ($cell_collection as $cell)
	    {
	        $column     = $objPHPExcel->getSheet(1)->getCell($cell)->getColumn();
	        $row        = $objPHPExcel->getSheet(1)->getCell($cell)->getRow();
	        $data_value = $objPHPExcel->getSheet(1)->getCell($cell)->getValue();

	        if ($row == 1)
	        {
	            continue;

	        }
	        else
	        {
                $arr_data[$row][$column] = $data_value;
	        }
	    }

	    foreach($arr_data as $num => $row_data)
	    {
	        if ($row_data['H'] == "RD, API testing")
	        {
	            $api_test_rows[] = array('category'      => $row_data['G'],
	                                     'pk'            => $row_data['K'],
	                                     'descript'      => $row_data['L'],
	                                     'method'        => $row_data['N'],
	                                     'input'         => $row_data['O'],
	                                     'assert_output' => $row_data['P'],
	                                     'author'        => $row_data['U']
	            );
	        }
	    }

	    $result = $this->api_unit_test_model->add($api_test_rows);

	    if ($result)
	    {
	        echo "import success";
	    }


	}

	public function set_param()
	{
	    $param     = $this->input->post('input', TRUE);
	    $condition = $this->input->post('pk', TRUE);

	    $result = $this->api_unit_test_model->set(array('input' => $param),
	                                              array('pk' => $condition));
        if ($result)
        {
            echo "success";
        }
        else
        {
            //echo $this->db->last_query();
        }

	}

	/**
	 * 新增測試案例
	 *
	 * @param
	 */
    public function add_test()
    {
        $post_data = $this->input->post();

        $api_test_rows[] = array('type'          => 'RD, API testing',
	                             'category'      => $post_data['category'],
	                             'pk'            => $post_data['pk'],
	                             'descript'      => $post_data['descript'],
	                             'method'        => $post_data['method'],
	                             'input'         => $post_data['input'],
	                             'assert_output' => $post_data['assert_output'],
	                             'author'        => $post_data['author']
	            );

        $result = $this->api_unit_test_model->add($api_test_rows);

        if ($result)
	    {
            echo "<script>alert('新增成功');</script>";
	        $result = array('api_list' => $this->api_list);
            $grid_view = $this->parser->parse('main_view', array(), TRUE);
            $result['grid_view'] = $grid_view;
            $this->load->view('api_unit_test_view', $result);
	    }
    }
}
