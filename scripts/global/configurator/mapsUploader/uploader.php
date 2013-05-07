<!DOCTYPE
    html
    PUBLIC
    "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TeamServer: Загрузка карт на сервер</title>
	<link href="http://www.teamserver.ru/favicon.ico" type="image/x-icon" rel="icon" />
	<link href="http://www.teamserver.ru/favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="https://panel.teamserver.ru/css/js.css" /> 
	<link rel="stylesheet" type="text/css" href="https://panel.teamserver.ru/css/login.css" /> 
	<link rel="stylesheet" type="text/css" href="https://panel.teamserver.ru/css/ts-client.css" /> 
	<link rel="stylesheet" type="text/css" href="https://panel.teamserver.ru/css/ts-theme/jquery-ui.css" />

	<script type="text/javascript" src="https://panel.teamserver.ru/js/jquery-1.4.4.min.js"></script> 
	<script type="text/javascript" src="https://panel.teamserver.ru/js/jquery-ui-1.8.8.custom.min.js"></script> 
	<script type="text/javascript" src="https://panel.teamserver.ru/js/plupload.full.min.js"></script> 
	<script type="text/javascript" src="https://panel.teamserver.ru/js/plupload.html4.min.js"></script> 
	<script type="text/javascript" src="https://panel.teamserver.ru/js/plupload.html5.min.js"></script> 
	<script type="text/javascript" src="https://panel.teamserver.ru/js/jquery.plupload.queue.min.js"></script>
</head>
<body>
<?php 
	$id = intval($_GET['id']);
	$token = $_GET['token'];
	$ip = $_SERVER['HTTP_HOST'];
	
	$uploadScript = "http://".$ip."/~configurator/mapsUploader/upload.py";
?>
	
	<div id="rightfield" style="width: 500px; min-width: 500px; min-height: 250px;">
		<div id="map_upload">		
		<div id="filelist">No runtime found.</div>
		<br />
		<a id="pickfiles" href="#" class='button' style="color: #fff;">Выбор файлов</a>
		<a id="uploadfiles" href="#" class='button' style="color: #fff;">Загрузить файлы</a>	
		</div>
		<br/>
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<small>
				<ul>
					<li>Карты должны быть упакованы в ZIP-архив.</li>
					<li>Пути должны быть от главной папки, т.е. той, в которой находится <strong>maps</strong>.
						Например:<br/>
						maps/cs_havana.nav <br/>
						maps/cs_havana.txt<br/>
						models/finger.mdl<br/>
						И так далее.<br/>
				 	</li>
					<li>Вы можете указать и закачать сразу несколько файлов.</li>
				</ul>
				
				
				
				
				</small>
				</p>
		</div>
		<br/>
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<small>
				Ограничение на один загружаемый файл - 20Мб.<br/> 
				Если вам требуется загрузить карту, которая в архиве имеет больший размер,
				используйте FTP.
				</small>
				</p>
		</div>	
	</div>	
	<script type="text/javascript">
		// Custom example logic
		$(function() {
			var uploader = new plupload.Uploader({
				runtimes : 'html5,html4',
				browse_button : 'pickfiles',
				container : 'map_upload',
				max_file_size : '20mb',
				url : '<?php echo $uploadScript;?>',
				multipart_params: 
					{'id': '<?php echo $id; ?>',
					'token': '<?php echo $token; ?>'}
				,
				filters : [
					{title : "Архивы ZIP", extensions : "zip"}
				]
			});
		
			uploader.bind('Init', function(up, params) {
				$('#filelist').html("<div>Механизм загрузки: " + params.runtime + "</div>");
			});
		
			$('#uploadfiles').click(function(e) {
				uploader.start();
				e.preventDefault();
			});
		
			uploader.init();
		
			uploader.bind('FilesAdded', function(up, files) {
				$.each(files, function(i, file) {
					$('#filelist').append(
						'<div id="' + file.id + '">' +
						file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
					'</div><div id="res_' + file.id + '"></div><div id="progress_' + file.id + '"></div>');
				});
		
				up.refresh(); // Reposition Flash/Silverlight
			});

			uploader.bind('UploadProgress', function(up, file) {
				$('#' + file.id + " b").html(file.percent + "%");
				$('#progress_' + file.id).progressbar({
											value: file.percent
										});
			});
		
			uploader.bind('Error', function(up, err) {
				if (err.code == '-600'){
					$('#filelist').append("<div>Ошибка: Размер файла не должен превышать 20Мб" +
					"</div>");
				}
				else
				if (err.code == '-601'){
					$('#filelist').append("<div>Ошибка: Неправильное расширение файла, допускается только ZIP" +
					"</div>");
				}
				else
				{
					$('#filelist').append("<div>Ошибка #" + err.code + ": " + err.message +
					(err.file ? ", Файл: " + err.file.name : "") +
					"</div>");
				}
				
				
		
				up.refresh(); // Reposition Flash/Silverlight
			});
		
			uploader.bind('FileUploaded', function(up, file, response) {
				$('#progress_' + file.id).progressbar("destroy");
				var obj = jQuery.parseJSON(response.response);

				if (obj != null && obj.error.code > 0)
				{
					$('#res_' + file.id).addClass("ui-state-error ui-corner-all");
					$('#res_' + file.id).html("&nbsp;<strong>Ошибка #" + obj.error.code + ":</strong> " + obj.error.message);
				}
				else
				if (obj != null && obj.error.code ==0)
				{
					$('#res_' + file.id).addClass("ui-state-highlight ui-corner-all");
					$('#res_' + file.id).html("<p>" + obj.error.message + "</p>");
				}
			});
			
			$(".button, input:submit").button();
			
		});
		</script>
</body>
</html>