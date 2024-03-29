<?php
include('pChart/pData.class');
include('pChart/pChart.class');

class imgraf{

	function imgraf(){

	}

	function pie($valores,$label,$titulo){
		$nombre=tempnam('/tmp', 'g').'.png';

		$DataSet = new pData;
		$DataSet->AddPoint($valores,'Serie1');
		$DataSet->AddPoint($label  ,'Serie2');
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie('Serie2');

		$Test = new pChart(300,200);
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',8);
		$Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,295,195,5,230,230,230);

		$Test->AntialiasQuality = 0;
		$Test->setShadowProperties(2,2,200,200,200);
		$Test->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),120,110,60,PIE_PERCENTAGE,8);
		$Test->clearShadow();
		$Test->drawPieLegend(210,30,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
		$Test->drawTitle(10,22,$titulo,50,50,50,300);
		$Test->Render($nombre);
		return $nombre;
	}

	function bar($valores,$label,$titulo){
		$nombre=tempnam('/tmp', 'g').'.png';

		$DataSet = new pData;

		foreach($valores AS $ind=>$val){
			$DataSet->AddPoint(array($val),'Serie'.$ind);
			$DataSet->SetSerieName($label[$ind] ,'Serie'.$ind);
		}

		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie();

		$Test = new pChart(300,200);
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',8);
		$Test->setGraphArea(60,40,250,180);
		$Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,295,195,5,230,230,230);
		$Test->drawGraphArea(255,255,255,TRUE);
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,true,0,0);
		$Test->drawGrid(4,TRUE,200,200,200,50);

		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',6);
		$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
		$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);
		//$Test->drawOverlayBarGraph($DataSet->GetData(),$DataSet->GetDataDescription());
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',8);
		$Test->drawLegend(220,7,$DataSet->GetDataDescription(),255,255,255);
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',10);
		$Test->drawTitle(10,22,$titulo,50,50,50,300);
		$Test->Render($nombre);

		return $nombre;
	}

	function line($valores,$titulo,$label=array()){
		$nombre=tempnam('/tmp', 'g').'.png';

		$DataSet = new pData;

		$ind=1;
		$DataSet->AddPoint($valores,'Serie'.$ind);

		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie();
		$DataSet->SetSerieName($titulo ,'Serie'.$ind);

		if(count($label)>0){
			$ID=0;
			foreach($label AS $val){
				$DataSet->Data[$ID]["Name"]=$val;
				$ID++;
			}
		}

		$Test = new pChart(300,200);
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',8);
		$Test->setGraphArea(70,30,280,170);
		$Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,295,195,5,230,230,230);
		$Test->drawGraphArea(255,255,255,TRUE);
		$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,true,0,0);
		$Test->drawGrid(4,TRUE,200,200,200,50);

		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',6);
		$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
		$Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());

		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',8);
		$Test->drawLegend(200,9,$DataSet->GetDataDescription(),255,255,255);
		$Test->setFontProperties(APPPATH.'libraries/pChart/Fonts/tahoma.ttf',10);
		//$Test->drawTitle(10,22,$titulo,50,50,50,300);
		$Test->Render($nombre);

		return $nombre;
	}
}
