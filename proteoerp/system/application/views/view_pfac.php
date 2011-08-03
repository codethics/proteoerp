<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itpfac');
$scampos  ='<tr id="tr_itpfac_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigoa']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['desca']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cana']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['preca']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['tota']['field'];
for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['itiva']['field'];
$scampos .= $campos['sinvtipo']['field'];
$scampos .= $campos['itpvp']['field'];
$scampos .= $campos['itcosto']['field'];
$scampos .= $campos['sinvpeso']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itpfac(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
itpfac_cont=<?php echo $form->max_rel_count['itpfac']; ?>;
invent = (<?php echo $inven; ?>);
//jinven = eval('('+invent+')');

$(function(){
	$(document).keydown(function(e){
		if (e.which == 13) return false;
	});

	$(".inputnum").numeric(".");
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itpfac']; ?>;i++){
		cdropdown(i);
		autocod(i.toString());
	}
});

function OnEnter(e,ind){
	var keynum;
	var keychar;
	var numcheck;

	if(window.event){ //IE
		keynum = e.keyCode;
	}else if(e.which){ //Netscape/Firefox/Opera
		keynum = e.which;
	}
	if(keynum==13){
		dacodigo(ind);
		return false;
	}

	//keychar = String.fromCharCode(keynum);
	return true;
}

function dacodigo(nind){
	ind=nind.toString();
	var codigo = $("#codigoa_"+ind).val();
	var eeval;
	eval('eeval= typeof invent._'+codigo);
	//alert(eeval);
	var descrip='';
	if(eeval != "undefined"){
		eval('descrip=invent._'+codigo+'[0]');
		eval('tipo   =invent._'+codigo+'[1]');
		eval('base1  =invent._'+codigo+'[2]');
		eval('base2  =invent._'+codigo+'[3]');
		eval('base3  =invent._'+codigo+'[4]');
		eval('base4  =invent._'+codigo+'[5]');
		eval('itiva  =invent._'+codigo+'[6]');
		eval('peso   =invent._'+codigo+'[7]');
		eval('precio1=invent._'+codigo+'[8]');
		eval('pond   =invent._'+codigo+'[9]');

		$("#desca_"+ind).val(descrip);
		$("#precio1_"+ind).val(base1);
		$("#precio2_"+ind).val(base2);
		$("#precio3_"+ind).val(base3);
		$("#precio4_"+ind).val(base4);
		$("#itiva_"+ind).val(itiva);
		$("#sinvtipo_"+ind).val(tipo);
		$("#sinvpeso_"+ind).val(peso);
		$("#itpvp_"+ind).val(precio1);
		$("#itcosto_"+ind).val(pond);
	}else{
		$("#desca_"+ind).val('');
		$("#precio1_"+ind).val('');
		$("#precio2_"+ind).val('');
		$("#precio3_"+ind).val('');
		$("#precio4_"+ind).val('');
		$("#itiva_"+ind).val('');
		$("#sinvtipo_"+ind).val('');
		$("#sinvpeso_"+ind).val('');
		$("#itpvp_"+ind).val('');
		$("#itcosto_"+ind).val('');
	}
	post_modbus_sinv(nind);
}
function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var preca   = Number($("#preca_"+ind).val());
	var tota = roundNumber(cana*preca,2);
	$("#tota_"+ind).val(tota);

	totalizar();
}

function totalizar(){
	var iva    =0;
	var totalg =0;
	var itiva  =0;
	var itpeso =0;
	var totals =0;
	var tota   =0;
	var peso   =0;
	var cana   =0;
	var arr=$('input[name^="tota_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cana_"+ind).val());
			itiva   = Number($("#itiva_"+ind).val());
			itpeso  = Number($("#sinvpeso_"+ind).val());
			tota    = Number(this.value);

			peso    = peso+(itpeso*cana);
			iva     = iva+tota*(itiva/100);
			totals  = totals+tota;
		}
	});
	$("#peso").val(roundNumber(peso,2));
	$("#totalg").val(roundNumber(totals+iva,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));

	$("#totalg_val").text(nformat(totals+iva,2));
	$("#totals_val").text(nformat(totals,2));
	$("#ivat_val").text(nformat(iva,2));
}

function add_itpfac(){
	var htm = <?php echo $campos; ?>;
	can = itpfac_cont.toString();
	con = (itpfac_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	autocod(can);
	$('#codigoa_'+can).focus();
	itpfac_cont=itpfac_cont+1;
}

function post_precioselec(ind,obj){
	if(obj.value=='o'){
		otro = prompt('Precio nuevo','');
		otro = Number(otro);
		if(otro>0){
			var opt=document.createElement("option");
			opt.text = nformat(otro,2);
			opt.value= otro;
			obj.add(opt,null);
			obj.selectedIndex=obj.length-1;
		}
	}
	importe(ind);
}

function post_modbus_scli(){
	var tipo  =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	//var cambio=confirm('¿Deseas cambiar los precios por los que tenga asginado el cliente?');

	var arr=$('select[name^="preca_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			id  = Number(ind);
			this.selectedIndex=tipo;
			importe(id);
		}
	});
	totalizar();
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var tipo =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	$("#preca_"+ind).empty();
	var arr=$('#preca_'+ind);
	cdropdown(nind);
	
	jQuery.each(arr, function() { this.selectedIndex=tipo; });
	importe(nind);
	totalizar();
}

function cdropdown(nind){
	var ind=nind.toString();
	var preca=$("#preca_"+ind).val();
	var pprecio  = document.createElement("select");

	pprecio.setAttribute("id"    , "preca_"+ind);
	pprecio.setAttribute("name"  , "preca_"+ind);
	pprecio.setAttribute("class" , "select");
	pprecio.setAttribute("style" , "width: 100px");
	pprecio.setAttribute("onchange" , "post_precioselec("+ind+",this)");

	var ban=0;
	var ii=0;
	var id='';
	
	if(preca==null || preca.length==0) ban=1;
	for(ii=1;ii<5;ii++){
		id =ii.toString();
		val=$("#precio"+id+"_"+ind).val();
		opt=document.createElement("option");
		opt.text =nformat(val,2);
		opt.value=val;
		pprecio.add(opt,null);
		if(val==preca){
			ban=1;
			pprecio.selectedIndex=ii-1;
		}
	}
	if(ban==0){
		opt=document.createElement("option");
		opt.text = nformat(preca,2);
		opt.value= preca;
		pprecio.add(opt,null);
		pprecio.selectedIndex=4;
	}

	opt=document.createElement("option");
	opt.text = 'Otro';
	opt.value= 'o';
	pprecio.add(opt,null);

	$("#preca_"+ind).replaceWith(pprecio);
}

function del_itpfac(id){
	id = id.toString();
	$('#tr_itpfac_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigoa_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ventas/spre/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			//id='0';
			$('#codigoa_'+id).val(ui.item.codigo);
			$('#desca_'+id).val(ui.item.descrip);
			$('#precio1_'+id).val(ui.item.base1);
			$('#precio2_'+id).val(ui.item.base2);
			$('#precio3_'+id).val(ui.item.base3);
			$('#precio4_'+id).val(ui.item.base4);
			$('#itiva_'+id).val(ui.item.iva);
			$('#sinvtipo_'+id).val(ui.item.tipo);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#itcosto_'+id).val(ui.item.pond);
			$('#itpvp_'+id).val(ui.item.ultimo);
			$('#cana_'+id).val('1');
			$('#cana_'+id).focus();
			$('#cana_'+id).select();

			var arr  = $('#preca_'+ind);
			var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
			cdropdown(id);
			//cdescrip(id);
			jQuery.each(arr, function() { this.selectedIndex=tipo; });
			importe(id);
			totalizar();
		}
	});
}
</script>
<?php } ?>
<table align='center' width="95%">
	<tr>
<?php if ($form->_status=='show') { ?>
		<td>
		<a href="#" onclick="window.open('<?php echo base_url() ?>formatos/verhtml/PFAC/<?php echo $form->numero->value ?>', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');" heigth="600" >
		<img src='<?php echo base_url() ?>images/html_icon.gif'></a>
		</td>
<?php } ?>
		<td align=right><?php echo $container_tr?></td>
	</tr>
</table>
<table align='center' width="95%">
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Documento</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->vd->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->vd->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?=$form->peso->label  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?=$form->peso->output ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Cliente</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output,$form->sclitipo->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->rifci->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->rifci->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->direc->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->direc->output ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td></tr></table>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px'>
		<table width='100%'>
			<tr id='__INPL__'>
				<td bgcolor='#7098D0'><strong>C&oacute;digo</strong></td>
				<td bgcolor='#7098D0'><strong>Descripci&oacute;n</strong></td>
				<td bgcolor='#7098D0'><strong>Cantidad</strong></td>
				<td bgcolor='#7098D0'><strong>Precio</strong></td>
				<td bgcolor='#7098D0'><strong>Importe</strong></td>
				<?php if($form->_status!='show') {?>
					<td  bgcolor='#7098D0'><strong>&nbsp;</strong></td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itpfac'];$i++) {
				$it_codigoa  = "codigoa_$i";
				$it_desca    = "desca_$i";
				$it_cana     = "cana_$i";
				$it_preca    = "preca_$i";
				$it_tota     = "tota_$i";
				$it_iva      = "itiva_$i";
				$it_peso     = "sinvpeso_$i";
				$it_tipo     = "sinvtipo_$i";
				$it_costo    = "itcosto_$i";
				$it_pvp      = "itpvp_$i";

				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}
				$pprecios .= $form->$it_iva->output;
				$pprecios .= $form->$it_peso->output;
				$pprecios .= $form->$it_tipo->output;
				$pprecios .= $form->$it_costo->output;
				$pprecios .= $form->$it_pvp->output;
			?>

			<tr id='tr_itpfac_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_codigoa->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_desca->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_preca->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_tota->output.$pprecios;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itpfac(<?=$i ?>);return false;'>Eliminar</a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
		</table>
		</div>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<table width='100%'>
			<tr>
				<td class="littletableheader" width='100'><?php echo $form->observa->label;    ?></td>
				<td class="littletablerow"    width='350'><?php echo $form->observa->output;   ?></td>
				<td class="littletableheader">           <?php echo $form->totals->label;  ?></td>
				<td class="littletablerow" align='right'><b id='totals_val'><?php echo nformat($form->totals->value); ?></b><?php echo $form->totals->output; ?></td>

			<tr></tr>	
				<td class="littletableheader">&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->observ1->output;   ?></td>
				<td class="littletableheader"><?php echo $form->ivat->label;    ?></td>
				<td class="littletablerow" align='right'><b id='ivat_val'><?php echo nformat($form->ivat->value); ?></b><?php echo $form->ivat->output; ?></td>
			<tr></tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td class="littletableheader">           <?php echo $form->totalg->label;  ?></td>
				<td class="littletablerow" align='right' style='font-size:18px;font-weight: bold'><b id='totalg_val'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
			</tr>
		</table>
		</fieldset>

		<?php echo $form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>