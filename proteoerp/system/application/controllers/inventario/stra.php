<?php
class stra extends Controller {

	var $genesal=true;

	function stra(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('302',1);
		$this->back_dataedit='inventario/stra/filteredgrid';
	}

	function index(){
		redirect('inventario/stra/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Filtro de Transferencias','stra');

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField('Fecha', 'fecha');
		$filter->fecha->size=12;

		$filter->envia = new inputField('Env&iacute;a', 'envia');
		$filter->envia->size=12;

		$filter->recibe = new inputField('Recibe', 'recibe');
		$filter->recibe->size=12;

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('inventario/stra/dataedit/show/<#numero#>','<#numero#>');

		$grid = new DataGrid('Lista de Transferencias');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;
		$grid->use_function("substr");

		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha','<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Env&iacute;a','envia','envia');
		$grid->column_orderby('Recibe','recibe','recibe');
		$grid->column_orderby('Observaci&oacute;n','observ1','observ1');

		//echo $grid->db->last_query();
		$grid->add('inventario/stra/dataedit/create');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Transferencias de inventario');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				'peso'=>'Peso'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'where'   =>'activo = "S" AND tipo="Articulo"',
			'script'  =>array('post_modbus("<#i#>")'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Busqueda de producto en inventario');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('stra');
		$do->rel_one_to_many('itstra', 'itstra', 'numero');
		//$do->rel_pointer('itstra','sinv','itstra.codigo=sinv.codigo','sinv.descrip as sinvdescrip');

		$edit = new DataDetails('Transferencia', $do);
		$edit->back_url = $this->back_dataedit;
		$edit->set_rel_title('itstra','Producto <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->fecha = new  dateonlyField('Fecha', 'fecha');
		$edit->fecha->rule='required|chfecha';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->envia = new dropdownField('Almac&eacute;n que Env&iacute;a', 'envia');
		$edit->envia->option('','Seleccionar');
		$edit->envia->options('SELECT ubica,ubides FROM caub ORDER BY ubica');
		$edit->envia->rule ='required';
		$edit->envia->style='width:200px;';

		$edit->recibe = new dropdownField('Almac&eacute;n que Recibe', 'recibe');
		$edit->recibe->option('','Seleccionar');
		$edit->recibe->options('SELECT ubica,ubides FROM caub ORDER BY ubica');
		$edit->recibe->rule ='required|callback_chrecibe';
		$edit->recibe->style='width:200px;';

		$edit->observ1 = new inputField('Observaci&oacute;n','observ1');
		$edit->observ1->rule='max_length[60]|trim';
		$edit->observ1->size =32;
		$edit->observ1->maxlength =30;

		//comienza el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
		$edit->codigo->rule = 'trim|required';
		$edit->codigo->rel_id='itstra';
		$edit->codigo->maxlength=15;
		$edit->codigo->size     =15;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->rel_id   = 'itstra';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->maxlength= 45;
		$edit->descrip->size     = 40;

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->rel_id   ='itstra';
		$edit->cantidad->rule     ='numeric|mayorcero|required';
		$edit->cantidad->maxlength=10;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->size     =10;
		//Fin del detalle

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->buttons('save', 'undo', 'add','back','add_rel');

		if($this->genesal){
			$edit->build();
			$conten['form']  =& $edit;
			$data['style']   = style('redmond/jquery-ui.css');

			$data['script']  = script('jquery.js');
			$data['script'] .= script('jquery-ui.js');
			$data['script'] .= script("jquery-impromptu.js");
			$data['script'] .= script('plugins/jquery.numeric.pack.js');
			$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
			$data['script'] .= script('plugins/jquery.floatnumber.js');
			$data['script'] .= phpscript('nformat.js');
			$data['content'] = $this->load->view('view_stra', $conten,true);
			$data['title']   = heading('Transferencias de inventario');
			$data['head']    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				$rt= 'Transferencia Guardada';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}

	function dataeditordp($numero,$esta){
		if(!isset($_POST['codigo_0'])){
			//SELECT c.codigo
			//,COALESCE(b.cantidad*IF(tipoordp='E',-1,1),0) AS tracana
			//,c.cantidad
			//FROM stra AS a
			//JOIN itstra AS b ON a.numero=b.numero
			//RIGHT JOIN ordpitem AS c ON a.ordp=c.numero AND b.codigo=c.codigo
			//WHERE c.numero='00000019'
		}
		$id_ordp=$this->datasis->dameval('SELECT id FROM ordp WHERE numero='.$this->db->escape($numero));
		$this->back_dataedit='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->load('dataobject','datadetails');
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'existen'=>'Existencia',
				'peso'=>'Peso'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'where'   =>'activo = "S" AND tipo="Articulo"',
			'script'  =>array('post_modbus("<#i#>")'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Busqueda de producto en inventario');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('stra');
		$do->rel_one_to_many('itstra', 'itstra', 'numero');
		//$do->rel_pointer('itstra','sinv','itstra.codigo=sinv.codigo','sinv.descrip as sinvdescrip');

		$edit = new DataDetails('Transferencia', $do);
		$edit->back_url = $this->back_dataedit;
		$edit->set_rel_title('itstra','Producto <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_pre_ordp_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->ordp= new inputField('Orden de producci&oacute;n', 'ordp');
		$edit->ordp->mode='autohide';
		$edit->ordp->size=10;
		$edit->ordp->rule='required|callback_chordp';
		$edit->ordp->insertValue=$numero;
		$edit->ordp->when=array('show','modify');

		$edit->fecha = new  dateonlyField('Fecha', 'fecha');
		$edit->fecha->rule='required|chfecha';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->esta = new  dropdownField('Estaci&oacute;n', 'esta');
		$edit->esta->option('','Seleccionar');
		$edit->esta->options('SELECT estacion,CONCAT(estacion,\'-\',nombre) AS lab FROM esta ORDER BY estacion');
		$edit->esta->rule   = 'required';
		$edit->esta->insertValue=$esta;
		$edit->esta->style  = 'width:150px;';

		$edit->tipoordp = new  dropdownField('Tipo de movimiento', 'tipoordp');
		$edit->tipoordp->option('','Seleccionar');
		$edit->tipoordp->option('E','Entrega');
		$edit->tipoordp->option('R','Retiro' );
		$edit->tipoordp->rule   = 'required|enum[E,R]';
		$edit->tipoordp->style  = 'width:150px;';

		$edit->observ1 = new inputField('Observaci&oacute;n','observ1');
		$edit->observ1->rule='max_length[60]|trim';
		$edit->observ1->size =32;
		$edit->observ1->maxlength =30;

		//comienza el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
		$edit->codigo->rule = 'trim|required|sinvexiste';
		$edit->codigo->rel_id='itstra';
		$edit->codigo->maxlength=15;
		$edit->codigo->size     =15;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->rel_id   = 'itstra';
		$edit->descrip->maxlength= 45;
		$edit->descrip->size     = 40;

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->rel_id   ='itstra';
		$edit->cantidad->rule     ='numeric|mayorcero|required';
		$edit->cantidad->maxlength=10;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->size     =10;
		//Fin del detalle

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$accion="javascript:buscaprod()";
		$edit->button_status('btn_terminar','Traer insumos',$accion,'TR','create');

		$edit->buttons('save', 'undo', 'back','add_rel');

		if($this->genesal){
			$edit->build();
			$conten['form']  =& $edit;
			$data['content'] = $this->load->view('view_stra_ordp', $conten,true);
			$data['style']   = style('redmond/jquery-ui.css');

			$data['script']  = script('jquery.js');
			$data['script'] .= script('jquery-ui.js');
			$data['script'] .= script("jquery-impromptu.js");
			$data['script'] .= script('plugins/jquery.numeric.pack.js');
			$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
			$data['script'] .= script('plugins/jquery.floatnumber.js');
			$data['script'] .= phpscript('nformat.js');
			$data['content'] = $this->load->view('view_stra_ordp', $conten,true);
			$data['head']    = $this->rapyd->get_head();
			$data['title']   = heading('Transferencias de inventario para producci&oacute;n');
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				$rt= 'Transferencia Guardada';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}

	function chordp($numero){
		$this->db->from('ordp');
		$this->db->where('status <>','T');
		$this->db->where('numero',$numero);
		$cana=$this->db->count_all_results();
		if($cana>0){
			return true;
		}
		$this->validation->set_message('chordp','No existe una orden de producci&oacute;n abierta con el n&uacute;mero '.$numero);
		return false;
	}

	//Hace la reservacion del material para una orden de produccion
	function creadordp($id_ordp){
		$url='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : $url;

		$this->genesal=false;
		$mSQL="INSERT IGNORE INTO caub  (ubica,ubides,gasto) VALUES ('APRO','APARTADO DE PRODUCCION','N')";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT IGNORE INTO caub  (ubica,ubides,gasto) VALUES ('PROD','ALMACEN DE PRODUCCION','S')";
		$this->db->simple_query($mSQL);

		$sel=array('a.fecha','a.almacen','a.numero','a.status','a.cana','a.reserva');
		$this->db->select($sel);
		$this->db->from('ordp AS a');
		$this->db->join('sinv AS b','a.codigo=b.codigo');
		$this->db->where('a.id' , $id_ordp);
		$mSQL_1 = $this->db->get();

		if($mSQL_1->num_rows() > 0){
			$row = $mSQL_1->row();
			$cana= $row->cana;
			if($row->reserva=='N'){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'envia'      => $row->almacen,
					'fecha'      => dbdate_to_human($row->fecha),
					'recibe'     => 'APRO',
					'observ1'    => 'ORDEN DE PRODUCCION '.$row->numero
				);

				$sel=array('a.codigo','b.descrip','a.cantidad');
				$this->db->select($sel);
				$this->db->from('ordpitem AS a');
				$this->db->join('sinv AS b','a.codigo=b.codigo');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_2 = $this->db->get();
				$ordpitem_row =$mSQL_2->result();

				foreach ($ordpitem_row as $id=>$itrow){
					$ind='codigo_'.$id;
					$_POST[$ind] = $itrow->codigo;
					$ind='descrip_'.$id;
					$_POST[$ind] = $itrow->descrip;
					$ind='cantidad_'.$id;
					$_POST[$ind] = $itrow->cantidad*$cana;
				}
				$rt=$this->dataedit();
				if(strripos($rt,'Guardada')){
					$data = array('status' => 'P','reserva'=>'S');
					$this->db->where('id', $id_ordp);
					$this->db->update('ordp', $data);
				}

				echo $rt.' '.anchor($back,'regresar');
			}else{
				redirect($back);
			}
		}else{
			exit();
		}
	}

	//Termina la produccion
	function creadordpt($id_ordp){
		$error=0;
		$url='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : $url;

		$this->genesal=false;
		$mSQL="INSERT IGNORE INTO caub (ubica,ubides,gasto) VALUES ('APRO','APARTADO DE PRODUCCION','N')";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT IGNORE INTO caub (ubica,ubides,gasto) VALUES ('PROD','ALMACEN DE PRODUCCION','S')";
		$this->db->simple_query($mSQL);

		$sel=array('a.fecha','a.almacen','a.numero','a.status','a.cana','a.codigo','b.descrip');
		$this->db->select($sel);
		$this->db->from('ordp AS a');
		$this->db->join('sinv AS b','a.codigo=b.codigo');
		$this->db->where('a.id' , $id_ordp);
		$mSQL_1 = $this->db->get();

		if($mSQL_1->num_rows() > 0){
			$row = $mSQL_1->row();
			$codigo = $row->codigo;
			$cana   = $row->cana;
			if($row->status=='C'){
				//Hace la transferencia de lo producido al almacen
				$_POST=array(
					'btn_submit' => 'Guardar',
					'envia'      => 'PROD',
					'fecha'      => dbdate_to_human($row->fecha),
					'recibe'     => $row->almacen,
					'observ1'    => 'FIN ORDEN DE PROD. '.$row->numero
				);

				$id='1';
				$ind='codigo_'.$id;   $_POST[$ind] = $codigo;
				$ind='descrip_'.$id;  $_POST[$ind] = $row->descrip;
				$ind='cantidad_'.$id; $_POST[$ind] = $cana;

				$rt=$this->dataedit();
				if(strripos($rt,'Guardada')){
					$data = array('status' => 'T');
					$this->db->where('id', $id_ordp);
					$this->db->update('ordp', $data);
				}

				//Calcula los costos
				$itcosto=0;
				$sel=array('a.cantidad','a.costo','a.fijo');
				$this->db->select($sel);
				$this->db->from('ordpitem AS a');
				$this->db->join('sinv AS b','a.codigo=b.codigo');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_2 = $this->db->get();
				$ordpitem_row =$mSQL_2->result();

				foreach ($ordpitem_row as $itrow){
					$itcosto+=($itrow->fijo=='S')? $itrow->costo : $itrow->costo*$itrow->cantidad;
				}

				$sel=array('a.porcentaje','a.tipo');
				$this->db->select($sel);
				$this->db->from('ordpindi AS a');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_4 = $this->db->get();
				$ordpindi_row =$mSQL_4->result();
				$costo=$itcosto;
				foreach ($ordpindi_row as $itrow){
					$costo += ($itrow->tipo=='M')? $itrow->porcentaje/$cana :$itrow->porcentaje*$itcosto/100;
				}
				$costo=round($costo,2);

				$data = array('ultimo' => $costo,'formcal'=>'U');
				$this->db->where('codigo', $codigo);
				$this->db->update('sinv', $data);
				$dbcodigo=$this->db->escape($codigo);

				$mSQL="UPDATE sinv SET
				pond   = IF(existen IS NULL,${costo},(existen*pond+${costo}*${cana})/(existen+${cana})),
				base1  = ${costo}*100/(100-margen1),
				base2  = ${costo}*100/(100-margen2),
				base3  = ${costo}*100/(100-margen3),
				base4  = ${costo}*100/(100-margen4),
				precio1= ${costo}*100*(1+(iva/100))/(100-margen1),
				precio2= ${costo}*100*(1+(iva/100))/(100-margen2),
				precio3= ${costo}*100*(1+(iva/100))/(100-margen3),
				precio4= ${costo}*100*(1+(iva/100))/(100-margen4)
				WHERE codigo=${dbcodigo}";

				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'straordp'); $error++; }

				echo $rt.' '.anchor($back,'regresar');
			}else{
				redirect($back);
			}
		}else{
			exit();
		}
	}

	function chrecibe($recibe){
		$envia=$this->input->post('envia');
		if($recibe!=$envia){
			return true;
		}
		$this->validation->set_message('chrecibe','El almac&eacute;n que env&iacute;a no puede ser igual a que recibe');
		return false;
	}

	function _pre_ordp_insert($do){
		if($do->get('tipoordp')=='E'){
			$do->set('envia' ,'APRO');
			$do->set('recibe','PROD');
		}else{
			$do->set('envia' ,'PROD');
			$do->set('recibe','APRO');
		}

		$this->_pre_insert($do);
	}

	function _pre_insert($do){
		$numero=$this->datasis->fprox_numero('nstra');
		$do->set('numero',$numero);
		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('transac', $transac);

		$cana = $do->count_rel('itstra'); $error=0;
		for($i = 0;$i < $cana;$i++){
			$itcodigo  = $do->get_rel('itstra', 'codigo'  ,$i);
			$dbitcodigo=$this->db->escape($itcodigo);
			$sinvrow=$this->datasis->damerow('SELECT iva,precio1,precio2,precio3,precio4, ultimo FROM sinv WHERE codigo='.$dbitcodigo);

			$do->set_rel('itstra', 'precio1',  $sinvrow['precio1'], $i);
			$do->set_rel('itstra', 'precio2',  $sinvrow['precio2'], $i);
			$do->set_rel('itstra', 'precio3',  $sinvrow['precio3'], $i);
			$do->set_rel('itstra', 'precio4',  $sinvrow['precio4'], $i);
			$do->set_rel('itstra', 'iva'    ,  $sinvrow['iva']    , $i);
			$do->set_rel('itstra', 'costo'  ,  $sinvrow['ultimo'] , $i);
		}
		return true;
	}

	function _post_insert($do){
		$envia   = $do->get('envia');
		$recibe  = $do->get('recibe');
		$dbenvia = $this->db->escape($envia);
		$dbrecibe= $this->db->escape($recibe);

		$cana = $do->count_rel('itstra'); $error=0;
		for($i = 0;$i < $cana;$i++){
			$itcana    = floatval($do->get_rel('itstra', 'cantidad',$i));
			$itcodigo  = $do->get_rel('itstra', 'codigo'  ,$i);
			$dbitcodigo=$this->db->escape($itcodigo);

			$mSQL="INSERT INTO itsinv (codigo,alma,existen) VALUES (${dbitcodigo},${dbenvia},-$itcana) ON DUPLICATE KEY UPDATE existen=existen-${itcana}";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'stra'); $error++;}

			$mSQL="INSERT INTO itsinv (codigo,alma,existen) VALUES (${dbitcodigo},${dbrecibe},$itcana) ON DUPLICATE KEY UPDATE existen=existen+${itcana}";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'stra'); $error++;}
		}

		$codigo=$do->get('numero');
		logusu('stra',"TRANSFERENCIA $codigo CREADO");
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se pueden modificar las tranferencias.';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='No se pueden eliminar';
		return false;
	}

	function instalar(){
		if($this->db->field_exists('ordp', 'stra')){
			$mSQL="ALTER TABLE `stra`
			ADD COLUMN `ordp` VARCHAR(8) NULL DEFAULT NULL AFTER `numere`,
			ADD COLUMN `esta` VARCHAR(5) NULL DEFAULT NULL AFTER `ordp`";
			$this->db->simple_query($mSQL);
			$mSQL="ALTER TABLE `stra` ADD COLUMN `tipoordp` CHAR(1) NULL DEFAULT NULL COMMENT 'Si es entrega a estacion o retiro de estacion' AFTER `esta`";
			$this->db->simple_query($mSQL);

		}
	}
}
