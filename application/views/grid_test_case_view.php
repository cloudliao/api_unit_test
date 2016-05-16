<h3 class="sub-header"> API 單元測試</h3>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>狀態</th>
          <th>PK</th>
          <th>機器位置</th>
          <th>描述</th>
          <th>method</th>
          <th>input</th>
          <th>assert_output</th>
          <th>作者</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php
      if ($data)
      {
      foreach($data as $key => $value):?>
        <tr id="<?php echo $value['pk']?>">
          <td>
          <?php if($value['status'] == 1) {?>
			NORMAL
		  <?php }else{?>
			FAILURE
		  <?php }?>
          </td>
          <td><?php echo $value['pk']?></td>
          <td><?php echo $value['category']?></td>
          <td><?php echo $value['descript']?></td>
          <td><?php echo $value['method']?></td>
          <td>
          <span><?php echo $value['input']?></span>
          <button  class="btn btn-primary edit" data-modify="on" >編輯</button>
          </td>
          <td><?php echo $value['assert_output']?></td>
          <td><?php echo $value['author']?></td>
          <td>
          	<!-- <button class="btn btn-danger local"   href="/api_caller/result/<?php echo $value['method']?>/' . self::LOCAL . '" target="_blank">Local</button>
            <button class="btn btn-primary dev"     href="/api_caller/result/<?php echo $value['method']?>/' . self::DEV . '" target="_blank">Dev</button>
            <button class="btn btn-success beta"    href="/api_caller/result/<?php echo $value['method']?>/' . self::BETA . '" target="_blank">Beta</button>
            <button class="btn btn-danger preview" href="/api_caller/result/<?php echo $value['method']?>/' . self::PREVIEW . '" target="_blank">Prev</button>
            -->
            <button  class="btn btn-danger run_test" >跑測試</button>
			<button  class="btn btn-success watch_report">看報告</button>

          </td>
        </tr>
        <?php endforeach;
      }?>

      </tbody>
    </table>
  </div>

