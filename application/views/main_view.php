
<h2 class="page-header">匯入 excel</h1>

<div class="row">
<input type="file" id="fileupload"  name="files" multiple/>
<br>
<div id="files" class="files"></div>
<div id="progress" class="progress" style="display:none">
    <div class="progress-bar progress-bar-success"></div>
</div>

</div>



<div style="height:80px;"></div>

<h3 class="page-header">新增單筆測試案例</h3>

<div class="row">
<form action="api_unit_test/add_test" method="post" id="add_test_form">
<table class="table table-condensed table-responsive">
    <tr>
        <th><label for="category_1">機器</label></th>
        <td>
            <input type="radio" name="category" value="PMADMIN" id="category_1" onclick="change_func_id_val()"/> 商品<br />
            <input type="radio" name="category" value="ERP" onclick="change_func_id_val()"/> ERP<br />
            <input type="radio" name="category" value="STOCK" onclick="change_func_id_val()"/> 倉庫<br />
            <input type="radio" name="category" value="RmaRemote" onclick="change_func_id_val()"/> solr<br />
            <input type="radio" name="category" value="MEMBER" onclick="change_func_id_val()"/> 會員
        </td>
    </tr>
    <tr>
        <th><label for="author">案例作者</label></th>
        <td><input type="text" name="author" id="author" onchange="change_func_id_val()"/></td>
    </tr>
    <tr>
        <th><label for="func_id">Func_id (${機器}#${Owner}${五碼流水號})</label></th>
        <td><input type="text" name="pk" id="func_id"/></td>
    </tr>
    <tr>
        <th><label for="descript">功能描述</label></th>
        <td><input type="text" name="descript" id="descript"/></td>
    </tr>
    <tr>
        <th><label for="method">Method</label></th>
        <td><input type="text" name="method" id="method"/></td>
    </tr>
    <tr>
        <th><label for="input">Input</label></th>
        <td><input type="text" name="input" id="input"/></td>
    </tr>
    <tr>
        <th><label for="output">Assret_output</label></th>
        <td><input type="text" name="assert_output" id="output"/></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="button" value="送出" class="btn btn-primary" id="submit_btn" onclick="submit_form()"></td>
    </tr>
</table>
</form>
</div>

<script type="text/javascript">

function change_func_id_val()
{
    var form = document.getElementById('add_test_form');
    //var type = document.getElementsByName('type');
    var author_val = document.getElementById('author').value;
    var current_func_id = document.getElementById('func_id').value;

    //取得radio的值
    for (var i=0; i<form.category.length; i++)
    {

       if (form.category[i].checked)
       {
          var category_val = form.category[i].value;
          break;
       }
    }

    if (category_val != '' || author_val != '' )
    {
        document.getElementById('func_id').value = category_val + '#' + author_val;
    }
}

function submit_form()
{
    var form = document.getElementById('add_test_form');

    //取得radio的值
    for (var i=0; i<form.category.length; i++)
    {

        if (form.category[i].checked)
        {
            var category_val = form.category[i].value;
            break;
        }
        else
        {
            category_val = '';
        }
    }

    if (category_val == '')
    {
        alert('請選擇機器');
    }
    else if (document.getElementById('author').value == '')
    {
        alert('請填寫測試案例作者');
    }
    else if (document.getElementById('func_id').value == '')
    {
        alert('請填寫Func_id');
    }
    else if (document.getElementById('descript').value == '')
    {
        alert('請填寫功能描述');
    }
    else if (document.getElementById('method').value == '')
    {
        alert('請填寫Method');
    }
    else if (document.getElementById('input').value == '')
    {
        alert('請填寫Input');
    }
    else if (document.getElementById('output').value == '')
    {
        alert('請填寫Assret_output');
    }
    else
    {
        form.submit();
    }
}

</script>

