<?php
require_once ('weather_config.inc.php'); 

require_once ($gc_jpgraph_path.'/jpgraph.php');
require_once ($gc_jpgraph_path.'/jpgraph_line.php'); 
require_once ($gc_jpgraph_path.'/jpgraph_utils.inc.php'); 
require_once ($gc_jpgraph_path.'/jpgraph_scatter.php');


//$date_start = date_sub(Now(), INTERVAL 1 DAY);
$date_start = strtotime("-1 days");
$day = date( 'Y-m-d', $date_start ); // $day is now "2012-03-09"
$hour = (int)date( 'H', $date_start ); // $hour is now (int)16
$date_start = strtotime( "$day $hour:00:00" );
$date_end =  time();
$day = date( 'Y-m-d', $date_end ); // $day is now "2012-03-09"
$hour = (int)date( 'H', $date_end ); // $hour is now (int)16
$date_end = strtotime( "$day $hour:59:29" );

$con = mysql_connect($database_host,$database_user,$database_pass);
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}

mysql_select_db($database_name, $con);
//unix_timestamp(DATE_FORMAT(timestamp, '%Y%m%d%H0000'))
//						where timestamp >= timestamp(date_sub(Now(), INTERVAL 1 DAY))

$result = mysql_query("select unix_timestamp(timestamp)
						, ROUND(avg(value),1)
						, extract(HOUR FROM timestamp)
						, sensor_id
						from weather_vals 
						where timestamp >= timestamp(date_sub(Now(), INTERVAL 1 DAY))					
						and ( sensor_id = 1
						or  sensor_id = 3 
						or  sensor_id = 6
						or  sensor_id = 13)
						group by sensor_id, DATE(timestamp), extract(HOUR FROM timestamp) 
						ORDER BY timestamp, id");


$i=0;
$j=0;
$k=0;
$l=0;
$m=0;
$n=0;
$o=0;

$xdata3=array();
$ydata3=array();
$xdata6=array();
$ydata6=array();
$xdatas13=array();
$ydatas13=array();

while ($array=mysql_fetch_array($result)) {
	$xdatat[$l]=$array[0];
	$l++;
	switch ($array[3]) {
	case 1:
		$xdata[$i]=$array[0];
		$ydata[$i]=$array[1];
		if ($i == 0) {
		  $xdatami[0] = $xdata[$i];
		  $ydatami[0] = $ydata[$i];
		  $xdatama[0] = $xdata[$i];
		  $ydatama[0] = $ydata[$i];
		}
		if ($ydata[$i] >= $ydatama[0]) {
		  $xdatama[0] = $xdata[$i];
		  $ydatama[0] = $ydata[$i];
		}
		if ($ydata[$i] <= $ydatami[0]) {
		  $xdatami[0] = $xdata[$i];
		  $ydatami[0] = $ydata[$i];
		}
		$i++; 
		break;
	case 2:
		$xdata2[$j]=$array[0];
		$ydata2[$j]=$array[1];
		$j++; 
		break;
	case 3:
		$xdatam[3][$k]=$array[0];
		$ydatam[3][$k]=$array[1];
		$k++; 
		break;
	case 6:
		$xdata6[$m]=$array[0];
		$ydata6[$m]=$array[1];
		$m++; 
		break;	
//	case 8:
//		$xdata8[$n]=$array[0];
//		$ydata8[$n]=$array[1];
//		$n++; 
//		break;

// Windgeschwindigkeit
	case 13: 
		$xdatas13[$o]=$array[0];
		$ydatas13[$o]=$array[1];
		$o++; 
		break;	
	}
}; 

// Niederschlag
$result = mysql_query("select unix_timestamp(timestamp)
						, ROUND(value,1)
						, extract(HOUR FROM timestamp)
						, sensor_id
						from weather_vals 
						where timestamp >= timestamp(date_sub(Now(), INTERVAL 1 DAY))
						and ( sensor_id = 8  
						or  sensor_id = 10 )
						ORDER BY timestamp, id");

$i=0;
$j=0;
$k=0;
$l=0;
$m=0;
$n=0;
$xdata8=array();
$ydata8=array();
$xdata9=array();
$ydata9=array();

while ($array=mysql_fetch_array($result)) {
//	$xdatat[$l]=$array[0];
	$l++;
	switch ($array[3]) {
	case 8:
		$xdata8[$n]=$array[0];
		$ydata8[$n]=$array[1];
		$n++; 
		break;
	case 10:
		$xdata9[$m]=$array[0];
		$ydata9[$m]=$array[1];
		$m++; 
		break;
	}
}; 

$result = mysql_query("select unix_timestamp(timestamp(str_to_date(concat(DATE_FORMAT(timestamp,'%d-%m-'),year(timestamp)+1,DATE_FORMAT(timestamp,' %H:%i:%s')),'%d-%m-%Y %H:%i:%s')))
						, ROUND(avg(value),1)
						, extract(HOUR FROM timestamp)
						, sensor_id
						from weather_vals 
						where ( timestamp >= timestamp(date_sub(str_to_date(concat(DATE_FORMAT(now(),'%d-%m-'),year(now())-1,DATE_FORMAT(now(),' %H:%i:%s')),'%d-%m-%Y %H:%i:%s'), INTERVAL 1 DAY))
						      and  timestamp <= timestamp(str_to_date(concat(DATE_FORMAT(now(),'%d-%m-'),year(now())-1,DATE_FORMAT(now(),' %H:%i:%s')),'%d-%m-%Y %H:%i:%s')) )
						and ( sensor_id = 1
						    or  sensor_id = 3 
						    or  sensor_id = 6 )
						group by sensor_id, DATE(timestamp), extract(HOUR FROM timestamp) 
						ORDER BY timestamp, id");

$i=0;
$j=0;
$k=0;
$l=0;
$m=0;


while ($array=mysql_fetch_array($result)) {
    $dattim_ly = $array[0]; 
	$xdata10t[$l]=$dattim_ly;
	$l++;
	switch ($array[3]) {
	case 1:
		$xdata10[$i]=$dattim_ly;
		$ydata10[$i]=$array[1];
		$i++; 
		break;
	case 2:
		$xdata12[$j]=$dattim_ly;
		$ydata12[$j]=$array[1];
		$j++; 
		break;
	case 3:
		$xdata13[$k]=$dattim_ly;
		$ydata13[$k]=$array[1];
		$k++; 
		break;
	case 6:
		$xdata16[$m]=$dattim_ly;
		$ydata16[$m]=$array[1];
		$m++; 
		break;
	}
}; 

mysql_close($con);


// Create the graph. These two calls are always required
$graph = new Graph($gc_graph_width,$gc_graph_height);
//$graph->SetScale('intlin',$gc_scale_ot_low,$gc_scale_ot_high,(min($xdatat)-4000),(max($xdatat)+4000)); 
$graph->SetScale('intlin',$gc_scale_ot_low,$gc_scale_ot_high,($date_start),($date_end)); 
$graph->img->SetMargin($gc_graph_margin_left,$gc_graph_margin_right,$gc_graph_margin_top,$gc_graph_margin_bottom); 

$graph->legend->Pos( 0.5, 0.99, 'center', 'bottom');
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->SetColumns(3);
$graph->legend->SetColor("black");
//$graph -> legend -> SetFont(FF_FONT1 ,FS_NORMAL);
$graph->legend->SetFillColor('white'); 
$graph->SetFrame(true);

if (count($xdatam[3]) > 0){
	$Y2ScaleMin=$gc_scale_wl_low;
	if (min($ydatam[3]) < $Y2ScaleMin){
		$Y2ScaleMin=min($ydatam[3]);
	}

	$Y2ScaleMax=$gc_scale_wl_high;
	if (max($ydatam[3]) > $Y2ScaleMax){
		$Y2ScaleMax=max($ydatam[3]);
	}

	$graph->SetY2Scale('lin',$Y2ScaleMin,$Y2ScaleMax);
}


$y=0;
if (count($xdata6) > 0){
	$graph->SetYScale($y,'lin',$gc_scale_ap_low,$gc_scale_ap_high); 
	    $y++;
}
$lv_rain_axis = 0;
if (count($xdata8) > 0){
	$graph->SetYScale($y,'lin',$gc_scale_rf_low,$gc_scale_rf_high); 
		$lv_rain_axis = 1;
}

if (count($xdata9) > 0){
	if ($lv_rain_axis == 0){
		$y++;
		$graph->SetYScale($y,'lin',$gc_scale_rf_low,$gc_scale_rf_high); 
	}
	$y++;
}
if (count($xdatas13) > 0){
	$graph->SetYScale($y,'lin',0,20); 
	    $y++;
}

$graph->xaxis->SetPos('min');

$dateUtils = new DateScaleUtils();

list($tickPos,$minTickPos) = $dateUtils->getTicks($xdatat,DSUTILS_HOUR1);
//$graph->xaxis->SetTickPositions($tickPos,$minTickPos);

// Setup the labels to be correctly format on the X-axis
//$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->xaxis->SetLabelAngle(90);

// The second paramter set to 'true' will make the library interpret the
// format string as a date format. We use a Month + Year format
$graph->xaxis->SetLabelFormatString('H:00',true);


// Create the linear plot
$lineplot=new LinePlot($ydata,$xdata);
$lineplot->SetColor('black');
$lineplot->setWeight(2);
// Legende generieren
$lineplot -> SetLegend ($gc_legend_ot);


	
//$lineplot2=new LinePlot($ydata2,$xdata2);
//$lineplot2->SetColor('green');
//$lineplot2->setWeight(2);
//$lineplot2 -> SetLegend("Innentemperatur ".$act_val2." °C");

// Add the plot to the graph
$graph->Add($lineplot);
//$graph->Add($lineplot2);
$graph->yaxis->SetColor(gray3);
$graph->yaxis->title->Set("°C");
$graph->yaxis->title->SetColor(gray3);


$y=0;

if (count($xdata10) > 0){
	$lineplot10=new LinePlot($ydata10,$xdata10);
	$lineplot10->SetColor('gray3');
	$lineplot10->setWeight(1);
	$graph->Add($lineplot10);
    $lineplot10 -> SetLegend ($gc_legend_ot_ly);
}

if (count($xdatam[3]) > 0){

	$lineplot3=new LinePlot($ydatam[3],$xdatam[3]);
	$lineplot3->SetColor('blue');
	$lineplot3->setWeight(2);
	$lineplot3->SetFillColor('blue@0.9');
	$lineplot3->SetFillFromYMin();
	$lineplot3 -> SetLegend($gc_legend_wl); 

	$graph->AddY2($lineplot3);
	$graph->y2axis->SetColor("blue");
	$graph->y2axis->title->Set("cm");
	$graph->y2axis->title->SetColor("blue");
    $graph->y2axis->SetTitleMargin(30);
//    $graph->y2axis->SetPos('max');
//    $graph->y2axis->SetTitleSide(SIDE_TOP);
}

if (count($xdata6) > 0){

	$lineplot6=new LinePlot($ydata6,$xdata6);
	$lineplot6->SetColor('orange');
	$lineplot6->setWeight(2);
	//	$lineplot6->SetFillColor('orange@0.9');
	//	$lineplot6->SetFillFromYMin();
	$lineplot6 -> SetLegend($gc_legend_ap); 

	$graph->AddY($y,$lineplot6);
	$graph->ynaxis[$y]->SetColor("orange");
	$graph->ynaxis[$y]->title->Set("hPa");
	$graph->ynaxis[$y]->title->SetColor("orange");
	$graph->ynaxis[$y]->title->SetMargin(15);
    $y++;
}

$lv_rain_axis = 0;

if (count($xdata8) > 0){

	$lineplot8=new LinePlot($ydata8,$xdata8);
	$lineplot8->SetColor('darkgreen');
	$lineplot8->setWeight(2);

	$lineplot8 -> SetLegend($gc_legend_rf); 

	$graph->AddY($y,$lineplot8);
	$graph->ynaxis[$y]->SetColor("darkgreen");
	$graph->ynaxis[$y]->title->Set("mm");
	$graph->ynaxis[$y]->title->SetColor("darkgreen");
    $graph->ynaxis[$y]->title->SetMargin(5);
	$lv_rain_axis = 1;
}

if (count($xdata9) > 0){

	$lineplot9=new LinePlot($ydata9,$xdata9);
	$lineplot9->SetColor('darkolivegreen3');
	$lineplot9->setWeight(2);

	$lineplot9 -> SetLegend($gc_legend_rfd); 

	//if ($lv_rain_axis == 0){
		$graph->AddY($y,$lineplot9);
		$graph->ynaxis[$y]->SetColor("darkolivegreen3");
		$graph->ynaxis[$y]->title->Set("mm");
		$graph->ynaxis[$y]->title->SetColor("darkolivegreen3");
		$graph->ynaxis[$y]->title->SetMargin(5);
		//$y++;
	//}
	$y++;

}

if (count($xdatas13) > 0){

	$lineplots13=new LinePlot($ydatas13,$xdatas13);
	$lineplots13->SetColor('blueviolet');
	$lineplots13->setWeight(2);

	$lineplots13 -> SetLegend('Windgeschwindigkeit'); 

	$graph->AddY($y,$lineplots13);
	$graph->ynaxis[$y]->SetColor("blueviolet");
	$graph->ynaxis[$y]->title->Set("km/h");
	$graph->ynaxis[$y]->title->SetColor("blueviolet");
    $graph->ynaxis[$y]->title->SetMargin(5);	
    $y++;
}

$lineplotmi = new LinePlot($ydatami,$xdatami);
$lineplotmi->mark->SetType(MARK_UTRIANGLE);
$lineplotmi->mark->SetFillColor("black@.3");
$lineplotmi->mark->SetWidth(8);
$lineplotmi->value->SetAlign('center','top');
if ($ydatami[0] < 0){
  $lineplotmi->value->SetMargin(8);
}
else {  
  $lineplotmi->value->SetMargin(-8);
}
$lineplotmi->value->Show();
$graph->Add($lineplotmi);

$lineplotma = new LinePlot($ydatama,$xdatama);
$lineplotma->mark->SetType(MARK_DTRIANGLE);
$lineplotma->mark->SetFillColor("black@.3");
$lineplotma->mark->SetWidth(8);
$lineplotma->value->SetAlign('center','bottom');
if ($ydatama[0] < 0){
  $lineplotma->value->SetMargin(-8);
}
else {  
  $lineplotma->value->SetMargin(8);
}
$lineplotma->value->Show();
$graph->Add($lineplotma);

$graph->title->Set('Durchschnittstwerte pro Stunde der letzten 24 Stunden');

// Display the graph
$graph->Stroke(); 


?> 