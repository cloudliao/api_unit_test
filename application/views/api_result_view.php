<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{API_NAME}</title>
    <link rel="icon" type="image/ico" href="/res/site.ico"></link>
    <link rel="shortcut icon" href="/res/site.ico"></link>

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

    .env
    {
        text-align: center;
    }

     .local{
        background-color: lightgreen;
    }

     .dev{
        background-color: lightblue;
    }

     .beta{
        background-color: orange;
    }

     .preview{
        background-color: coral;
    }

     .online{
        background-color: red;
        color: #fff;
    }


    .body{
    margin: 0 15px 0 15px;
    }

    #container{
        margin: 10px;
        border: 1px solid #D0D0D0;
        -webkit-box-shadow: 0 0 8px #D0D0D0;
    }

    code {
        font-family: Consolas, Monaco, Courier New, Courier, monospace;
        font-size: 15px;
        background-color: #f9f9f9;
        border: 1px solid #D0D0D0;
        color: #002166;
        display: block;
        margin: 10px 0;
        padding: 10px;
    }
    </style>
</head>
<body>

{api_form}

<?php if (!isset($mode) || $fmURL) : ?>
<div id="container">

    <h1>{title}</h1>
    <div class="body">
        <code><pre>{param}</pre></code>
    </div>

    <h1>{json_title}</h1>
    <div class="body">
        <code><pre>{json_param}</pre></code>
    </div>

    <h1 class='env {env_class}'>{env_info} - {api_url}</h1>

    <h1>{start} {times}</h1>
    <div class="body">
        <code><pre>{result}</pre></code>
    </div>

    <h1>{origin}</h1>
    <div class="body">
        <code>{mesg}</code>
    </div>
    <h1>{end}</h1>
</div>
<?php endif; ?>
</html>