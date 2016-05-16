<?php
/**
 *
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @author Cloud_Liao
 *
 */
class api_unit_test_model extends CI_Model
{

    var $table_name = 'test_case';

	public function __construct()
	{
		$this->load->database();

	}

	//看商品
	public function get_all()
	{

        $this->db->select('*');
        $this->db->from($this->table_name);

        $query = $this->db->get();

	    return $query->result_array();
	}

	public function get($condition)
	{
	    $this->db->select('*');
	    $this->db->from($this->table_name);
	    $this->db->where($condition);

	    $query = $this->db->get();

	    return $query->result_array();
	}
	//新增
	public function add($data)
	{
	    $this->db->insert_batch($this->table_name, $data);

	    return $this->db->affected_rows();
	}

	public function get_report_by_pk($pk)
	{
	    $this->db->select('test_case.pk,
                           test_case.category,
                           test_case.descript,
                           test_case.input,
                           test_case.assert_output,
                           test_case_log.*');
	    $this->db->from('test_case');
	    $this->db->join('test_case_log', 'test_case.pk = test_case_log.pk');
	    $this->db->where('$pk', $pk);
	    $query = $this->db->get();

	    return $query->result_array();
	}

	public function get_report_by_date($date)
	{

	}

	public function get_report_by_cate($cate)
	{

	    $this->db->select('test_case.pk,
                           test_case.category,
                           test_case.descript,
                           test_case.input,
                           test_case.assert_output,
                           test_case_log.*');
	    $this->db->from('test_case');
	    $this->db->join('test_case_log', 'test_case.pk = test_case_log.pk');
	    $this->db->where('category', $cate);

        return $this->db->result();


	}

	public function get_report_by_condition($condition)
	{
	    $this->db->select('test_case.pk,
                           test_case.category,
                           test_case.descript,
                           test_case_log.*');
	    $this->db->from('test_case');
	    $this->db->join('test_case_log', 'test_case.pk = test_case_log.pk');
	    $this->db->where($condition);

	    $query = $this->db->get();

	    return $query->result_array();
	}

	public function set($update_value, $condition)
	{
        $this->db->where($condition);
        $this->db->update('test_case', $update_value);

        return $this->db->affected_rows();
	}


}
