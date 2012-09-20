<?php
class Ppro extends Controller {
	var $mModulo='PPRO';
	var $titp='Pago a Proveedor';
	var $tits='Pago a Proveedor';
	var $url ='finanzas/ppro/';

	function Ppro(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('sprm','preabono') ) {
			$this->db->simple_query('ALTER TABLE sprm ADD preabono DECIMAL(17,2) NULL DEFAULT 0 AFTER causado ');
		};
		if ( !$this->datasis->iscampo('sprm','preppago') ) {
			$this->db->simple_query('ALTER TABLE sprm ADD preppago DECIMAL(17,2) NULL DEFAULT 0 AFTER preabono ');
		};
		$this->datasis->modintramenu( 900, 600, 'finanzas/ppro' );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '
<script type="text/javascript">
jQuery("#a1").click( function(){
	var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
	if (id)	{
		var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
		window.open(\''.base_url().'reportes/ver/SPRMECU/SPRM/\'+ret.cod_prv, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
	} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
});

$(function() {
	$("#dialog:ui-dialog").dialog( "destroy" );
	var mId = 0;
	var montotal = 0;
	var ffecha = $("#ffecha");
	var grid = jQuery("#newapi'.$param['grids'][0]['gridname'].'");
	var s;
	var allFields = $( [] ).add( ffecha );
	
	var tips = $( ".validateTips" );

	s = grid.getGridParam(\'selarrrow\'); 
	$( "input:submit, a, button", ".otros" ).button();

	$( "#preabono" ).click(function() {
		var id     = jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
		if (id)	{
			var ret    = $("#newapi'.$param['grids'][0]['gridname'].'").getRowData(id);  
			mId = id;
			$.post("'.base_url().'finanzas/ppro/formapabono/"+id, function(data){
				$("#fabono").html("");
				$("#fpreabono").html(data);
			});
			$( "#fpreabono" ).dialog( "open" );
			
		} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
	});

	$( "#fpreabono" ).dialog({
		autoOpen: false, height: 470, width: 790, modal: true,
		buttons: {
			"Aprobar Pago": function() {
				var bValid = true;
				var rows = $("#aceptados").jqGrid("getGridParam","data");
				var paras = new Array();
				for(var i=0;i < rows.length; i++){
					var row=rows[i];
					paras.push($.param(row));
				}
				// Coloca el Grid en un input
				$("#fgrid").val(JSON.stringify(paras));
				allFields.removeClass( "ui-state-error" );
				if ( bValid ) {
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url:"'.site_url("finanzas/ppro/pabono").'",
						data: $("#abonopforma").serialize(),
						success: function(r,s,x){
							var res = $.parseJSON(r);
							if ( res.status == "A"){
								alert(res.mensaje);
								grid.trigger("reloadGrid");
								window.open(\''.base_url().'reportes/ver/SPRMPRE/\'+res.id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
								$( "#fpreabono" ).dialog( "close" );
								return [true, a ];
							} else {
								apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
							}
						}
					});
				}
			},
			Cancel: function() { $( this ).dialog( "close" ); }
		},
		close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
	});

	$( "#abonos" ).click(function() {
		var id     = jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
		if (id)	{
			var ret    = $("#newapi'.$param['grids'][0]['gridname'].'").getRowData(id);  
			mId = id;
			$.post("'.base_url().'finanzas/ppro/formaabono/"+id, function(data){
				$("#fpreabono").html("");
				$("#fabono").html(data);
			});
			$( "#fabono" ).dialog( "open" );
			
		} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
	});

	$( "#fabono" ).dialog({
		autoOpen: false, height: 470, width: 790, modal: true,
		buttons: {
			"Abonar": function() {
				var bValid = true;
				var rows = $("#abonados").jqGrid("getGridParam","data");
				var paras = new Array();
				for(var i=0;i < rows.length; i++){
					var row=rows[i];
					paras.push($.param(row));
				}
				allFields.removeClass( "ui-state-error" );
				if ( bValid ) {
					// Coloca el Grid en un input
					$("#fgrid").val(JSON.stringify(paras));
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url:"'.site_url("finanzas/ppro/abono").'",
						data: $("#abonoforma").serialize(),
						success: function(r,s,x){
							var res = $.parseJSON(r);
							if ( res.status == "A"){
								apprise(res.mensaje);
								grid.trigger("reloadGrid");
								window.open(\''.base_url().'formato/ver/PPROABB/\'+res.id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
								$( "#fabono" ).dialog( "close" );
								return [true, a ];
							} else {
								apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
							}
						}
					});
				}
			},
			Cancel: function() { $( this ).dialog( "close" ); }
		},
		close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
	});


	$( "#ncredito" ).click(function() {
		var id     = jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
		if (id)	{
			var ret    = $("#newapi'.$param['grids'][0]['gridname'].'").getRowData(id);  
			mId = id;
			$.post("'.base_url().'finanzas/ppro/formancredito/"+id, function(data){
				$("#fpreabono").html("");
				$("#fabono").html("");
				$("#fncredito").html(data);
			});
			$( "#fncredito" ).dialog( "open" );
			
		} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
	});

	$( "#fncredito" ).dialog({
		autoOpen: false, height: 470, width: 790, modal: true,
		buttons: {
			"Abonar": function() {
				var bValid = true;
				var rows = $("#abonados").jqGrid("getGridParam","data");
				var paras = new Array();
				for(var i=0;i < rows.length; i++){
					var row=rows[i];
					paras.push($.param(row));
				}
				allFields.removeClass( "ui-state-error" );
				if ( bValid ) {
					// Coloca el Grid en un input
					$("#fgrid").val(JSON.stringify(paras));
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url:"'.site_url("finanzas/ppro/ncredito").'",
						data: $("#ncreditoforma").serialize(),
						success: function(r,s,x){
							var res = $.parseJSON(r);
							if ( res.status == "A"){
								apprise(res.mensaje);
								grid.trigger("reloadGrid");
								window.open(\''.base_url().'formato/ver/PPROABB/\'+res.id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
								$( "#fabono" ).dialog( "close" );
								return [true, a ];
							} else {
								apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
							}
						}
					});
				}
			},
			Cancel: function() { $( this ).dialog( "close" ); }
		},
		close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
	});

});


</script>
';

/*
	$( "#borrar" ).click(function() {
		var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
		var m = "";
		if (id)	{
			$.prompt( "Eliminar Registro? ",{
					buttons: { Borrar:true, Cancelar:false},
					callback: function(e,v,m,f){
						if (v == true) {
							$.get("'.base_url().$this->url.'bcajborra/"+id,
							function(data){
								alert(data);
							});
						}
					}
				}
			);
		} else { $.prompt("<h2>Por favor Seleccione un Movimiento</h2>");}
	});

*/


		$WestPanel = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<div class="otros">

<table id="west-grid" align="center">
	<tr><td>
		<div class="anexos"><table id="listados"></table></div></td>
	</tr><tr>
		<td><table id="otros"></table></td>
	</tr>
</table>

<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1"><a style="width:190px" href="#" id="a1">      <table><tr><td>'.img(array('src' => 'images/pdf_logo.gif', 'alt' => 'Formato PDF',  'title' => 'Formato PDF', 'border'=>'0')).'</td><td>Estado de Cuenta</td></tr></table></a></div></td>
	</tr><tr>
		<td><div class="tema1"><a style="width:190px" href="#" id="preabono"><table><tr><td>'.img(array('src' => 'images/checklist.png', 'alt' => 'Pre Abonar',  'title' => 'Pre Abonar', 'border'=>'0')).'</td><td>Preparar Pago</td></tr></table></a></div></td>
	</tr><tr>
		<td><div class="tema1"><a style="width:190px" href="#" id="abonos"> <table><tr><td>'.  img(array('src' => 'images/check.png', 'alt' => 'Abonos',  'title' => 'Abonos', 'border'=>'0')).'</td><td>Pagar o Abonar</td></tr></table></a></div></td>
	</tr><tr>
		<td><div class="tema1"><a style="width:190px" href="#" id="ncredito"><table><tr><td>'.  img(array('src' => 'images/star.png', 'alt' => 'Nota de Credito',  'title' => 'Nota de Credito', 'border'=>'0')).'</td><td>Notas de Credito</td></tr></table></a></div></td>
	</tr>

</table>
</div>
</div> <!-- #LeftPane -->
';


		$SouthPanel = '
<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
<p>'.$this->datasis->traevalor('TITULO1').'</p>
</div> <!-- #BottomPanel -->

<div id="fpreabono" title="Autorizar Abonos"></div>
<div id="fabono" title="Pagos y Abonos"></div>
<div id="fncredito" title="Notas de Creditos"></div>

';

		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('PPRO', 'JQ');
		$param['otros']    = $this->datasis->otros('PPRO', 'JQ');
		$param['temas']     = array('proteo','darkness','anexos1');
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('cod_prv');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));

		$grid->addField('rif');
		$grid->label('RIF');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('nombre');
		$grid->label('Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('saldo');
		$grid->label('Saldo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('cantidad');
		$grid->label('Cant.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('nueva');
		$grid->label('Nueva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('vieja');
		$grid->label('Vieja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('dias');
		$grid->label('Dias');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(false);
		$grid->setOndblClickRow('');
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata()
	{
		$grid = $this->jqdatagrid;
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('view_ppro');
		$response   = $grid->getData('view_ppro', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('sprv', $data);
				echo "Registro Agregado";
				logusu('SPRV',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('sprv', $data);
			logusu('SPRV',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM sprv WHERE id=$id ");
				logusu('SPRV',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//*********************************************************
	// Forma de Abono
	//
	function formapabono(){
		$id      = $this->uri->segment($this->uri->total_segments());
		$proveed = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=$id");

		$reg = $this->datasis->damereg("SELECT proveed, nombre, rif FROM sprv WHERE id=$id");

		$salida = '
<script type="text/javascript">
	var lastcell = 0;
	var totalapa = 0;
	var grid1 = jQuery("#aceptados");
	jQuery("#aceptados").jqGrid({
		datatype: "local",
		height: 250,
		colNames:["id","Tipo","Numero","Fecha","Vence","Monto","Saldo", "Faltante","Abonar","P.Pago"],
		colModel:[
			{name:"id",       index:"id",       width:10, hidden:true},
			{name:"tipo_doc", index:"tipo_doc", width:40},
			{name:"numero",   index:"numero",   width:90},
			{name:"fecha",    index:"fecha",    width:90},
			{name:"vence",    index:"vence",    width:90},
			{name:"monto",    index:"monto",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"saldo",    index:"saldo",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"faltan",   index:"faltan",   width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"abonar",   index:"abonar",   width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"ppago",    index:"ppago",    width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } }
		],
		cellEdit : true,
		cellsubmit : "clientArray",
		afterSaveCell: function (id,name,val,iRow,iCol){
			var row;
			if ( val=="" ){
				row = grid1.jqGrid(\'getRowData\', id );
				grid1.jqGrid("setCell",id,"abonar", Number(row["saldo"])-Number(row["faltan"]));
			}
			sumatot();
		}, 
		editurl: "clientArray"
	});
	
	var mefectos = [
';

		$mSQL  = "SELECT a.id, a.tipo_doc, a.numero, a.fecha, a.vence, a.monto, a.monto-a.abonos saldo, round(if(sum(d.devcant*d.costo) is null,0.00,sum(d.devcant*d.costo)),2) AS faltan, preabono abonar, preppago ppago ";
		$mSQL .= "FROM sprm a ";
		$mSQL .= 'LEFT JOIN scst   c ON a.transac=c.transac AND a.tipo_doc=c.tipo_doc AND a.cod_prv=c.proveed ';
		$mSQL .= 'LEFT JOIN itscst d ON c.control=d.control AND d.devcant is NOT NULL ';
		$mSQL .= "WHERE a.monto > a.abonos AND a.tipo_doc IN ('FC','ND','GI') AND a.cod_prv=".$this->db->escape($reg['proveed']);
		$mSQL .= ' GROUP BY a.cod_prv, a.tipo_doc, a.numero ';
		$mSQL .= "ORDER BY a.fecha ";
		
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0 ){
			foreach( $query->result() as $row ){
				$salida .= "\t\t".'{id:"'.$row->id.'",';
				$salida .= 'tipo_doc:"'.$row->tipo_doc.'",';
				$salida .= 'numero:"'.  $row->numero.'",';
				$salida .= 'fecha:"'.   $row->fecha.'",';
				$salida .= 'vence:"'.   $row->vence.'",';
				$salida .= 'monto:"'.   $row->monto.'",';
				$salida .= 'saldo:"'.   $row->saldo.'",';
				$salida .= 'faltan:"'.  $row->faltan.'",';
				$salida .= 'abonar:"'.  $row->abonar.'",';
				$salida .= 'ppago:"'.   $row->ppago.'"},'."\n";
			}
		}
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', banco, numcuent) banco ";
		$mSQL .= "FROM banc ";
		$mSQL .= "WHERE activo='S' ";
		$mSQL .= "ORDER BY (tbanco='CAJ'), codbanc ";
		$salida .= '
	];
	for(var i=0;i<=mefectos.length;i++) jQuery("#aceptados").jqGrid(\'addRowData\',i+1,mefectos[i]);
	$("#ffecha").datepicker({dateFormat:"dd/mm/yy"});
	function sumatot()
        { 
		var grid = jQuery("#aceptados");
		var s;
		var total = 0;
		var rowcells = new Array();
		var entirerow;
		s = grid.jqGrid("getGridParam","data");
		if(s.length)
		{
			for(var i=0; i< s.length; i++)
			{
				entirerow = s[i];
				if ( Number(entirerow["abonar"])>Number(entirerow["saldo"]) ){
					grid.jqGrid("setCell",s[i]["id"],"abonar", entirerow["saldo"]);
					total += Number(entirerow["saldo"]);
					total -= Number(entirerow["faltan"]);
				} else {
					total += Number(entirerow["abonar"]);
				}
				//Calcula el descuento
				if (  Number(entirerow["ppago"]) < 0 ){
					if (Number(entirerow["abonar"]) == 0 ){
						grid1.jqGrid("setCell",s[i]["id"],"abonar", Number(entirerow["saldo"])-Number(entirerow["faltan"]));
					}
					total -= Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100;
					grid.jqGrid("setCell",s[i]["id"],"ppago", Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100);
				} else {
					total -= Number(entirerow["ppago"]);
				}
			}
			total = Math.round(total*100)/100;	
			$("#grantotal").html("Total a Pagar: "+nformat(total,2));
			$("input#fmonto").val(total);
			montotal = total;
		} else {
			total = 0;
			$("#grantotal").html("Sin seleccion");
			$("input#fmonto").val(total);
			montotal = total;	
		}
	};
	sumatot();

</script>
	<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr><td>Codigo: '.$reg['proveed'].'</td><td>'.$reg['nombre'].'</td><td>RIF: '.$reg['rif'].'</td></tr></table></div>
	<p class="validateTips"></p>
	<form id="abonopforma">	
	<table width="80%" align="center">
	<tr>
		<td class="CaptionTD" align="right">Fecha</td>
		<td>&nbsp;'.date('d/m/Y').'</td>
		<td  class="CaptionTD"  align="right">Comprobante Externo</td>
		<td>&nbsp;<input name="fcomprob" id="fcomprob" type="text" value="" maxlengh="6" size="8"  /></td>
	</tr>
	</table>
	<input id="fmonto"   name="fmonto"   type="hidden">
	<input id="fsele"    name="fsele"    type="hidden">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="fgrid"    name="fgrid"    type="hidden">
	<br>
	<center><table id="aceptados"><table></center>
	<table width="100%">
	<tr>
		<td align="center"><div id="grantotal" style="font-size:20px;font-weight:bold">Monto a pagar: 0.00</div></td>
	</tr>
	</table>
	</form>
';


		echo $salida;
	}


	//*********************************************************
	// Forma de Abono
	//
	function formaabono(){
		$id      = $this->uri->segment($this->uri->total_segments());
		$proveed = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=$id");

		$reg = $this->datasis->damereg("SELECT proveed, nombre, rif FROM sprv WHERE id=$id");

		$salida = '

<script type="text/javascript">
	var lastcell = 0;
	var totalapa = 0;
	var grid1 = jQuery("#abonados");
	jQuery("#abonados").jqGrid({
		datatype: "local",
		height: 240,
		colNames:["id","Tipo","Numero","Fecha","Vence","Monto","Saldo", "Abonar","P.Pago"],
		colModel:[
			{name:"id",       index:"id",       width:10, hidden:true},
			{name:"tipo_doc", index:"tipo_doc", width:40},
			{name:"numero",   index:"numero",   width:90},
			{name:"fecha",    index:"fecha",    width:90},
			{name:"vence",    index:"vence",    width:90},
			{name:"monto",    index:"monto",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"saldo",    index:"saldo",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"abonar",   index:"abonar",   width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"ppago",    index:"ppago",    width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } }
		],
		cellEdit : true,
		cellsubmit : "clientArray",
		afterSaveCell: function (id,name,val,iRow,iCol){
			var row;
			if ( val=="" ){
				row = grid1.jqGrid(\'getRowData\', id );
				grid1.jqGrid("setCell",id,"abonar", Number(row["saldo"]));
			}
			sumabo();
		}, 
		editurl: "clientArray"
	});
	
	var mefectos = [
';

		$mSQL  = "SELECT a.id, a.tipo_doc, a.numero, a.fecha, a.vence, a.monto, a.monto-a.abonos saldo, preabono abonar, preppago ppago ";
		$mSQL .= "FROM sprm a ";
		$mSQL .= 'LEFT JOIN scst   c ON a.transac=c.transac AND a.tipo_doc=c.tipo_doc AND a.cod_prv=c.proveed ';
		$mSQL .= 'LEFT JOIN itscst d ON c.control=d.control AND d.devcant is NOT NULL ';
		$mSQL .= "WHERE a.monto > a.abonos AND a.tipo_doc IN ('FC','ND','GI') AND a.cod_prv=".$this->db->escape($reg['proveed']);
		$mSQL .= ' GROUP BY a.cod_prv, a.tipo_doc, a.numero ';
		$mSQL .= "ORDER BY a.fecha ";
		
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0 ){
			foreach( $query->result() as $row ){
				$salida .= "\t\t".'{id:"'.$row->id.'",';
				$salida .= 'tipo_doc:"'.$row->tipo_doc.'",';
				$salida .= 'numero:"'.  $row->numero.'",';
				$salida .= 'fecha:"'.   $row->fecha.'",';
				$salida .= 'vence:"'.   $row->vence.'",';
				$salida .= 'monto:"'.   $row->monto.'",';
				$salida .= 'saldo:"'.   $row->saldo.'",';
				$salida .= 'abonar:"'.  $row->abonar.'",';
				$salida .= 'ppago:"'.   $row->ppago.'"},'."\n";
			}
		}
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', banco, numcuent) banco ";
		$mSQL .= "FROM banc ";
		$mSQL .= "WHERE activo='S' ";
		$mSQL .= "ORDER BY (tbanco='CAJ'), codbanc ";
		
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', TRIM(banco), IF(tbanco='CAJ',' ',numcuent) ) banco FROM banc WHERE activo='S' ORDER BY tbanco='CAJ', codbanc ";
		$cajas = $this->datasis->llenaopciones($mSQL, true, 'fcodbanc');

		
		$salida .= '
	];
	for(var i=0;i<=mefectos.length;i++) jQuery("#abonados").jqGrid(\'addRowData\',i+1,mefectos[i]);
	$("#ffecha").datepicker({dateFormat:"dd/mm/yy"});
	function sumabo()
        { 
		var grid = jQuery("#abonados");
		var s;
		var total = 0;
		var rowcells = new Array();
		var entirerow;
		s = grid.jqGrid("getGridParam","data");
		if(s.length)
		{
			for(var i=0; i< s.length; i++)
			{
				entirerow = s[i];
				if ( Number(entirerow["abonar"])>Number(entirerow["saldo"]) ){
					grid.jqGrid("setCell",s[i]["id"],"abonar", entirerow["saldo"]);
					total += Number(entirerow["saldo"]);
				} else {
					total += Number(entirerow["abonar"]);
				}
				//Calcula el descuento
				if (  Number(entirerow["ppago"]) < 0 ){
					if (Number(entirerow["abonar"]) == 0 ){
						grid1.jqGrid("setCell",s[i]["id"],"abonar", Number(entirerow["saldo"]));
					}
					total -= Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100;
					grid.jqGrid("setCell",s[i]["id"],"ppago", Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100);
				} else {
					total -= Number(entirerow["ppago"]);
				}
			}
			total = Math.round(total*100)/100;	
			$("#grantotal").html("Total a Pagar: "+nformat(total,2));
			$("input#fmonto").val(total);
			montotal = total;
		} else {
			total = 0;
			$("#grantotal").html("Sin seleccion");
			$("input#fmonto").val(total);
			montotal = total;	
		}
	};
	sumabo();

</script>
	<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr><td>Codigo: '.$reg['proveed'].'</td><td>'.$reg['nombre'].'</td><td>RIF: '.$reg['rif'].'</td></tr></table></div>
	<p class="validateTips"></p>
	<form id="abonoforma">	
	<table width="90%" align="center" border="0">
	<tr>
		<td class="CaptionTD" align="right">Banco/Caja</td>
		<td>&nbsp;'.$cajas.'</td>

		<td class="CaptionTD" align="right">Tipo</td>
		<td>&nbsp;<select name="ftipo" id="ftipo" value="CH"><option value="CH">Cheque</option><option value="ND">Nota debito</option> </select></td>

		<td  class="CaptionTD"  align="right">Numero</td>
		<td>&nbsp;<input name="fcomprob" id="fcomprob" type="text" value="" maxlengh="12" size="12"  /></td>
	<tr>
		<td class="CaptionTD" align="right">Beneficiario:</td>
		<td colspan="3">&nbsp;<input name="fbenefi" id="fbenefi" type="text" value="" maxlengh="60" size="50"  /></td>
		<td class="CaptionTD" align="right">Fecha</td>
		<td>&nbsp;<input name="ffecha" id="ffecha" maxlength="10" size="10" value=\''.date('d/m/Y').'\'/></td>
	</tr>
	</tr>
	</table>
	<input id="fmonto"   name="fmonto"   type="hidden">
	<input id="fsele"    name="fsele"    type="hidden">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="fgrid"    name="fgrid"    type="hidden">
	<br>
	<center><table id="abonados"><table></center>
	<table width="100%">
	<tr>
		<td align="center"><div id="grantotal" style="font-size:20px;font-weight:bold">Monto a pagar: 0.00</div></td>
	</tr>
	</table>
	</form>
';


		echo $salida;
	}

//	function abonoguarda(){
//		echo 'a' ;
//	}

	//*********************************************************
	// Forma de Notas de Credito
	//
	function formancredito(){
		$id      = $this->uri->segment($this->uri->total_segments());
		$proveed = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=$id");

		$reg = $this->datasis->damereg("SELECT proveed, nombre, rif FROM sprv WHERE id=$id");

		$salida = '

<script type="text/javascript">
	var lastcell = 0;
	var totalapa = 0;
	var grid1 = jQuery("#abonados");
	jQuery("#abonados").jqGrid({
		datatype: "local",
		height: 240,
		colNames:["id","Tipo","Numero","Fecha","Vence","Monto","Saldo", "Abonar","P.Pago"],
		colModel:[
			{name:"id",       index:"id",       width:10, hidden:true},
			{name:"tipo_doc", index:"tipo_doc", width:40},
			{name:"numero",   index:"numero",   width:90},
			{name:"fecha",    index:"fecha",    width:90},
			{name:"vence",    index:"vence",    width:90},
			{name:"monto",    index:"monto",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"saldo",    index:"saldo",    width:80, align:"right", edittype:"text", editable:false, formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"abonar",   index:"abonar",   width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } },
			{name:"ppago",    index:"ppago",    width:80, align:"right", edittype:"text", editable:true,  formatter: "number", formatoptions: {label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 } }
		],
		cellEdit : true,
		cellsubmit : "clientArray",
		afterSaveCell: function (id,name,val,iRow,iCol){
			var row;
			if ( val=="" ){
				row = grid1.jqGrid(\'getRowData\', id );
				grid1.jqGrid("setCell",id,"abonar", Number(row["saldo"]));
			}
			sumabo();
		}, 
		editurl: "clientArray"
	});
	
	var mefectos = [
';

		$mSQL  = "SELECT a.id, a.tipo_doc, a.numero, a.fecha, a.vence, a.monto, a.monto-a.abonos saldo, preabono abonar, preppago ppago ";
		$mSQL .= "FROM sprm a ";
		$mSQL .= 'LEFT JOIN scst   c ON a.transac=c.transac AND a.tipo_doc=c.tipo_doc AND a.cod_prv=c.proveed ';
		$mSQL .= 'LEFT JOIN itscst d ON c.control=d.control AND d.devcant is NOT NULL ';
		$mSQL .= "WHERE a.monto > a.abonos AND a.tipo_doc IN ('FC','ND','GI') AND a.cod_prv=".$this->db->escape($reg['proveed']);
		$mSQL .= ' GROUP BY a.cod_prv, a.tipo_doc, a.numero ';
		$mSQL .= "ORDER BY a.fecha ";
		
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0 ){
			foreach( $query->result() as $row ){
				$salida .= "\t\t".'{id:"'.$row->id.'",';
				$salida .= 'tipo_doc:"'.$row->tipo_doc.'",';
				$salida .= 'numero:"'.  $row->numero.'",';
				$salida .= 'fecha:"'.   $row->fecha.'",';
				$salida .= 'vence:"'.   $row->vence.'",';
				$salida .= 'monto:"'.   $row->monto.'",';
				$salida .= 'saldo:"'.   $row->saldo.'",';
				$salida .= 'abonar:"'.  $row->abonar.'",';
				$salida .= 'ppago:"'.   $row->ppago.'"},'."\n";
			}
		}
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', banco, numcuent) banco ";
		$mSQL .= "FROM banc ";
		$mSQL .= "WHERE activo='S' ";
		$mSQL .= "ORDER BY (tbanco='CAJ'), codbanc ";
		
		//$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', TRIM(banco), IF(tbanco='CAJ',' ',numcuent) ) banco FROM banc WHERE activo='S' ORDER BY tbanco='CAJ', codbanc ";
		//$cajas = $this->datasis->llenaopciones($mSQL, true, 'fcodbanc');

		
		$salida .= '
	];
	for(var i=0;i<=mefectos.length;i++) jQuery("#abonados").jqGrid(\'addRowData\',i+1,mefectos[i]);
	$("#ffecha").datepicker({dateFormat:"dd/mm/yy"});
	function sumabo()
        { 
		var grid = jQuery("#abonados");
		var s;
		var total = 0;
		var rowcells = new Array();
		var entirerow;
		s = grid.jqGrid("getGridParam","data");
		if(s.length)
		{
			for(var i=0; i< s.length; i++)
			{
				entirerow = s[i];
				if ( Number(entirerow["abonar"])>Number(entirerow["saldo"]) ){
					grid.jqGrid("setCell",s[i]["id"],"abonar", entirerow["saldo"]);
					total += Number(entirerow["saldo"]);
				} else {
					total += Number(entirerow["abonar"]);
				}
				//Calcula el descuento
				if (  Number(entirerow["ppago"]) < 0 ){
					if (Number(entirerow["abonar"]) == 0 ){
						grid1.jqGrid("setCell",s[i]["id"],"abonar", Number(entirerow["saldo"]));
					}
					total -= Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100;
					grid.jqGrid("setCell",s[i]["id"],"ppago", Number(entirerow["abonar"])*Math.abs(Number(entirerow["ppago"]))/100);
				} else {
					total -= Number(entirerow["ppago"]);
				}
			}
			total = Math.round(total*100)/100;	
			$("#grantotal").html("Total a Pagar: "+nformat(total,2));
			$("input#fmonto").val(total);
			montotal = total;
		} else {
			total = 0;
			$("#grantotal").html("Sin seleccion");
			$("input#fmonto").val(total);
			montotal = total;	
		}
	};
	sumabo();

</script>
	<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr><td>Codigo: '.$reg['proveed'].'</td><td>'.$reg['nombre'].'</td><td>RIF: '.$reg['rif'].'</td></tr></table></div>
	<p class="validateTips"></p>
	<form id="abonoforma">	
	<table width="90%" align="center" border="0">
	<tr>
		<td class="CaptionTD" align="right">Banco/Caja</td>
		<td>&nbsp;</td>

		<td class="CaptionTD" align="right">Tipo</td>
		<td>&nbsp;<select name="ftipo" id="ftipo" value="CH"><option value="CH">Cheque</option><option value="ND">Nota debito</option> </select></td>

		<td  class="CaptionTD"  align="right">Numero</td>
		<td>&nbsp;<input name="fcomprob" id="fcomprob" type="text" value="" maxlengh="12" size="12"  /></td>
	<tr>
		<td class="CaptionTD" align="right">Beneficiario:</td>
		<td colspan="3">&nbsp;<input name="fbenefi" id="fbenefi" type="text" value="" maxlengh="60" size="50"  /></td>
		<td class="CaptionTD" align="right">Fecha</td>
		<td>&nbsp;<input name="ffecha" id="ffecha" maxlength="10" size="10" value=\''.date('d/m/Y').'\'/></td>
	</tr>
	</tr>
	</table>
	<input id="fmonto"   name="fmonto"   type="hidden">
	<input id="fsele"    name="fsele"    type="hidden">
	<input id="fid"      name="fid"      type="hidden" value="'.$id.'">
	<input id="fgrid"    name="fgrid"    type="hidden">
	<br>
	<center><table id="abonados"><table></center>
	<table width="100%">
	<tr>
		<td align="center"><div id="grantotal" style="font-size:20px;font-weight:bold">Monto a pagar: 0.00</div></td>
	</tr>
	</table>
	</form>
';


		echo $salida;
	}



	function pabono(){
		$comprob   = $this->input->post('fcomprob');
		$fecha     = $this->input->post('ffecha');
		$grid      = $this->input->post('fgrid');
		$id        = $this->input->post('fid');
		$monto     = $this->input->post('fmonto');
		$fsele     = $this->input->post('fsele');
		$check     = 0;
		$meco      = json_decode($grid);

		foreach( $meco as $row ){
			parse_str($row,$linea[]);
		}
	
		$cod_prv = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=$id");
		foreach( $linea as $efecto ){
			//actualiza los movimientos
			$this->db->where('cod_prv',   $cod_prv);
			$this->db->where('numero',    $efecto['numero']);
			$this->db->where('tipo_doc',  $efecto['tipo_doc']);
			$this->db->where('fecha',     $efecto['fecha']);
			if ( $efecto['abonar'] == 0 ) $efecto['ppago'] = "0";
			$data = array("preabono"=>$efecto['abonar'], "preppago"=>$efecto['ppago'], "comprob"=>$comprob);
			$this->db->update('sprm', $data);
		}
		logusu('SPRM',"Aprobacion de pagos CREADO $cod_prv "+$grid);
		echo '{"status":"A","id":"'.$id.'","mensaje":"Aprobacion Guardada"}';
	}

	function abono(){
		$numche  = $this->input->post('fcomprob');
		$tipo_op = $this->input->post('ftipo');
		$benefi  = $this->input->post('fbenefi');
		$codbanc = $this->input->post('fcodbanc');
		$fecha   = $this->input->post('ffecha');
		$grid    = $this->input->post('fgrid');
		$id      = $this->input->post('fid');
		$monto   = $this->input->post('fmonto');
		$fsele   = $this->input->post('fsele');
		$check   = 0;
		$meco    = json_decode($grid);

		//Convierte la fecha a YYYYmmdd
		$fecha = substr($fecha,6,4).substr($fecha,3,2).substr($fecha,0,2);

		// Validacion
		if ( $codbanc == '' ) {
			echo '{"status":"E","id":"'.$id.'" ,"mensaje":"Debe seleccionar un Banco o Caja "}';
			return;
		}
		
		if ( $this->datasis->dameval("SELECT count(*) FROM banc WHERE codbanc=".$this->db->escape($codbanc))==0 )
		{
			echo '{"status":"E","id":"'.$id.'" ,"mensaje":"Debe seleccionar un Banco o Caja "}';
			return;	
		}
		
		$tbanco = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=".$this->db->escape($codbanc));
		
		if ( $tbanco <> 'CAJ' && $numche == ''  ) {
			echo '{"status":"E","id":"'.$id.'" ,"mensaje":"Falta colocar el numero de Documento"}';
			return;
		}
		foreach( $meco as $row ){
			parse_str($row,$linea[]);
		}
		$cod_prv = $this->datasis->dameval("SELECT proveed FROM sprv WHERE id=$id");
		$nombre  = $this->datasis->dameval("SELECT nombre  FROM sprv WHERE id=$id");

		$totalab  = 0;
		$ppago    = 0;
		$impuesto = 0;
		$observa1 = 'ABONA A: ';
		$observa2 = '';
		$mTempo = "SELECT impuesto FROM sprm WHERE cod_prv=".$this->db->escape($cod_prv);
		foreach( $linea as $efecto ){
			if ($efecto['abonar'] > 0 ){
				$totalab  += $efecto['abonar'] - $efecto['ppago'];
				$ppago    += $efecto['ppago'];
				$observa1 .= $efecto['tipo_doc'].$efecto['numero'].', ';
				$impuesto += $efecto['abonar']*$this->datasis->dameval($mtempo." AND tipo_doc='".$efecto['tipo_doc']."' AND numero='".$efecto['numero']."'" )/$efecto['monto'];
			}
		}
		
		$observa2 = '';
		if ( strlen($observa1)>50) {
			$observa2 = substr($observa1, 49);
			$observa1 = substr($observa1, 0, 50);
		}
		
		if ( $totalab <= 0) {
			echo '{"status":"E","id":"'.$id.'" ,"mensaje":"Seleccione los efectos a abonar"}';
			return;
		}

		//Crea el Abono
		$transac  = $this->datasis->prox_sql("ntransa",8);
		$mnroegre = $this->datasis->prox_sql("nroegre",8);
		$tipo_doc = 'AB';
		$xnumero  = $this->datasis->prox_sql("num_ab",8);
		$mcontrol = $this->datasis->prox_sql("nsprm",8);

		$data = array();
		$data["tipo_doc"] = $tipo_doc;
		$data["numero"]   = $xnumero;
		$data["cod_prv"]  = $cod_prv;
		$data["nombre"]   = $nombre;
		$data["fecha"]    = $fecha;
		$data["monto"]    = $totalab;
		$data["impuesto"] = $impuesto;
		$data["vence"]    = $fecha;
		$data["observa1"] = $observa1;
		$data["observa2"] = $observa2;
		
		$data["banco"]    = $codbanc;
		$data["tipo_op"]  = $tipo_op;
		$data["numche"]   = $numche;
		$data["benefi"]   = $benefi;
		$data["reten"]    = 0;
		$data["reteiva"]  = 0;
		$data["ppago"]    = $ppago ;
		$data["control"]  = $mcontrol ;
		$data["cambio"]   = 0 ;
		$data["nfiscal"]  = '' ;
		$data["mora"]     = 0 ;

		$data["comprob"]  = '' ;
		$data["abonos"]   = $totalab ;

		$data['usuario']  = $this->secu->usuario();
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['transac']  = $transac;

		$this->db->insert('sprm',$data);
		$idab = $this->db->insert_id();

		// Si tiene prontopago genera la NC
		if ( $ppago > 0 ){
			$mnumero   = $this->datasis->prox_sql("num_nc",8);
			$mcontrol  = $this->datasis->prox_sql("nsprm",8);
			$mcdppago  = $mcontrol;

			$data = array();
			$data["tipo_doc"] =  "NC";
			$data["numero"]   =   $mnumero;
			$data["cod_prv"]  =   $cod_prv;
			$data["nombre"]   =   $nombre;
			$data["fecha"]    =   $fecha;
			$data["monto"]    =   $ppago;

			$data["impuesto"] = round($ppago*$impuesto/$totalab,2) ;

			$data["vence"]    = $fecha;
			$data["observa1"] = 'DESC. P.PAGO A '.$tipo_doc.$numero;
			$data["codigo"]   = 'DESPP';
			$data["descrip"]  = 'DESCUENTO PRONTO PAGO';
			$data["abonos"]   = $ppago;
			$data["control"]  = $mcontrol;

			$data['usuario']  = $this->secu->usuario();
			$data['estampa']  = date('Ymd');
			$data['hora']     = date('H:i:s');
			$data['transac']  = $transac;

			$this->db->insert('sprm',$data);

			// DEBE DEVOLVER EL IVA EN CASO DE CONTRIBUYENTE
			/*
			IF TRAEVALOR("CONTRIBUYENTE") = 'ESPECIAL'
				
			ENDIF
			*/
		}
		
		//Crea Movimiento en Bancos
		$mndebito = '';
	
		if ( $tbanco == 'CAJ' ) $tipo_op = 'ND';
		if ( $tipo_op == 'ND' && $tbanco != 'CAJ' ) $mndebito = $this->datasis->prox_sql("ndebito",8);
		
		$data = array();

		$data["codbanc"]  = $codbanc;

		$mtempo = " FROM banc WHERE codbanc=".$this->db->escape($codbanc);
		$data["numcuent"] = $this->datasis->dameval("SELECT numcuent ".$mtempo);
		$data["banco"]    = $this->datasis->dameval("SELECT banco    ".$mtempo);
		$data["saldo"]    = $this->datasis->dameval("SELECT saldo    ".$mtempo);
		
		$data["fecha"]    = $fecha;
		$data["tipo_op"]  = $tipo_op;
		$data["numero"]   = $numche;

		$data["concepto"] = $observa1;
		$data["concep2"]  = $observa2;
		$data["monto"]    = $totalab;
		$data["clipro"]   = 'P' ;

		$data["codcp"]    = $cod_prv;
		$data["nombre"]   = $nombre;
		$data["benefi"]   = $benefi;
		
		$data["negreso"]  = $mnroegre;
		$data["ndebito"]  = $mndebito;

		$data['usuario']  = $this->secu->usuario();
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['transac']  = $transac;

		$this->db->insert('bmov',$data);
		$this->datasis->actusal($codbanc, $fecha, $totalab);

		//IF UPPER(SUBSTR(XBENEFI,1,18)) != "TESORERIA NACIONAL"
		//	CARGAIDB(XBANCO, XFECHA, XMONTO, XNUMCHE , XNUMERO, mTRANSAC)
		//ENDIF

		foreach( $linea as $efecto ){
			if ( $efecto['abonar'] > 0 ) {
				// Guarda en itppro
				$data = array();
				$data["numppro"]  = $xnumero;
				$data["tipoppro"] = $tipo_doc;
				$data["cod_prv"]  = $cod_prv;
				$data["numero"]   = $efecto['numero'];
				$data["tipo_doc"] = $efecto['tipo_doc'];
				$data["fecha"]    = $fecha;
				$data["monto"]    = $efecto['numero'];
				$data["abono"]    = $efecto['abonar'];
				$data["breten"]   = 0;
				$data["creten"]   = '';
				$data["reten"]    = 0;
				$data["reteiva"]  = 0;
				$data["ppago"]    = 0;
				$data["cambio"]   = 0;
				$data["mora"]     = 0;

				$data['usuario']  = $this->secu->usuario();
				$data['estampa']  = date('Ymd');
				$data['hora']     = date('H:i:s');
				$data['transac']  = $transac;
				$this->db->insert('itppro',$data);
			
				// Actualiza sprm
				$data = array($efecto['abonar'], $efecto['tipo_doc'], $efecto['numero'], $cod_prv, $efecto['fecha']);
				$mSQL = "UPDATE sprm SET abonos=abonos+? WHERE tipo_doc=? AND numero=? AND cod_prv=? AND fecha=?";
				$this->db->query($mSQL, $data);
			}
		}
		logusu('PPRO',"Abono a proveedor CREADO Prov=$cod_prv  Numero=$xnumero Detalle=".$grid);
		echo '{"status":"A","id":"'.$idab.'" ,"mensaje":"Abono Guardado '.$codbanc.'"}';
	}


/*
class psprv extends Controller {
	var $titp='Pago a proveedores';
	var $tits='Pago a proveedor';
	var $url ='finanzas/psprv/';

	function psprv(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id('524',1);
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$sel=array('TRIM(a.cod_prv) AS cod_prv','b.nombre','SUM(a.monto-a.abonos) AS saldo');
		$filter->db->select($sel);
		$filter->db->from('sprm AS a');
		$filter->db->join('sprv AS b','a.cod_prv=b.proveed');
		$filter->db->where('a.monto > a.abonos');
		$filter->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		$filter->db->groupby('a.cod_prv');

		$filter->cod_prv = new inputField('Proveedor','cod_prv');
		$filter->cod_prv->rule      = 'max_length[5]';
		$filter->cod_prv->size      = 7;
		$filter->cod_prv->db_name   = 'a.cod_prv';
		$filter->cod_prv->maxlength = 5;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      = 'max_length[8]';
		$filter->nombre->size      = 10;
		$filter->nombre->db_name   = 'a.nombre';
		$filter->nombre->maxlength = 8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/<raencode><#cod_prv#></raencode>/create','<#cod_prv#>');

		$grid = new DataGrid('Seleccione el cliente');
		$grid->order_by('fecha','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Proveedor'  ,$uri,'cod_prv','align="left"');
		$grid->column_orderby('Nombre'     ,'nombre','nombre','align="left"');
		$grid->column_orderby('Saldo'      ,'<nformat><#saldo#></nformat>','saldo','align="right"');

		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($proveed){
		if(!$this->_exitesprv($proveed)) redirect($this->url.'filteredgrid');
		$cajero=$this->secu->getcajero();
		if(empty($cajero)) show_error('El usuario debe tener registrado un cajero para poder usar este modulo');

		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject('sprm');
		$do->rel_one_to_many('itppro', 'itppro', array(
			'tipo_doc'=>'tipoppro',
			'numero'  =>'numppro',
			'cod_prv' =>'cod_prv',
			'fecha'   =>'fecha')
		);
		$do->rel_one_to_many('bmov' , 'bmov' , array(
			'transac' =>'transac',
			'numero'  =>'numero',
			'tipo_op'=>'tipo_op',
			'fecha'   =>'fecha')
		);
		$do->order_by('itppro','itppro.fecha');

		$edit = new DataDetails('Pago a Proveedor', $do);
		$edit->back_url = site_url('finanzas/psprv/filteredgrid');
		$edit->set_rel_title('itppro', 'Efectos <#o#>');
		$edit->set_rel_title('bmov'  , 'Forma de pago <#o#>');

		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->cod_prv = new hiddenField('Proveedor','cod_prv');
		$edit->cod_prv->rule ='max_length[5]';
		$edit->cod_prv->size =7;
		$edit->cod_prv->insertValue=$proveed;
		$edit->cod_prv->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		$edit->tipo_doc->option('AB','Abono');
		$edit->tipo_doc->option('NC','Nota de credito');
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Ninguno');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecdoc = new dateonlyField('Fecha','fecdoc');
		$edit->fecdoc->size =10;
		$edit->fecdoc->maxlength =8;
		$edit->fecdoc->insertValue=date('Y-m-d');
		$edit->fecdoc->rule ='chfecha|required';

		$edit->monto = new inputField('Total','monto');
		$edit->monto->rule='max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;
		$edit->monto->type='inputhidden';

		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));
		$edit->fecha   = new autoUpdateField('fecha'   ,date('Ymd'), date('Ymd'));

		//************************************************
		//inicio detalle itppro
		//************************************************
		$i=0;
		$edit->detail_expand_except('itppro');
		$sel=array('a.tipo_doc','a.numero','a.fecha','a.monto','a.abonos','a.monto - a.abonos AS saldo', 'round(if(sum(d.devcant*d.costo) is null,0.00,sum(d.devcant*d.costo)),2) AS falta');
		$this->db->select($sel);
		$this->db->from('sprm AS a');
		$this->db->where('a.cod_prv',$proveed);
		$transac=$edit->get_from_dataobjetct('transac');
		if($transac!==false){
			$tipo_doc =$edit->get_from_dataobjetct('tipo_doc');
			$dbtransac=$this->db->escape($transac);
			$this->db->join('itppro AS b','a.tipo_doc = b.tipopsprv AND a.numero=b.numpsprv AND a.transac='.$dbtransac);
			$this->db->where('a.tipo_doc',$tipo_doc);
		}else{
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		}
		$this->db->join('scst   AS c', 'a.transac=c.transac AND a.tipo_doc=c.tipo_doc AND a.cod_prv=c.proveed','LEFT');
		$this->db->join('itscst AS d', 'c.control=d.control AND d.devcant is not null','LEFT');
		$this->db->group_by('a.cod_prv, a.tipo_doc, a.numero');
		$this->db->order_by('a.fecha');
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			$obj='cod_prv_'.$i;
			$edit->$obj = new autoUpdateField('cod_prv',$proveed,$proveed);
			$edit->$obj->rel_id  = 'itpsprv';
			$edit->$obj->ind     = $i;

			$obj='tipo_doc_'.$i;
			$edit->$obj = new inputField('Tipo_doc',$obj);
			$edit->$obj->db_name='tipo_doc';
			$edit->$obj->rel_id = 'itpsprv';
			$edit->$obj->rule='max_length[2]';
			$edit->$obj->insertValue=$row->tipo_doc;
			$edit->$obj->size =4;
			$edit->$obj->maxlength =2;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='numero_'.$i;
			$edit->$obj = new inputField('Numero',$obj);
			$edit->$obj->db_name='numero';
			$edit->$obj->rel_id = 'itpsprv';
			$edit->$obj->rule='max_length[8]';
			$edit->$obj->insertValue=$row->numero;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='fecha_'.$i;
			$edit->$obj = new dateonlyField('Fecha',$obj);
			$edit->$obj->db_name='fecha';
			$edit->$obj->rel_id = 'itpsprv';
			$edit->$obj->rule='chfecha';
			$edit->$obj->insertValue=$row->fecha;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='monto_'.$i;
			$edit->$obj = new inputField('Monto',$obj);
			$edit->$obj->db_name='monto';
			$edit->$obj->rel_id = 'itpsprv';
			$edit->$obj->rule='max_length[18]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->size =20;
			$edit->$obj->insertValue=$row->monto;
			$edit->$obj->maxlength =18;
			$edit->$obj->ind       = $i;
			$edit->$obj->showformat='decimal';
			$edit->$obj->type='inputhidden';

			$obj='saldo_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->saldo));
			$edit->$obj->ind = $i;

			$obj='falta_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->falta));
			$edit->$obj->ind = $i;


	        $obj='abono_'.$i;
			$edit->$obj = new inputField('Abono',$obj);
			$edit->$obj->db_name      = 'abono';
			$edit->$obj->rel_id       = 'itpsprv';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chabono[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onfocus      = 'itsaldo(this,'.round($row->saldo,2).');';

	        $obj='ppago_'.$i;
			$edit->$obj = new inputField('Pronto Pago',$obj);
			$edit->$obj->db_name      = 'ppago';
			$edit->$obj->rel_id       = 'itpsprv';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chppago[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onchange     = "itppago(this,'$i');";

			$i++;
		}
		//************************************************
		//fin de campos para detalle,inicio detalle2 bmov
		//************************************************
		$edit->banco = new dropdownField('Banco <#o#>', 'banco_<#i#>');
		$edit->banco->option('','Seleccionar');
		$edit->banco->options('SELECT "**" codbanc,"** PENDIENTE" banco FROM banc LIMIT 1 UNION ALL SELECT codbanc, CONCAT(codbanc," ",banco) banco  FROM banc ORDER BY codbanc');
		$edit->banco->db_name='banco';
		$edit->banco->rel_id ='bmov';
		$edit->banco->style  ='width:200px;';
		$edit->banco->rule   ='required';

		$edit->tipo_op = new  dropdownField('Tipo Operacion <#o#>', 'tipo_op_<#i#>');
		$edit->tipo_op->option('','Seleccionar');
		$edit->tipo_op->option('CH','Cheque');
		$edit->tipo_op->option('ND','Nota de Debito');
		$edit->tipo_op->db_name  = 'tipo';
		$edit->tipo_op->rel_id   = 'bmov';
		$edit->tipo_op->style    = 'width:160px;';
		$edit->tipo_op->rule     = 'required|enum[CH,ND]';
		$edit->tipo_op->insertValue='CH';

		$edit->bmovfecha = new dateonlyField('Fecha','bmovfecha_<#i#>');
		$edit->bmovfecha->rel_id   = 'bmov';
		$edit->bmovfecha->db_name  = 'fecha';
		$edit->bmovfecha->size     = 10;
		$edit->bmovfecha->maxlength= 8;
		$edit->bmovfecha->rule ='condi_required|chsitfecha|callback_chtipo[<#i#>]';

		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 12;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'bmov';
		$edit->numref->rule     = 'condi_required|callback_chtipo[<#i#>]';

		$edit->itmonto = new inputField('Monto <#o#>', 'itmonto_<#i#>');
		$edit->itmonto->db_name     = 'monto';
		$edit->itmonto->css_class   = 'inputnum';
		$edit->itmonto->rel_id      = 'bmov';
		$edit->itmonto->size        = 10;
		$edit->itmonto->rule        = 'condi_required|positive|callback_chmontosfpa[<#i#>]';
		$edit->itmonto->showformat  = 'decimal';
		$edit->itmonto->autocomplete= false;
		//************************************************
		// Fin detalle 2 (bmov)
		//************************************************

		$edit->buttons('save','undo','back','add');
		$edit->build();

		$conten['cana']  = $i;
		$conten['form']  = & $edit;
		$conten['title'] = heading('');
		$data['head']    = style('estilo.css');
		$data['head']   .= $this->rapyd->get_head();
		$data['script']  = script('jquery.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= phpscript('nformat.js');
		$data['content'] = $this->load->view('view_psprv.php', $conten,true);
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}

	function chsfpatipo($val){
		$tipo=$this->input->post('tipo_doc');
		if($tipo=='NC') {
			return true;
		}
		$this->validation->set_message('chsfpatipo', 'El campo %s es obligatorio');
		if(empty($val)){
			return false;
		}else{
			return true;
		}
	}

	function chfuturo($fecha){
		$fdoc=timestampFromInputDate($fecha);
		$fact=mktime();

		if($fdoc > $fact){
			$this->validation->set_message('chfuturo', 'No puede meter un efecto a futuro');
			return false;
		}
		return true;
	}

	function chtipo($val,$i){
		$tipo=$this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo!='EF'))
			return false;
		else
			return true;
	}

	function chmontosfpa($monto){
		$tipo   = $this->input->post('tipo_doc');
		if($tipo=='NC'){
			return true;
		}
		if(empty($monto) || $monto==0){
			$this->validation->set_message('chmontosfpa', "El campo %s es obligatorio");
			return false;
		}
		return true;
	}

	function chppago($monto,$i){
		$tipo   = $this->input->post('tipo_doc');
		if($tipo=='NC' && $monto>0){
			$this->validation->set_message('chppago', "No se puede hacer pronto pago cuando el tipo de documento es una nota de cr&eacute;dito");
			return false;
		}
		return true;
	}

	function chabono($monto,$i){
		$tipo   = $this->input->post('tipo_doc_'.$i);
		$ppago  = $this->input->post('ppago_'.$i);
		$numero = $this->input->post('numero_'.$i);
		$cod_prv= $this->input->post('cod_prv');
		$fecha  = human_to_dbdate($this->input->post('fecha_'.$i));

		$this->db->select(array('monto - abonos AS saldo'));
		$this->db->from('sprm');
		$this->db->where('tipo_doc',$tipo);
		$this->db->where('numero'  ,$numero);
		$this->db->where('fecha'   ,$fecha);
		$this->db->where('cod_prv' ,$cod_prv);

		$query = $this->db->get();
		$row   = $query->row();

		if ($query->num_rows() == 0) return false;
		$saldo = $row->saldo;

		if(($monto+$ppago)<=$saldo){
			return true;
		}else{
			$this->validation->set_message('chabono', "No se le puede abonar al efecto $tipo-$numero un monto mayor al saldo");
			return false;
		}
	}

	function cuenta($cliente){
		if(!$this->_exitescli($cliente)) redirect($this->url.'filterscli');

		$do = new DataObject('smov');
		$r1 = array('tipo_doc' => 'numpsprv' ,'numero'=>'numpsprv');
		$r2 = array('tipo_doc' => 'tipo_doc','numero'=>'numero' );

		$do->rel_many_to_many('smov', 'smov','itpsprv',$r1,$r2);
	}

	function _exitesprv($proveed){
		$dbsprv= $this->db->escape($proveed);
		$mSQL  = "SELECT COUNT(*) AS cana FROM sprv WHERE proveed=$dbsprv";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function _pre_insert($do){
		$proveed =$do->get('cod_prv');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$cod_prv = $do->get('cod_prv');
		$tipo_doc= $do->get('tipo_doc');
		$fecha   = $do->get('fecha');
		$itabono=$sfpamonto=$ppagomonto=0;

		$rrow    = $this->datasis->damerow('SELECT nombre,rif,direc1,direc2 FROM sprv WHERE proveed='.$this->db->escape($proveed));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('dire1' ,$rrow['dire1']);
			$do->set('dire2' ,$rrow['dire2']);
		}

		//Totaliza el abonado
		$rel='itppro';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono += $do->get_rel($rel, 'abono', $i);
			$pppago   = $do->get_rel($rel, 'ppago', $i);
			if(empty($pppago)){
				$do->set_rel($rel,'ppago',0,$i);
			}else{
				$ppagomonto += $do->get_rel($rel, 'ppago', $i);
			}
		}
		$itabono=round($itabono,2);

		//Totaliza lo pagado
		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpamonto+=$do->get_rel($rel, 'monto', $i);
		}
		$sfpamonto=round($sfpamonto,2);

		//Realiza las validaciones
		$cajero=$this->secu->getcajero();
		$this->load->library('validation');
		$rt=$this->validation->cajerostatus($cajero);
		if(!$rt){
			$do->error_message_ar['pre_ins']='El cajero usado ('.$cajero.') esta cerrado para esta fecha';
			return false;
		}

		if($tipo_doc=='NC'){
			$do->truncate_rel('bmov');
			if($itabono==0){
				$do->error_message_ar['pre_ins']='Si crea una nota de credito debe relacionarla con algun movimiento';
				return false;
			}
		}elseif($tipo_doc=='AN'){
			$do->truncate_rel('itppro');
			if($itabono!=0){
				$do->error_message_ar['pre_ins']='Un anticipo no puede estar relacionado con algun efecto, en tal caso seria un abono';
				return false;
			}else{
				$itabono=$sfpamonto;
			}
		}else{
			if(abs($sfpamonto-$itabono)>0.01){
				$do->error_message_ar['pre_ins']='El monto cobrado no coincide con el monto de la la transacci&oacute;n';
				return false;
			}
		}
		//fin de las validaciones
		$do->set('monto',$itabono);

		$dbcliente= $this->db->escape($cliente);
		$rowscli  = $this->datasis->damerow('SELECT nombre,dire11,dire12 FROM scli WHERE cliente='.$dbcliente);
		$do->set('nombre', $rowscli['nombre']);
		$do->set('dire1' , $rowscli['dire11']);
		$do->set('dire2' , $rowscli['dire12']);

		$transac  = $this->datasis->fprox_numero('ntransa');

		if($tipo_doc=='AB'){
			$mnum = $this->datasis->fprox_numero('nabcli');
		}elseif($tipo_doc=='GI'){
			$mnum = $this->datasis->fprox_numero('ngicli');
		}elseif($tipo_doc=='NC'){
			$mnum = $this->datasis->fprox_numero('npsprv');
		}else{
			$mnum = $this->datasis->fprox_numero('nancli');
		}
		$do->set('vence'  , $fecha);
		$do->set('numero' , $mnum);
		$do->set('transac', $transac);

		$rel='itpsprv';
		$observa=array();
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono = $do->get_rel($rel, 'abono'   , $i);
			$ittipo  = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero= $do->get_rel($rel, 'numero'  , $i);
			if(empty($itabono) || $itabono==0){
				$do->rel_rm($rel,$i);
			}else{
				$observa[]=$ittipo.$itnumero;
				$do->set_rel($rel, 'tipopsprv', $tipo_doc, $i);
				$do->set_rel($rel, 'cod_prv' , $cod_prv , $i);
				$do->set_rel($rel, 'estampa' , $estampa , $i);
				$do->set_rel($rel, 'hora'    , $hora    , $i);
				$do->set_rel($rel, 'usuario' , $usuario , $i);
				$do->set_rel($rel, 'transac' , $transac , $i);
				$do->set_rel($rel, 'mora'    , 0, $i);
				$do->set_rel($rel, 'reten'   , 0, $i);
				$do->set_rel($rel, 'cambio'  , 0, $i);
				$do->set_rel($rel, 'reteiva' , 0, $i);
			}
		}
		if(count($observa)>0){
			$observa='PAGA '.implode(',',$observa);
			$do->set('observa1' , substr($observa,0,50));
			if(strlen($observa)>50) $do->set('observa2' , substr($observa,50));
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo=$do->get_rel($rel, 'tipo_doc', $i);
			if($sfpatipo=='EF') $do->set_rel($rel, 'fecha' , $fecha , $i);

			$do->set_rel($rel,'estampa'  , $estampa , $i);
			$do->set_rel($rel,'hora'     , $hora    , $i);
			$do->set_rel($rel,'usuario'  , $usuario , $i);
			$do->set_rel($rel,'transac'  , $transac , $i);
			$do->set_rel($rel,'f_factura', $fecha   , $i);
			$do->set_rel($rel,'cod_prv'  ,$cliente  , $i);
			$do->set_rel($rel,'cobro'    ,$fecha    , $i);
			$do->set_rel($rel,'vendedor' ,$this->secu->getvendedor(),$i);
			$do->set_rel($rel,'cobrador' ,$this->secu->getcajero()  ,$i);
			$do->set_rel($rel,'almacen'  ,$this->secu->getalmacen() ,$i);
		}
		$this->ppagomonto=$ppagomonto;

		$do->set('mora'    ,0);
		$do->set('reten'   ,0);
		$do->set('cambio'  ,0);
		$do->set('reteiva' ,0);
		$do->set('ppago'   ,$ppagomonto);
		$do->set('codigo'  ,'NOCON');
		$do->set('descrip' ,'NOTA DE CONTABILIDAD');
		$do->set('vendedor', $this->secu->getvendedor());
		return true;
	}

	function _post_insert($do){
		$cliente  =$do->get('cod_prv');
		$dbcliente=$this->db->escape($cliente);

		$rel_id='itpsprv';
		$cana = $do->count_rel($rel_id);
		if($cana>0){
			if($this->ppagomonto>0){
				//Crea la NC por Pronto pago
				$mnumnc = $this->datasis->fprox_numero('npsprv');

				$dbdata=array();
				$dbdata['cod_prv']    = $cliente;
				$dbdata['nombre']     = $do->get('nombre');
				$dbdata['dire1']      = $do->get('dire1');
				$dbdata['dire2']      = $do->get('dire2');
				$dbdata['tipo_doc']   = 'NC';
				$dbdata['numero']     = $mnumnc;
				$dbdata['fecha']      = $do->get('fecha');
				$dbdata['monto']      = $this->ppagomonto;
				$dbdata['impuesto']   = 0;
				$dbdata['abonos']     = $this->ppagomonto;
				$dbdata['vence']      = $do->get('fecha');
				$dbdata['tipo_ref']   = 'AB';
				$dbdata['num_ref']    = $do->get('numero');
				$dbdata['observa1']   = 'DESCUENTO POR PRONTO PAGO';
				$dbdata['estampa']    = $do->get('estampa');
				$dbdata['hora']       = $do->get('hora');
				$dbdata['transac']    = $do->get('transac');
				$dbdata['usuario']    = $do->get('usuario');
				$dbdata['codigo']     = 'NOCON';
				$dbdata['descrip']    = 'NOTA DE CONTABILIDAD';
				$dbdata['fecdoc']     = $do->get('fecha');
				$dbdata['nroriva']    = '';
				$dbdata['emiriva']    = '';
				$dbdata['reten']      = 0;
				$dbdata['cambio']     = 0;
				$dbdata['mora']       = 0;

				$mSQL = $this->db->insert_string('smov', $dbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'psprv'); }

				$itdbdata=array();
				$itdbdata['cod_prv']  = $cliente;
				$itdbdata['numpsprv']  = $mnumnc;
				$itdbdata['tipopsprv'] = 'NC';
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['fecha']    = $do->get('fecha');
				$itdbdata['monto']    = $this->ppagomonto;
				$itdbdata['reten']    = 0;
				$itdbdata['cambio']   = 0;
				$itdbdata['mora']     = 0;

				unset($dbdata);
			}

			foreach($do->data_rel[$rel_id] AS $i=>$data){
				$tipo_doc = $data['tipo_doc'];
				$numero   = $data['numero'];
				$fecha    = $data['fecha'];
				$monto    = $data['abono'];
				$ppago    = (empty($data['ppago']))? 0: $data['ppago'];

				$dbtipo_doc = $this->db->escape($tipo_doc);
				$dbnumero   = $this->db->escape($numero  );
				$dbfecha    = $this->db->escape($fecha   );
				$dbmonto    = $monto+$ppago;

				$mSQL="UPDATE smov SET abonos=abonos+$dbmonto WHERE tipo_doc=$dbtipo_doc AND fecha=$dbfecha AND numero=$dbnumero AND cod_prv=$dbcliente";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'psprv'); }

				if($ppago > 0 ){
					$itdbdata['tipo_doc'] = $tipo_doc;
					$itdbdata['numero']   = $numero;
					$itdbdata['abono']    = $ppago;

					$mSQL = $this->db->insert_string('itpsprv', $itdbdata);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'psprv'); }
				}
			}
		}
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		return false;
	}
*/
}

?>