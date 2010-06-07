<?php
class Consultas extends Controller {
	function Consultas(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		//redirect("inventario/consultas/filteredgrid");
	}

	function precios(){

		$barras = array(
			'name'      => 'barras',
			'id'        => 'barras',
			'value'     => '',
			'maxlength' => '15',
			'size'      => '16',
			//'style'     => 'display:none;',
		);

		$out  = form_open('supermercado/consultas/precios');
		$out .= "Introduzca un Codigo ";
		$out .= form_input($barras);
		$out .= form_close();

		$link=site_url('supermercado/consultas/rprecios');

		$data['script']= <<<script
		<script type="text/javascript">
		$(document).ready(function(){
			$("#resp").hide();
			$("#barras").attr("value", "");
			$("#barras").focus();
			$("form").submit(function() {
				mostrar();
				return false;
			});

		});

		function mostrar(){
			$("#resp").hide();
			var url = "$link";
			$.ajax({
				type: "POST",
				url: url,
				data: $("input").serialize(),
				success: function(msg){ 
					$("#resp").html(msg).fadeIn("slow");
					$("#barras").attr("value", "");
					$("#barras").focus();
				}
			});
		}
		</script>
script;
		$data['content'] = '<div id="resp" style=" width: 100%; height: 300px" >&nbsp;</div>';
		$data['logo']   = "<img src='".base_url()."images/logopm.jpg' width=150>";
		$data['title']   = "<h1>$out</h1>";
		$data["head"]    = script("jquery-1.2.6.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rprecios($cod_bar=NULL){
		if(empty($cod_bar)){
			$cod_bar=$this->input->post('barras');
			if ($cod_bar===false){
				echo 'Debe introducir un c&oacute;digo de barras';
				return 0;
			}
		}
		$mSQL_p='SELECT codigo, referen, barras, descrip, corta, codigo, marca, precio1, precio2, precio3, precio4, dvolum1, dvolum2, existen, mempaq, dempaq FROM maes';
		$mSQL  =$mSQL_p." WHERE barras='$cod_bar'";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() == 0){
			$mSQL  =$mSQL_p." WHERE codigo='$cod_bar'";
			$query = $this->db->query($mSQL);
			if ($query->num_rows()== 0){
			    $mSQL  =$mSQL_p." WHERE referen='$cod_bar'";
			    $query = $this->db->query($mSQL);
			
			    if ($query->num_rows()== 0){
				echo 'Producto no registrado';
				return 0;
			    }
			}
		}
		$row = $query->row();
		$data['precio1']  = number_format($row->precio1,2,',','.');
		$data['precio2']  = number_format($row->precio2,2,',','.');
		$data['precio3']  = number_format($row->precio3,2,',','.');
		$data['precio4']  = number_format($row->precio4,2,',','.');

		$data['dvolum1'] = $row->dvolum1;
		$data['dvolum2'] = $row->dvolum2;

		$data['descrip'] = $row->descrip;

		$data['corta'] = $row->corta;

		$data['referen'] = $row->referen;
		
		$data['codigo']  = $row->codigo;
		$data['marca']   = $row->marca;
		$data['existen'] = $this->datasis->dameval("SELECT sum(fraccion) FROM ubic WHERE codigo='".$row->codigo."' AND ubica IN ('DE00','DE01')");
		//$row->existen;
		$data['barras']  = $row->barras;
		$data['moneda']  = 'Bs.F.';
		$this->load->view('view_rprecios', $data);
		return 1;
	}

}
?>