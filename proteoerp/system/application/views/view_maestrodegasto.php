<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php 
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
?>
<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table border=0 width="100%">
	<tr>
		<td align='right' ><?php echo $container_tr; ?></td>
	</tr>
	<tr>
		<td>
			<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;'>
			<legend class="subtitulotabla" style='color: #114411;'>Gasto</legend>
			<table border=0 width="100%">
			<tr>
				<td width="140" class="littletableheader"><?php echo $form->codigo->label  ?></td>
				<td class="littletablerow" ><?php echo $form->codigo->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->descrip->label ?></td>
				<td  class="littletablerow"><?php echo $form->descrip->output ?></td>
			</tr>	
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label ?></td>
				<td  class="littletablerow"><?php echo $form->tipo->output?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->grupo->label    ?></td>
				<td  class="littletablerow"><?php echo $form->grupo->output?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->medida->label  ?></td>
				<td class="littletablerow"><?php echo $form->medida->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->cuenta->label ?></td>
				<td  class="littletablerow" ><?php echo $form->cuenta->output." "; ?>
				<?php
					if ( $form->_status == 'show' ) {
						$mSQL = "SELECT descrip FROM cpla WHERE codigo='".trim($form->cuenta->output)."'";
						echo $this->datasis->dameval($mSQL);
					}
				?>
				</td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;'>
			<legend class="subtitulotabla" style='color: #114411;'>Costos</legend>
			<table style="height: 100%;width: 100%" >
				<tr>
					<td width="120" class="littletableheader"><?php echo $form->iva->label   ?></td>
					<td class="littletablerow"><?php echo $form->iva->output  ?></td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->ultimo->label ?></td>
					<td class="littletablerow"><?php echo $form->ultimo->output ?></td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->promedio->label ?></td>
					<td class="littletablerow"><?php echo $form->promedio->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td  valign="top">	
			<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;'>
			<legend class="subtitulotabla" style='color: #114411;'>Existencias</legend>
			<table style="height: 100%;width: 100%">
				<tr>
					<td class="littletableheader"><?php echo $form->fraxuni->label  ?></td>
					<td class="littletablerow"><?php echo $form->fraxuni->output ?></td>
				</tr>
				<tr>
					<td width="160" class="littletableheader"> <?php echo $form->minimo->label ?> </td>
					<td class="littletablerow"> <?php echo $form->minimo->output ?> </td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->maximo->label  ?></td>
					<td class="littletablerow"><?php echo $form->maximo->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;'>
			<legend class="subtitulotabla" style='color: #114411;'>Cantidad Actual</legend>
			<table style="height: 100%;width: 100%" >
				<tr>
					<td class="littletableheader"><?php echo $form->unidades->label  ?></td>
					<td class="littletablerow"><?php echo $form->unidades->output ?></td>
					<td class="littletableheader"> <?php echo $form->fraccion->label ?> </td>
					<td class="littletablerow"> <?php echo $form->fraccion->output ?> </td>
				</tr>
			</table>
		</td>
	</tr>
        <?php if ( $this->datasis->traevalor('PAIS') == 'COLOMBIA' ) { ?>
	<tr>
		<td colspan='2'>
			<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;' >
			<legend class="subtitulotabla" style='color: #114411;' >Retencion</legend>
			<table style="height: 100%;width: 100%" >
				<tr>
					<td class="littletableheader"><?php echo $form->rica->label   ?></td>
					<td class="littletablerow"><?php echo $form->rica->output  ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<?php }; ?>
	<tr>
		<td valign="top" colspan='2'><?php echo $form->almacenes->output ?>	</td>
	</tr>
</table>
<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
<?php 
	//foreach (get_object_vars($form) as $label=>$tiene)
	//	echo "$label => $tiene <br>";
	//echo '<pre>';print_r($form->grupo->request);echo '</pre>'
?>