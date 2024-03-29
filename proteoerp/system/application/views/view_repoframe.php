<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?> " />
<title><?php echo $titu; ?></title>
<?php
echo script('jquery-2.0.0.min.js');
echo script('jquery-migrate-min.js');
echo script('jquery-impromptu.js');
echo script('plugins/css_inline_transform.js');
echo style('impromptu/default.css');

$checkmail=$this->config->load('notifica',false,true);

?>
</head>
<body marginheight="0" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" >
	<script type="text/javascript">
	function descarga(){ }
	function carga(){ }

	<?php if($checkmail){ ?>
	function emailsend(){

		$.prompt(
		'<label>Correo: <input type=\"text\" size=\"40\" name=\"fcorreo\" value=\"\"></label><br />'+
		'<label>Asunto: <input type=\"text\" size=\"40\" name=\"fasunto\" value=\"Sin asunto\"></label><br />'+
		'<p>Texto adicional:<br />  <textarea name=\"ftexto\" rows=\"2\" cols=\"47\"></textarea></p>'+
		'<p id=\"mmsj\" style=\"color:red\"></p>', {
			title: 'Env&iacute;o de documento por correo',
			buttons: {'Enviar': true, 'Cancelar': false },
			submit: function(e,v,m,f){
				if(v){
					var correo = $('input[name=\"fcorreo\"]').val();
					var asunto = $('input[name=\"fasunto\"]').val();
					var texto  = $('textarea[name=\"ftexto\"]').val();

					var htmlContent = $('#contenido').contents().find('body').html();
					var hcss = $('#contenido').contents().find('link').attr('href');
					if(hcss === undefined){
						var cssContent = $('#contenido').contents().find('style').text();
					}else{
						var cssContent = $.ajax({hurl: hcss,async: false }).responseText;
					}

					createAndAppendStylesheet(cssContent);
					var tmpOutput = jQuery('<span></span>').html(htmlContent.replace(/\t/g, ''));
					tmpOutput.find('script').remove();
					tmpOutput.find('link').remove();
					tmpOutput.find('title').remove();
					tmpOutput.find('head').remove();
					tmpOutput.find('meta').remove();
					interpritAppendedStylesheet(tmpOutput);

					if(texto.length>0){
						body = '<p>'+texto+'</p><hr>'+tmpOutput.html();
					}else{
						var body = tmpOutput.html();
					}
					$('#_delcss').remove();

					$.ajax({
						dataType: 'json',
						type: 'POST',
						url: '<?php echo site_url('sincro/notifica/sendmail/html') ?>',
						data: {fcorreo:correo,fasunto:asunto,fbody:body},
						success: function(data){
							if(data.status!='A'){
								$('#mmsj').text(data.msj);
							}else{
								$.prompt.close();
							}
							//console.log(data.prog);
						}
					});

					return false;
				}
			}
		}).bind('promptloaded', function(e){
			var asunto = $('#contenido').contents().find('title').text();
			if(asunto.length>0){
				$('input[name=\"fasunto\"]').val(asunto);
			}
		});
	}

	$(function(){
		$('#btnemail').hide();
		$("#contenido").load(function (){
			var surl=String($("#contenido").get(0).contentWindow.location);
			if(surl.search("search")>0){
				if($('#contenido').contents().find('form').length==0){
					$('#btnemail').show();
				}
			}
		});
	});
	<?php } ?>
	</script>

	<div>
		<table width='100%' colspacing='0' >
			<tr>
				<td align='left' width="100px" ><img src='<?php echo $this->config->item('base_url').'/images/proteo.png'; ?>' width='85' alt='Reportes'></td>
				<td align='center'><?php echo $titulo ?></td>
				<td align="right" width="100px">
				<?php //echo anchor('reportes/ver/',image('go-previous.png','Volver al Filtro',array('border'=>0)),array('target'=>'contenido','id'=>'rgfil'));?>
				<?php echo ($checkmail)? image('mail_btn.png','Enviar por correo',array('width'=>'25','onclick'=>'emailsend()','id'=>'btnemail')) :''; ?>
				<?php echo anchor('reportes/enlistar/'.$repo,image('listado.png','Volver al Listado',array('border'=>0)),array('target'=>'contenido'));?>
				<?php echo image('cerrar.png','Cerrar Ventana',array('width'=>'25','onclick'=>'window.close()')); ?></td>
			</tr>
		</table>
	</div>

	<iframe id="contenido" name="contenido" src="<?php echo site_url('reportes/enlistar/'.$pre) ?>" width="100%" height="90%" scrolling="auto" frameborder="0">
		El navegador no soporta iFrames o esta desactivado <A href="<?php echo site_url('reportes/enlistar/sfac') ?> ">Alternativa</A>
	</iframe>
</body>
</html>
