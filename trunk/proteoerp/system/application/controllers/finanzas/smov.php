<?php
class smov extends Controller {
	var $titp='Movimientos de clientes';
	var $tits='Movimientos de cliente';
	var $url ='finanzas/smov/';

	function smov(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('502',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('smov','id') ) {
			$this->db->simple_query('ALTER TABLE smov DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE smov ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE smov ADD UNIQUE INDEX cod_cli (cod_cli, tipo_doc, numero, fecha)');
			echo "Indice ID Creado";
		}
		//redirect($this->url.'filteredgrid');
		$this->smovextjs();

	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'smov');

		$filter->cod_cli = new inputField('Cliente','cod_cli');
		$filter->cod_cli->rule      ='max_length[5]';
		$filter->cod_cli->size      =7;
		$filter->cod_cli->maxlength =5;

		$filter->tipo_doc = new dropdownField('Tipo de documento', 'tipo_doc');
		$filter->tipo_doc->option('','Todos');
		$filter->tipo_doc->option('FC','Facturas');
		$filter->tipo_doc->option('ND','Nota de D&eacute;bito');
		$filter->tipo_doc->option('NC','Nota de Cr&eacute;dito');
		$filter->tipo_doc->option('AB','Abono');
		$filter->tipo_doc->option('AN','Anticipo');
		$filter->tipo_doc->style ='width:200px;';

		$filter->numero = new inputField('N&uacute;mero','numero');
		$filter->numero->rule      ='max_length[8]';
		$filter->numero->size      =10;
		$filter->numero->maxlength =8;

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#cod_cli#></raencode>/<raencode><#tipo_doc#></raencode>/<raencode><#numero#></raencode>/<raencode><#fecha#></raencode>','<#tipo_doc#>-<#numero#>');

		$grid = new DataGrid('');
		$grid->order_by('fecha','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Cliente'    ,$uri,'cod_cli','align="left"');
		$grid->column_orderby('Nombre'     ,'nombre','nombre','align="left"');
		$grid->column_orderby('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align="center"');
		$grid->column_orderby('Monto'      ,'<nformat><#monto#></nformat>','monto','align="right"');
		$grid->column_orderby('Abonos'     ,'<nformat><#abonos#></nformat>','abonos','align="right"');
		$grid->column_orderby('Observaci&oacute;n','<#observa1#> <#observa2#>','observa1','align="left"');
		$grid->column_orderby('Transaci&oacute;n' ,'transac','transac','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'smov');

		$edit->back_url = site_url($this->url.'filteredgrid');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->cod_cli = new inputField('Cod_cli','cod_cli');
		$edit->cod_cli->rule='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->dire1 = new inputField('Dire1','dire1');
		$edit->dire1->rule='max_length[40]';
		$edit->dire1->size =42;
		$edit->dire1->maxlength =40;

		$edit->dire2 = new inputField('Dire2','dire2');
		$edit->dire2->rule='max_length[40]';
		$edit->dire2->size =42;
		$edit->dire2->maxlength =40;

		$edit->tipo_doc = new inputField('Tipo_doc','tipo_doc');
		$edit->tipo_doc->rule='max_length[2]';
		$edit->tipo_doc->size =4;
		$edit->tipo_doc->maxlength =2;

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;

		$edit->impuesto = new inputField('Impuesto','impuesto');
		$edit->impuesto->rule='max_length[17]|numeric';
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->size =19;
		$edit->impuesto->maxlength =17;

		$edit->abonos = new inputField('Abonos','abonos');
		$edit->abonos->rule='max_length[17]|numeric';
		$edit->abonos->css_class='inputnum';
		$edit->abonos->size =19;
		$edit->abonos->maxlength =17;

		$edit->vence = new dateField('Vence','vence');
		$edit->vence->rule='chfecha';
		$edit->vence->size =10;
		$edit->vence->maxlength =8;

		$edit->tipo_ref = new inputField('Tipo_ref','tipo_ref');
		$edit->tipo_ref->rule='max_length[2]';
		$edit->tipo_ref->size =4;
		$edit->tipo_ref->maxlength =2;

		$edit->num_ref = new inputField('Num_ref','num_ref');
		$edit->num_ref->rule='max_length[8]';
		$edit->num_ref->size =10;
		$edit->num_ref->maxlength =8;

		$edit->observa1 = new inputField('Observa1','observa1');
		$edit->observa1->rule='max_length[50]';
		$edit->observa1->size =52;
		$edit->observa1->maxlength =50;

		$edit->observa2 = new inputField('Observa2','observa2');
		$edit->observa2->rule='max_length[50]';
		$edit->observa2->size =52;
		$edit->observa2->maxlength =50;

		$edit->servicio = new inputField('Servicio','servicio');
		$edit->servicio->rule='max_length[17]|numeric';
		$edit->servicio->css_class='inputnum';
		$edit->servicio->size =19;
		$edit->servicio->maxlength =17;

		$edit->banco = new inputField('Banco','banco');
		$edit->banco->rule='max_length[2]';
		$edit->banco->size =4;
		$edit->banco->maxlength =2;

		$edit->tipo_op = new inputField('Tipo_op','tipo_op');
		$edit->tipo_op->rule='max_length[2]';
		$edit->tipo_op->size =4;
		$edit->tipo_op->maxlength =2;

		$edit->fecha_op = new dateField('Fecha_op','fecha_op');
		$edit->fecha_op->rule='chfecha';
		$edit->fecha_op->size =10;
		$edit->fecha_op->maxlength =8;

		$edit->num_op = new inputField('Num_op','num_op');
		$edit->num_op->rule='max_length[12]';
		$edit->num_op->size =14;
		$edit->num_op->maxlength =12;

		$edit->ppago = new inputField('Ppago','ppago');
		$edit->ppago->rule='max_length[17]|numeric';
		$edit->ppago->css_class='inputnum';
		$edit->ppago->size =19;
		$edit->ppago->maxlength =17;

		$edit->reten = new inputField('Reten','reten');
		$edit->reten->rule='max_length[17]|numeric';
		$edit->reten->css_class='inputnum';
		$edit->reten->size =19;
		$edit->reten->maxlength =17;

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='max_length[6]';
		$edit->codigo->size =8;
		$edit->codigo->maxlength =6;

		$edit->descrip = new inputField('Descrip','descrip');
		$edit->descrip->rule='max_length[30]';
		$edit->descrip->size =32;
		$edit->descrip->maxlength =30;

		$edit->control = new inputField('Control','control');
		$edit->control->rule='max_length[8]';
		$edit->control->size =10;
		$edit->control->maxlength =8;

		$edit->transac = new inputField('Transac','transac');
		$edit->transac->rule='max_length[8]';
		$edit->transac->size =10;
		$edit->transac->maxlength =8;

		$edit->origen = new inputField('Origen','origen');
		$edit->origen->rule='max_length[2]';
		$edit->origen->size =4;
		$edit->origen->maxlength =2;

		$edit->cambio = new inputField('Cambio','cambio');
		$edit->cambio->rule='max_length[17]|numeric';
		$edit->cambio->css_class='inputnum';
		$edit->cambio->size =19;
		$edit->cambio->maxlength =17;

		$edit->mora = new inputField('Mora','mora');
		$edit->mora->rule='max_length[17]|numeric';
		$edit->mora->css_class='inputnum';
		$edit->mora->size =19;
		$edit->mora->maxlength =17;

		$edit->reteiva = new inputField('Reteiva','reteiva');
		$edit->reteiva->rule='max_length[18]|numeric';
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size =20;
		$edit->reteiva->maxlength =18;

		$edit->vendedor = new inputField('Vendedor','vendedor');
		$edit->vendedor->rule='max_length[5]';
		$edit->vendedor->size =7;
		$edit->vendedor->maxlength =5;

		$edit->nfiscal = new inputField('Nfiscal','nfiscal');
		$edit->nfiscal->rule='max_length[8]';
		$edit->nfiscal->size =10;
		$edit->nfiscal->maxlength =8;

		$edit->montasa = new inputField('Montasa','montasa');
		$edit->montasa->rule='max_length[17]|numeric';
		$edit->montasa->css_class='inputnum';
		$edit->montasa->size =19;
		$edit->montasa->maxlength =17;

		$edit->monredu = new inputField('Monredu','monredu');
		$edit->monredu->rule='max_length[17]|numeric';
		$edit->monredu->css_class='inputnum';
		$edit->monredu->size =19;
		$edit->monredu->maxlength =17;

		$edit->monadic = new inputField('Monadic','monadic');
		$edit->monadic->rule='max_length[17]|numeric';
		$edit->monadic->css_class='inputnum';
		$edit->monadic->size =19;
		$edit->monadic->maxlength =17;

		$edit->tasa = new inputField('Tasa','tasa');
		$edit->tasa->rule='max_length[17]|numeric';
		$edit->tasa->css_class='inputnum';
		$edit->tasa->size =19;
		$edit->tasa->maxlength =17;

		$edit->reducida = new inputField('Reducida','reducida');
		$edit->reducida->rule='max_length[17]|numeric';
		$edit->reducida->css_class='inputnum';
		$edit->reducida->size =19;
		$edit->reducida->maxlength =17;

		$edit->sobretasa = new inputField('Sobretasa','sobretasa');
		$edit->sobretasa->rule='max_length[17]|numeric';
		$edit->sobretasa->css_class='inputnum';
		$edit->sobretasa->size =19;
		$edit->sobretasa->maxlength =17;

		$edit->exento = new inputField('Exento','exento');
		$edit->exento->rule='max_length[17]|numeric';
		$edit->exento->css_class='inputnum';
		$edit->exento->size =19;
		$edit->exento->maxlength =17;

		$edit->fecdoc = new dateField('Fecdoc','fecdoc');
		$edit->fecdoc->rule='chfecha';
		$edit->fecdoc->size =10;
		$edit->fecdoc->maxlength =8;

		$edit->nroriva = new inputField('Nroriva','nroriva');
		$edit->nroriva->rule='max_length[20]';
		$edit->nroriva->size =22;
		$edit->nroriva->maxlength =20;

		$edit->emiriva = new dateField('Emiriva','emiriva');
		$edit->emiriva->rule='chfecha';
		$edit->emiriva->size =10;
		$edit->emiriva->maxlength =8;

		$edit->codcp = new inputField('Codcp','codcp');
		$edit->codcp->rule='max_length[5]';
		$edit->codcp->size =7;
		$edit->codcp->maxlength =5;

		$edit->depto = new inputField('Depto','depto');
		$edit->depto->rule='max_length[3]';
		$edit->depto->size =5;
		$edit->depto->maxlength =3;

		$edit->maqfiscal = new inputField('Maqfiscal','maqfiscal');
		$edit->maqfiscal->rule='max_length[20]';
		$edit->maqfiscal->size =22;
		$edit->maqfiscal->maxlength =20;

		$edit->ningreso = new inputField('Ningreso','ningreso');
		$edit->ningreso->rule='max_length[8]';
		$edit->ningreso->size =10;
		$edit->ningreso->maxlength =8;

		$edit->ncredito = new inputField('Ncredito','ncredito');
		$edit->ncredito->rule='max_length[8]';
		$edit->ncredito->size =10;
		$edit->ncredito->maxlength =8;

		$edit->buttons('back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function sfacreiva(){
		$reinte = $this->uri->segment($this->uri->total_segments());
		$efecha = $this->uri->segment($this->uri->total_segments()-1);
		$fecha  = $this->uri->segment($this->uri->total_segments()-2);
		$numero = $this->uri->segment($this->uri->total_segments()-3);
		$id     = $this->uri->segment($this->uri->total_segments()-4);
		$mdevo  = "Exito";

		//memowrite("efecha=$efecha, fecha=$fecha, numero=$numero, id=$id, reinte=$reinte","sfacreiva");

		// status de la factura
		$fecha  = substr($fecha, 6,4).substr($fecha, 3,2).substr($fecha, 0,2);
		$efecha = substr($efecha,6,4).substr($efecha,3,2).substr($efecha,0,2);

		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=$id");
		$referen  = $this->datasis->dameval("SELECT referen  FROM sfac WHERE id=$id");
		$numfac   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=$id");
		$cod_cli  = $this->datasis->dameval("SELECT cod_cli  FROM sfac WHERE id=$id");
		$monto    = $this->datasis->dameval("SELECT ROUND(iva*0.75,2)  FROM sfac WHERE id=$id");
		$factura  = $this->datasis->dameval("SELECT factura  FROM sfac WHERE id=$id");

		$anterior = $this->datasis->dameval("SELECT reiva FROM sfac WHERE id=$id");
		$usuario = addslashes($this->session->userdata('usuario'));

		if ( strlen($numero) == 14 ){
			if (  $anterior == 0 )  {
				$mSQL = "UPDATE sfac SET reiva=round(iva*0.75,2), creiva='$numero', freiva='$fecha', ereiva='$efecha' WHERE id=$id";
				$this->db->simple_query($mSQL);
				//memowrite($mSQL,"sfacreivaSFAC");

				$transac = $this->datasis->prox_sql("ntransa");
				$transac = str_pad($transac, 8, "0", STR_PAD_LEFT);

				if ($referen == 'C') {
					$saldo =  $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero='$numfac'");
				}

				if ( $tipo_doc == 'F') {
					if ($referen == 'E') {
						// FACTURA PAGADA AL CONTADO GENERA ANTICIPO
						$mnumant = $this->datasis->prox_sql("nancli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

						$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
						SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
							CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
							curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
						FROM sfac WHERE id=$id";
						$this->db->simple_query($mSQL);
						$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";
					} elseif ($referen == 'C') {
						// Busca si esta cancelada
						$tiposfac = 'FC';
						if ( $tipo_doc == 'D') $tiposfac = 'NC';
						$mSQL = "SELECT monto-abonos saldo FROM smov WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
						$saldo = $this->datasis->dameval($mSQL);
						if ( $saldo < $monto ) {  // crea anticipo
							$mnumant = $this->datasis->prox_sql("nancli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
							SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Anticipo Generado por factura ya pagada";
							memowrite($mSQL,"sfacreivaAN");
						} else {
							$mnumant = $this->datasis->prox_sql("nccli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, codigo, descrip, nroriva, emiriva )
								SELECT cod_cli, nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, reiva abonos, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario,
								'NOCON 'codigo, 'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
								FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);

							// ABONA A LA FACTURA
							$mSQL = "UPDATE smov SET abonos=abonos+$monto WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
								$this->db->simple_query($mSQL);

							//Crea la relacion en ccli

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y aplicada a la factura";
						}
					}
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");

				} else {
					// DEVOLUCIONES GENERA ND AL CLIENTE
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

					$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
					SELECT cod_cli, nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
						CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
						curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";

					//Devoluciones debe crear un NC si esta en el periodo
					$mnumant = $this->datasis->prox_sql("nccli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");

				}
			} else {
				$mdevo = "<h1 style='color:red;'>ERROR</h1>Retencion ya aplicada";
			}
		} else $mdevo = "<h1 style='color:red;'>ERROR</h1>Longitud del comprobante menor a 14 caracteres, corrijalo y vuelva a intentar";

		echo $mdevo;
	}



	function _pre_insert($do){
		return false;
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		return false;
	}

	function _creasmov(){

		$data['cod_cli']    ='';
		$data['nombre']     ='';
		$data['dire1']      ='';
		$data['dire2']      ='';
		$data['tipo_doc']   ='';
		$data['numero']     ='';
		$data['fecha']      ='';
		$data['monto']      ='';
		$data['impuesto']   ='';
		$data['abonos']     ='';
		$data['vence']      ='';

		$data['tipo_ref']   ='';
		$data['num_ref']    ='';
		$data['observa1']   ='';
		$data['observa2']   ='';
		$data['servicio']   ='';
		$data['banco']      ='';
		$data['tipo_op']    ='';
		$data['fecha_op']   ='';
		$data['num_op']     ='';
		$data['ppago']      ='';
		$data['reten']      ='';
		$data['codigo']     ='';
		$data['descrip']    ='';
		$data['control']    ='';
		$data['usuario']    ='';
		$data['estampa']    ='';
		$data['hora']       ='';
		$data['transac']    ='';
		$data['origen']     ='';
		$data['cambio']     ='';
		$data['mora']       ='';
		$data['reteiva']    ='';
		$data['vendedor']   ='';
		$data['nfiscal']    ='';
		$data['montasa']    ='';
		$data['monredu']    ='';
		$data['monadic']    ='';
		$data['tasa']       ='';
		$data['reducida']   ='';
		$data['sobretasa']  ='';
		$data['exento']     ='';
		$data['fecdoc']     ='';
		$data['nroriva']    ='';
		$data['emiriva']    ='';
		$data['codcp']      ='';
		$data['depto']      ='';
		$data['maqfiscal']  ='';
		$data['ningreso']   ='';
		$data['ncredito']   ='';
	}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters,'smov');
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('smov');

		if (strlen($where)>1){
			$this->db->where($where);
		}

		if ( $sort == '') $this->db->order_by( 'id', 'desc' );

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);

		$query = $this->db->get();
		$results = $query->num_rows();

		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function griditsmov(){
		$transac   = isset($_REQUEST['transac'])  ? $_REQUEST['transac']  :  0;
		$tipo_doc  = isset($_REQUEST['tipo_doc']) ? $_REQUEST['tipo_doc'] :  '';
		$numero    = isset($_REQUEST['numero'])   ? $_REQUEST['numero']   :  '';
		$cod_cli   = isset($_REQUEST['cod_cli'])  ? $_REQUEST['cod_cli']  :  '';


//numccli, tipoccli, cod_cli, tipo_doc, numero, fecha, monto, abono, ppago, reten, reteiva
		//{ header: 'Cambio',  width: 80, sortable: true, dataIndex: 'cambio' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		//{ header: 'Mora',    width: 80, sortable: true, dataIndex: 'mora' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		//{ header: 'Transac', width: 60, sortable: true, dataIndex: 'transac' , field: { type: 'textfield' }, filter: { type: 'string' }},
		//{ header: 'Estampa', width: 80, sortable: true, dataIndex: 'estampa' , field: { type: 'date' }, filter: { type: 'date' }},
		//{ header: 'Hora',    width: 60, sortable: true, dataIndex: 'hora' , field: { type: 'textfield' }, filter: { type: 'string' }},
		//{ header: 'Usuario', width: 60, sortable: true, dataIndex: 'usuario' , field: { type: 'textfield' }, filter: { type: 'string' }},
		//{ header: 'Reteiva', width: 60, sortable: true, dataIndex: 'reteiva' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		//{ header: 'Nroriva', width: 60, sortable: true, dataIndex: 'nroriva' , field: { type: 'textfield' }, filter: { type: 'string' }},
		//{ header: 'Emiriva', width: 60, sortable: true, dataIndex: 'emiriva' , field: { type: 'date' }, filter: { type: 'date' }},
		//{ header: 'Recriva', width: 60, sortable: true, dataIndex: 'recriva' , field: { type: 'date' }, filter: { type: 'date' }},
		//{ header: 'id',      width: 60, sortable: true, dataIndex: 'id' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')}




		if ($transac == 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM smov ")  ;
			$transac = $this->datasis->dameval("SELECT transac FROM smov WHERE id=$id ")  ;
		}

		$mSQL = "
SELECT
cod_cli, fecha, 
IF(tipo_doc='$tipo_doc' AND numero='$numero', tipoccli, tipo_doc) tipo_doc,
IF(tipo_doc='$tipo_doc' AND numero='$numero', numccli, numero) numero,
monto, abono, ppago, reten, reteiva
FROM itccli WHERE transac='$transac' ";
/*
#UNION ALL
#SELECT * FROM itccli WHERE cod_cli='$cod_cli' AND numero='$numero' AND tipo_doc='$tipo_doc' AND transac!='$transac'
#UNION ALL
#SELECT * FROM itccli WHERE cod_cli='$cod_cli' AND numccli='$numero' AND tipoccli='$tipo_doc' AND transac!='$transac'
ORDER BY estampa
";*/

		$query = $this->db->query($mSQL);
		$results =  0;
		$mSQL = '';
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data"'.$mSQL.' ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function sclibu(){
		$control = $this->uri->segment(4);
		$id = $this->datasis->dameval("SELECT b.id FROM smov a JOIN sprv b ON a.proveed=b.proveed WHERE control='$control'");
		redirect('finanzas/sprv/dataedit/show/'.$id);
	}

	function tabla() {
		$transac  = isset($_REQUEST['transac'])  ? $_REQUEST['transac']  :  0;
		$cod_cli  = isset($_REQUEST['cod_cli'])  ? $_REQUEST['cod_cli']  :  0;
		$numero   = isset($_REQUEST['numero'])   ? $_REQUEST['numero']   :  0;
		$tipo_doc = isset($_REQUEST['tipo_doc']) ? $_REQUEST['tipo_doc'] :  0;

		$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM sprm WHERE transac='$transac' ORDER BY cod_prv ";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$salida = '';
		$saldo  = 0;
		if ( $query->num_rows() > 0 ){
			$salida = "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				if ( $codcli != $row['cod_prv']){
					$codcli = $row['cod_prv'];
					$salida .= "<tr bgcolor='#c7d3c7'>";
					$salida .= "<td colspan=4>".trim($row['nombre']). "</td>";
					$salida .= "</tr>";
				}
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}

		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM smov WHERE transac='$transac' ORDER BY cod_cli ";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				if ( $codcli != $row['cod_cli']){
					$codcli = $row['cod_cli'];
					$salida .= "<tr bgcolor='#c7d3c7'>";
					$salida .= "<td colspan=4>".trim($row['nombre']). "</td>";
					$salida .= "</tr>";
				}
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}



		//cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
		//Cruce de Cuentas
		$mSQL = "SELECT b.proveed cod_cli, MID(b.nombre,1,25) nombre, a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.cliente='$cod_cli' AND a.onumero='$tipo_doc$numero'
			UNION ALL
			SELECT b.cliente cod_cli, MID(b.nomcli,1,25) nombre, -a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.cliente='$cod_cli' AND a.onumero='$tipo_doc$numero'
			ORDER BY numero
			";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<td colspan=4>Cruce de Cuentas</td>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Codigo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['cod_cli']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}
		echo $salida;
	}


	function smovextjs() {
		$encabeza='MOVIMIENTO DE CLIENTES';
		$listados= $this->datasis->listados('smov');
		$otros=$this->datasis->otros('smov', 'finanzas/smov');

		$modulo = 'smov';
		$urlajax = 'finanzas/smov/';

		$columnas = "
			{ header: 'Cliente',   width: 50, sortable: true, dataIndex: 'cod_cli' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Nombre',    width:210, sortable: true, dataIndex: 'nombre' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Tipo',      width: 40, sortable: true, dataIndex: 'tipo_doc' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Numero',    width: 60, sortable: true, dataIndex: 'numero' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Fecha',     width: 80, sortable: true, dataIndex: 'fecha' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'Monto',     width: 90, sortable: true, dataIndex: 'monto' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'I.V.A.',    width: 90, sortable: true, dataIndex: 'impuesto' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Abonos',    width: 90, sortable: true, dataIndex: 'abonos' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Vence',     width: 80, sortable: true, dataIndex: 'vence' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'Referen.',  width: 40, sortable: true, dataIndex: 'tipo_ref' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'num_ref',   width: 60, sortable: true, dataIndex: 'num_ref' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'observa1',  width: 60, sortable: true, dataIndex: 'observa1' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'observa2',  width: 60, sortable: true, dataIndex: 'observa2' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Banco',     width: 60, sortable: true, dataIndex: 'banco' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'tipo_op' ,  width: 60, sortable: true, dataIndex: 'tipo_op' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Fecha_op',  width: 60, sortable: true, dataIndex: 'fecha_op' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'Num_op',    width: 60, sortable: true, dataIndex: 'num_op' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'pP.Pago',   width: 80, sortable: true, dataIndex: 'ppago' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Reten',     width: 60, sortable: true, dataIndex: 'reten' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Codigo',    width: 60, sortable: true, dataIndex: 'codigo' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Descrip',   width: 60, sortable: true, dataIndex: 'descrip' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Control',   width: 60, sortable: true, dataIndex: 'control' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Usuario',   width: 60, sortable: true, dataIndex: 'usuario' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Estampa',   width: 60, sortable: true, dataIndex: 'estampa' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'Hora',      width: 60, sortable: true, dataIndex: 'hora' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Transac',   width: 60, sortable: true, dataIndex: 'transac' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Origen',    width: 60, sortable: true, dataIndex: 'origen' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Cambio',    width: 60, sortable: true, dataIndex: 'cambio' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Mora',      width: 60, sortable: true, dataIndex: 'mora' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Reteiva',   width: 60, sortable: true, dataIndex: 'reteiva' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'Vendedor',  width: 60, sortable: true, dataIndex: 'vendedor' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Nfiscal',   width: 60, sortable: true, dataIndex: 'nfiscal' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'montasa',   width: 60, sortable: true, dataIndex: 'montasa' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'monredu',   width: 60, sortable: true, dataIndex: 'monredu' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'monadic',   width: 60, sortable: true, dataIndex: 'monadic' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'tasa',      width: 60, sortable: true, dataIndex: 'tasa' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'reducida',  width: 60, sortable: true, dataIndex: 'reducida' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'sobretasa', width: 60, sortable: true, dataIndex: 'sobretasa' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'exento',    width: 60, sortable: true, dataIndex: 'exento' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
			{ header: 'fecdoc',    width: 60, sortable: true, dataIndex: 'fecdoc' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'nroriva',   width: 60, sortable: true, dataIndex: 'nroriva' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'emiriva',   width: 60, sortable: true, dataIndex: 'emiriva' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'codcp',     width: 60, sortable: true, dataIndex: 'codcp' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'depto',     width: 60, sortable: true, dataIndex: 'depto' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'maqfiscal', width: 60, sortable: true, dataIndex: 'maqfiscal' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'modificado',width: 60, sortable: true, dataIndex: 'modificado' , field: { type: 'date' }, filter: { type: 'date' }},
			{ header: 'Ningreso',  width: 60, sortable: true, dataIndex: 'ningreso' , field: { type: 'textfield' }, filter: { type: 'string' }},
			{ header: 'Ncredito',  width: 60, sortable: true, dataIndex: 'ncredito' , field: { type: 'textfield' }, filter: { type: 'string' }}
		";

		$coldeta = "
	var Deta1Col = [
		{ header: 'Cliente', width: 50, sortable: true, dataIndex: 'cod_cli' , field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Tipo',    width: 40, sortable: true, dataIndex: 'tipo_doc' , field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Numero',  width: 70, sortable: true, dataIndex: 'numero' , field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Fecha',   width: 80, sortable: true, dataIndex: 'fecha' , field: { type: 'date' }, filter: { type: 'date' }},
		{ header: 'Monto',   width: 80, sortable: true, dataIndex: 'monto' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Abono',   width: 80, sortable: true, dataIndex: 'abono' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'P.Pago',  width: 80, sortable: true, dataIndex: 'ppago' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Reten',   width: 80, sortable: true, dataIndex: 'reten' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		//{ header: 'Cambio',  width: 80, sortable: true, dataIndex: 'cambio' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		//{ header: 'Mora',    width: 80, sortable: true, dataIndex: 'mora' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Reteiva', width: 60, sortable: true, dataIndex: 'reteiva' , field: { type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Nroriva', width: 60, sortable: true, dataIndex: 'nroriva' , field: { type: 'textfield' }, filter: { type: 'string' }},
		//{ header: 'Emiriva', width: 60, sortable: true, dataIndex: 'emiriva' , field: { type: 'date' }, filter: { type: 'date' }},
		//{ header: 'Recriva', width: 60, sortable: true, dataIndex: 'recriva' , field: { type: 'date' }, filter: { type: 'date' }},
		]";

		$variables='';

		$valida="		{ type: 'length', field: 'cod_cli',  min:  1 }";

		$funciones = "
		function renderSprv(value, p, record) {
			var mreto='';
			if ( record.data.proveed == '' ){
				mreto = '{0}';
			} else {
				mreto = '<a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlApp+'finanzas/smov/sprvbu/{1}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">{0}</a>';
			}
			return Ext.String.format(mreto,	value, record.data.control );
		}";

		$campos = $this->datasis->extjscampos($modulo);

		$stores = "
	Ext.define('It".$modulo."', {
		extend: 'Ext.data.Model',
		fields: [".$this->datasis->extjscampos("itccli")."],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlAjax + 'griditsmov',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeIt".$modulo." = Ext.create('Ext.data.Store', {
		model: 'It".$modulo."',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});

	//////////////////////////////////////////////////////////
	//
	var gridDeta1 = Ext.create('Ext.grid.Panel', {
		width:   '100%',
		height:  '100%',
		store:   storeIt".$modulo.",
		title:   'Detalle del Movimiento',
		iconCls: 'icon-grid',
		frame:   true,
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		columns: Deta1Col
	});

	var ".$modulo."TplMarkup = [
		'<table width=\'100%\' bgcolor=\"#F3F781\">',
		'<tr><td colspan=3 align=\'center\'><p style=\'font-size:14px;font-weight:bold\'>IMPRIMIR FACTURA</p></td></tr><tr>',
		'<td align=\'center\'><a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlApp+'ventas/sfac_add/dataprint/modify/{id}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">".img(array('src' => 'images/html_icon.gif', 'alt' => 'Formato HTML', 'title' => 'Formato HTML','border'=>'0'))."</a></td>',
		'<td align=\'center\'>{numero}</td>',
		'<td align=\'center\'><a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlApp+'ventas/sfac_add/dataprint/modify/{id}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">".img(array('src' => 'images/pdf_logo.gif', 'alt' => 'Formato PDF',  'title' => 'Formato PDF', 'border'=>'0'))."</a></td></tr>',
		'<tr><td colspan=3 align=\'center\' >--</td></tr>',
		'</table>','nanai'
	];



	// Al cambiar seleccion
	gridMaest.getSelectionModel().on('selectionchange', function(sm, selectedRecord) {
		if (selectedRecord.length) {
			gridMaest.down('#delete').setDisabled(selectedRecord.length === 0);
			gridMaest.down('#update').setDisabled(selectedRecord.length === 0);
			numero   = selectedRecord[0].data.numero;
			cod_cli  = selectedRecord[0].data.cod_cli;
			tipo_doc = selectedRecord[0].data.tipo_doc;
			transac  = selectedRecord[0].data.transac;
			gridDeta1.setTitle( numero+' '+selectedRecord[0].data.nombre);
			storeIt".$modulo.".load({ params: { numero: numero, cod_cli: cod_cli, tipo_doc: tipo_doc, transac: transac }});
			var meco1 = Ext.getCmp('imprimir');
			Ext.Ajax.request({
				url: urlAjax +'tabla',
				params: { numero: numero, cod_cli: cod_cli, tipo_doc: tipo_doc, transac: transac  },
				success: function(response) {
					var vaina = response.responseText;
					".$modulo."TplMarkup.pop();
					".$modulo."TplMarkup.push(vaina);
					var ".$modulo."Tpl = Ext.create('Ext.Template', ".$modulo."TplMarkup );
					meco1.setTitle('Imprimir Compra');
					".$modulo."Tpl.overwrite(meco1.body, selectedRecord[0].data );
				}
			});
		}
	});
";

		$acordioni = "{
					layout: 'fit',
					items:[
						{
							name: 'imprimir',
							id: 'imprimir',
							border:false,
							html: 'Para imprimir seleccione una Compra '
						}
					]
				},
";

		$dockedItems = "{
			xtype: 'toolbar',
			items: [
				{
					iconCls: 'icon-add',
					text: 'Agregar',
					scope: this,
					handler: function(){
						window.open(urlApp+'ventas/sfac_add/dataedit/create', '_blank', 'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys);
					}
				},
				{
					iconCls: 'icon-update',
					text: 'Modificar',
					disabled: true,
					itemId: 'update',
					scope: this,
					handler: function(selModel, selections){
						var selection = gridMaest.getView().getSelectionModel().getSelection()[0];
						gridMaest.down('#delete').setDisabled(selections.length === 0);
						window.open(urlApp+'ventas/sfac_add/dataedit/modify/'+selection.data.id, '_blank', 'width=900,height=730,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys);
					}
				},{
					iconCls: 'icon-delete',
					text: 'Eliminar',
					disabled: true,
					itemId: 'delete',
					scope: this,
					handler: function() {
						var selection = gridMaest.getView().getSelectionModel().getSelection()[0];
						Ext.MessageBox.show({
							title: 'Confirme',
							msg: 'Seguro que quiere eliminar la compra Nro. '+selection.data.numero,
							buttons: Ext.MessageBox.YESNO,
							fn: function(btn){
								if (btn == 'yes') {
									if (selection) {
										//storeMaest.remove(selection);
									}
									storeMaest.load();
								}
							},
							icon: Ext.MessageBox.QUESTION
						});
					}
				}
			]
		}
		";

		$grid2 = ",{
				itemId: 'viewport-center-detail',
				activeTab: 0,
				region: 'south',
				height: '40%',
				split: true,
				margins: '0 0 0 0',
				preventHeader: true,
				items: gridDeta1
			}";


		$titulow = 'Movimiento';

		$filtros = "";
		$features = "
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		plugins: [Ext.create('Ext.grid.plugin.CellEditing', { clicksToEdit: 2 })],
";

		$final = "storeIt".$modulo.".load();";

		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['encabeza']    = $encabeza;
		$data['urlajax']     = $urlajax;
		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['stores']      = $stores;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['titulow']     = $titulow;
		$data['dockedItems'] = $dockedItems;
		$data['features']    = $features;
		$data['filtros']     = $filtros;
		$data['grid2']       = $grid2;
		$data['coldeta']     = $coldeta;
		$data['acordioni']   = $acordioni;
		$data['final']       = $final;

		$data['title']  = heading('Movimiento de Cliente');
		$this->load->view('extjs/extjsvenmd',$data);


	}

}