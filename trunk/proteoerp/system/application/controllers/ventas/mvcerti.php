<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//crucecuentas
class Mvcerti extends validaciones {
	var $mModulo='MVCERTI';
	var $titp='Certificados Mision Vivienda';
	var $tits='Certificados Mision Vivienda';
	var $url ='ventas/mvcerti/';
	var $data_type = null;
	var $data = null;
	 
	function mvcerti(){
		parent::Controller(); 
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->library("rapyd");
		$this->load->library('jqdatagrid');

		if (!$this->datasis->istabla('mvcerti')) {
			$mSQL = "
				CREATE TABLE mvcerti (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					cliente CHAR(5) NULL DEFAULT NULL COMMENT 'Codigo del Cliente',
					numero CHAR(32) NULL DEFAULT NULL COMMENT 'Numero de Certificado',
					fecha DATE NULL DEFAULT NULL COMMENT 'Fecha del certificado',
					obra VARCHAR(200) NULL DEFAULT NULL COMMENT 'Nombre de la Obra',
					status CHAR(1) NULL DEFAULT 'A' COMMENT 'Activo Cerrado',
					PRIMARY KEY (id),
					UNIQUE INDEX numero (numero),
					INDEX cliente (cliente)
				)
				COLLATE='latin1_swedish_ci'
				ENGINE=MyISAM
				ROW_FORMAT=DEFAULT
			";
			$this->db->simple_query($mSQL);
			
			$mSQL = "CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `view_mvcerti` AS
				select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra`
				from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`)))
				order by `a`.`id` desc";
				
			$this->db->simple_query($mSQL);
			
		}

		if (!$this->datasis->istabla('view_mvcerti')) {
			$mSQL = "CREATE ALGORITHM=UNDEFINED 
					DEFINER=`datasis`@`%` 
					SQL SECURITY INVOKER VIEW `view_mvcerti` AS 
					select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra` 
					from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`))) 
					order by `a`.`id` desc";
			$this->db->simple_query($mSQL);
		}
	}


	function index(){
		$this->datasis->modulo_id('13C',1);
		//redirect("ventas/mvcerti/filteredgrid");
		//$this->mvcertiextjs();
		redirect($this->url.'jqdatag');
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"imprimir",   "img"=>"images/pdf_logo.gif",  "alt" => 'Formato PDF', "label"=>""));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function consulmv(){
			mnumero=$("#numero").val();
			if(mnumero.length==0){
				alert("Debe introducir primero el numero de certificado");
			}else{
				mnumero=mnumero.toUpperCase();
				$("#numero").val(mnumero);
				window.open("'.site_url('ventas/mvcerti/traepdf/').'/"+mnumero,"CONSULTA MV","height=350,width=410");
			}
		}
		';


		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['funciones']   = $funciones;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('MVCERTI', 'JQ');
		$param['otros']       = $this->datasis->otros('MVCERTI', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);

	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= "\n\t</script>\n";
		$bodyscript = "";
		return $bodyscript;
	}



	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i = 1;
		$link  = site_url('ajax/buscascli');

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 50,
			'edittype'      => "'select'",
			'stype'         => "'text'",
			'edittype' => "'select'",
			'editoptions' => '{value: {"A":"ACTIVO", "C":"CERRADO"} }'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 50,
			'edittype'      => "'text'",
			'editoptions' => '{'.$grid->autocomplete($link, 'cliente','clicli','<div id=\"clicli\">Nombre:<b>"+ui.item.nombre+"</b><br>RIF:<b>"+ui.item.rifci+"<b></div>').'}',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 190,
			'edittype'      => "'text'",
		));

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 250,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:40, maxlength:32}'
		));

		$grid->addField('obra');
		$grid->label('Obra');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 350,
			'edittype'      => "'textarea'",
			'editrules'     => '{required:true}',
			'editoptions'   => '{ rows:"2", cols:"60"}'
			
			
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');

		$grid->setFormOptionsE('
			closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0) $.prompt(a.responseText);
					return [true, a ];
			},
			beforeShowForm: function(frm){
					$(\'<a href="#">MISION V<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulmv();
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}'
		);

		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},
			beforeShowForm: function(frm){
					$(\'<a href="#">MISION V<span class="ui-icon ui-icon-disk"></span></a>\').click(function(){
						consulrmv();
					}).addClass("fm-button ui-state-default ui-corner-all fm-button-icon-left").prependTo("#Act_Buttons>td.EditButton");
				},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} '
		);


		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

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
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('mvcerti');

		$response   = $grid->getData('view_mvcerti', array(array()), array(), false, $mWHERE );
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
				$this->db->insert('mvcerti', $data);
			}
			return "Registro Agregado";

		} elseif($oper == 'edit') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE certificado=".$this->db->escape($data['numero']));
			if ( $check > 0 ) {
				unset($data['numero']);
				unset($data['cliente']);
			}
			$this->db->where('id', $id);
			$this->db->update('mvcerti', $data);
			return "Registro Modificado";

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE certificado=".$this->db->escape($data['numero']));
			if ($check > 0){
				echo " El certificado no puede ser eliminado, tiene facturas asignadas ";
			} else {
				$this->db->simple_query("DELETE FROM mvcerti WHERE id=$id ");
				logusu('mvcerti',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////


/*
	function chexiste($codigo){
		$codigo=$this->input->post('numero');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM mvcerti WHERE numero='$codigo'");
		if ($check > 0){
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe");
			return FALSE;
		}else {
		return TRUE;
		}
	}
*/
	function traepdf($certificado){
		$this->load->helper('pdf2text');

		$host='http://www.minvih.gob.ve/constancia/index.php/consulta';
		$data=array('ConsultaForm[codigo]'=>$certificado);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3');
		$output = curl_exec($ch);
		$error  = curl_errno($ch);
		$derror = curl_error($ch);
		curl_close($ch);
		if(stripos($output,'errores de ingreso')===false){
			$tt=fluj2text($output);
			$desde=stripos($tt,'Se hace constar');
			echo substr($tt,$desde);
		}else{
			echo 'Certificado no encontrado';
		}
	}

}

?>