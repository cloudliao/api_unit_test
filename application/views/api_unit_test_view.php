<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>ASUS API UNIT TEST</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="/c/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="/c/css/bootstrap-theme.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Custom styles for this template -->
    <link href="/c/css/dashboard.css" rel="stylesheet">

</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">ASUS API UNIT TEST</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">FAQ</a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a></li>
            <li><a href="#">Reports</a></li>

          </ul>
          <ul class="nav nav-sidebar">
          <?php foreach($api_list as $key => $value):?>
            <li><a href="/api_unit_test/category/<?php echo $value['code']?>"><?php echo $value['api_name']?></a></li>
          <?php endforeach;?>
          </ul>

        </div>
        <div id="main-area" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <?php echo $grid_view;?>


        </div>
      </div>
    </div>


	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script src="/c/js/vendor/jquery.ui.widget.js"></script>
    <script src="/c/js/jquery.iframe-transport.js"></script>
    <script src="/c/js/jquery.fileupload.js"></script>
    <script src="/c/js/jquery.fileupload-process.js"></script>
    <script src="/c/js/jquery.fileupload-validate.js"></script>

	<script type="text/javascript">


	var watchReportBtn = {

			init: function ()
			{
				var activeBtn = $('.watch_report');
				activeBtn.bind('click', this.seeReportDetail);
			},

			seeReportDetail: function (){
				var $tdObj = $(this).parent().parent();
				var id     = $tdObj.attr('id');

				window.location = "/api_unit_test/report/" + encodeURIComponent(id);

			}

		};


		var runTestBtn = {

				init: function (){
					var activeBtn = $('.run_test');
					activeBtn.bind('click', this.runTest);
				},

				runTest: function()
				{
					var $tdObj = $(this).parent().parent();
					var id     = $tdObj.attr('id');

					var button = $(this).addClass('disabled');

					$.post('/api_unit_test/run_test',
							{id: id},
							function (result){
								button.removeClass('disabled');
							}
					);
				}
		};

		var editBtn = {

			init: function ()
			{
				var activeBtn = $('.edit');
				activeBtn.bind('click', this.onEdit);
			},

			onEdit: function()
			{
				var $tdObj  = $(this).parent();
				var $tdObjP = $(this).parent().parent();
				var id      = $tdObjP.attr('id');

				var inpValue = $tdObj.find('span').text();
	    		$tdObj.find('span').text('');

	    		$(this).off('click', editBtn.onEdit);

	    		var inpArea = $('<input />').val(inpValue);;
	    		inpArea.prependTo($tdObj);

	    		$(this).text('送出');

				$(this).on('click', editBtn.onSend);


			},

			onSend: function()
			{

				var $tdObj  = $(this).parent();
				var $tdObjP = $(this).parent().parent();
				var id      = $tdObjP.attr('id');
				var activeBtn = $(this);

				activeBtn.off('click', editBtn.onSend);

				var inpValue = $tdObj.find('input').val();

				$.post('/api_unit_test/set_param',
					   {pk: id, input:inpValue},
					   function (result){
							if(result == "success")
							{}
								$tdObj.find('span').text(inpValue);
								$tdObj.find('input').remove();
								activeBtn.text('編輯');
								activeBtn.on('click', editBtn.onEdit);

				});


			}



		};

	$(function () {
	    var url = '/api_unit_test/import_excel',
	        uploadButton = $('<button/>')
	            .addClass('btn btn-primary')
	            .prop('disabled', true)
	            .text('Processing...')
	            .on('click', function () {
	                var $this = $(this),
	                    data = $this.data();
	                $this
	                    .off('click')
	                    .text('Abort')
	                    .on('click', function () {
	                        $this.remove();
	                        data.abort();
	                    });
	                data.submit().always(function () {
	                    $this.remove();

	                });

	                data.submit().complete(function (result, textStatus, jqXHR) {
	                	$('.progress-bar-success').remove();
	                    $('.progress').hide();

	                    if (result.responseText != ""){
	                    	var result = $('<div/>').html(result.responseText);

	                    	$('#files').append(result);
	                    }
	                });
	            });

	    $('#fileupload').fileupload({
	        url: url,
	        dataType: 'html',
	        autoUpload: false,
	        acceptFileTypes: /(\.|\/)(xls?x)$/i,

	    }).on('fileuploadadd', function (e, data) {
	        data.context = $('<div/>').appendTo('#files');
	        $.each(data.files, function (index, file) {
	            var node = $('<p/>')
	                    .append($('<span/>').text(file.name));
	            if (!index) {
	                node
	                    .append('<br>')
	                    .append(uploadButton.clone(true).data(data));
	            }
	            node.appendTo(data.context);
	            $('.progress').show();

	        });
	    }).on('fileuploadprocessalways', function (e, data) {
	        var index = data.index,
	            file = data.files[index],
	            node = $(data.context.children()[index]);
	        if (file.preview) {
	            node
	                .prepend('<br>')
	                .prepend(file.preview);
	        }
	        if (file.error) {
	            node
	                .append('<br>')
	                .append($('<span class="text-danger"/>').text(file.error));
	        }
	        if (index + 1 === data.files.length) {
	            data.context.find('button')
	                .text('Upload')
	                .prop('disabled', !!data.files.error);
	        }
	    }).on('fileuploadprogressall', function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        $('#progress .progress-bar').css(
	            'width',
	            progress + '%'
	        );
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');


	    watchReportBtn.init();
	    runTestBtn.init();
	    editBtn.init();
	});



	</script>
</body>
</html>