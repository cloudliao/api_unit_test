<h3 class="sub-header">測試報告 </h3>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>狀態</th>
          <th>日期</th>
          <th>機器位置</th>
          <th>input</th>
          <th>assert_output</th>
          <th>測試人</th>
          <th>動作</th>
        </tr>
      </thead>
      <tbody>
      <?php
      //if ($data)
      //{
      foreach($data as $key => $value):?>
        <tr id="<?php echo $value['pk']?>">
          <td>
          <?php if($value['status'] == 1) {?>
			NORMAL
		  <?php }else{?>
			FAILURE
		  <?php }?>
          </td>
          <td><?php echo $value['run_date']?></td>
          <td><?php echo $value['category']?></td>
          <td><?php echo $value['input']?></td>
          <td><?php echo $value['assert_output']?></td>
          <td><?php echo $value['tester']?></td>

          <td><a  class="btn btn-success watch_date_report" href="/api_unit_test/report/<?php echo urlencode($value['pk'])?>/<?php echo date('YmdHis', strtotime($value['run_date']))?>">該筆報告</a></td>
        </tr>
        <?php endforeach;
      //}
      ?>

      </tbody>
    </table>
  </div>

