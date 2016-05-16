<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Error Log API 工具</title>

    <style type="text/css">
    ::selection{ background-color: #E13300; color: white; }
    ::moz-selection{ background-color: #E13300; color: white; }
    ::webkit-selection{ background-color: #E13300; color: white; }

    body {
        background-color: #fff;
        margin: 20px 40px;
        font: 13px/20px normal Helvetica, Arial, sans-serif;
        color: #4F5155;
    }

    h1 {
        color: #444;
        background-color: activecaption;
        border-bottom: 1px solid #D0D0D0;
        font-size: 17px;
        font-weight: bold;
        margin: 0 0 14px 0;
        padding: 14px 15px 10px 15px;
    }

    table, th, td{
        border: 1px solid #555;
        border-collapse: collapse;
    }
    
    th,td{
        padding: 10px;
    }
    
    .body{
        margin: 0 15px 0 15px;
    }

    #container{
        margin: 10px;
        border: 1px solid #D0D0D0;
        -webkit-box-shadow: 0 0 8px #D0D0D0;
    }
    </style>
</head>
<body>

<div id="container">

    <h1>{total}</h1>
    <div class="body">
        <table width="100%">
            <tr style="background-color: activecaption">
                <th>No.</th>
                <th>hosttype</th>
                <th>message</th>
            </tr>
            <?php foreach($item as $val):?>
            <tr>
                <td><?php echo $val['number'];?></td>
                <td><?php echo $val['hosttype'];?></td>
                <td><?php echo $val['message'];?></td>
            </tr>
            <?php endforeach;?>
        <table>
    </div>
</div>
</html>