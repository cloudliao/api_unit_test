<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>API 工具 - 清單</title>
    <link rel="icon" type="image/ico" href="/res/site.ico"></link>
    <link rel="shortcut icon" href="/res/site.ico"></link>

	<style type="text/css">
	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	h1 {
		color: #444;
		background-color: activecaption;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	#body{
		margin: 0 15px 0 15px;
	}

	#container{
        clear:both;
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}

    #method_ist{
        list-style: decimal;
    }

    #method_ist li{
        width: 98%;
        border: 1px solid #D0D0D0;
        margin: 5px 0;
        font-size: larger;
        font-weight: bold;
    }

    #method_ist li a{
        padding: 12px 10px;
        color: #000;
        text-decoration: none;
        display: inline-block;
        border-style: solid;
        border-width: 0.5px;
    }

    #method_ist li .local{
        background-color: lightgreen;
    }

    #method_ist li .dev{
        background-color: lightblue;
    }

    #method_ist li .beta{
        background-color: orange;
    }

    #method_ist li .preview{
        background-color: coral;
    }

    #method_ist li .online{
        background-color: red;
        color: #fff;
    }

    #method_ist li a:hover{
        color: #00F;
        text-decoration: underline;
        background-color: #EEE;
        display: inline-block;
    }

    .sort li{
        width: 80px;
        border: 1px solid #D0D0D0;
        margin: 5px;
        font-size: larger;
        font-weight: bold;
        float: left;
        list-style: none;
    }

    .sort li a{
        padding: 5px;
        color: #000;
        text-decoration: none;
        display: block;
    }

    .sort li a:hover{
        color: #00F;
        background-color: #EEE;
        display: block;
    }
	</style>
</head>
<body>

<div class="sort">
    <li><a href="/{controller}/">原排序</a></li>
    <li><a href="/{controller}/?sort=asc">正排序</a></li>
    <li><a href="/{controller}/?sort=desc">逆排序</a></li>
</div>
<div id="container">
	<h1>Welcome to Uitox API Tools - by Legendary Gentlemen Leo </h1>

    <ul id="method_ist">
    {tmp}
        {title}
    {/tmp}
    </ul>
</div>
</html>