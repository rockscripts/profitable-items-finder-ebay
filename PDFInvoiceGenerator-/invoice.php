<?php

/**********************************
* 						          *
* PDF INVOICE GENERATOR           *
*                                 *
* Version: 1.1                    *
* Date:    2013-04-16             *
* Author:  Sergio D. Sánchez      *
*								  *
***********************************/

require_once('tfpdf.php');

define('decimal_symbol',','); // Decimal Symbol 152,56
define('thousand_symbol','.'); // Thousand Symbol 1.255,22

class Invoice extends tFPDF {
var $ang=0;

function Rotate($ang,$x=-1,$y=-1){
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->ang!=0)
        $this->_out('Q');
    $this->ang=$ang;
    if($ang!=0){
        $ang*=M_PI/180;
        $c=cos($ang);
        $s=sin($ang);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}

function RotatedText($x,$y,$txt,$ang){
	$this->SetFont('UniFont','',6);
    $this->SetTextColor(103,103,103);
    $this->Rotate($ang,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
}

function Head($data){
	
	// LOGO
	$this->Image('logo.jpg',10,8,105);
	
	//COMPANY NAME
	$this->SetY(6);
	$this->SetX(122);
	$this->SetFont('UniFont','',24);
	$this->SetTextColor($data['color']['red'],$data['color']['green'],$data['color']['blue']);
    $this->Cell(80,11,$data['company_data']['name'],0,1,'R');
	
	// ADDRESS, PHONE, FAX, EMAIL, ETC..
	
	$this->SetFont('UniFont','',10);
	$this->SetTextColor(109,109,109);
	
	//ADDRESS
	$this->SetX(122);
	$this->Cell(80,5,$data['company_data']['address'],0,1,'R');
	
	// POSTAL CODE
	$this->SetX(122);
	$this->Cell(80,5,$data['company_data']['postal_code'].' - '.$data['company_data']['city'],0,1,'R');
	
	// PHONE
	$this->SetX(122);
	$this->Cell(80,5,$data['text']['phone'].' '.$data['company_data']['phone'],0,1,'R');
	
	// FAX
	$this->SetX(122);
	$this->Cell(80,5,$data['text']['fax'].' '.($data['company_data']['fax']!='')?$data['company_data']['fax']:'',0,1,'R');
	
	// IDENTI
	$this->SetX(122);
	$this->Cell(80,5,$data['text']['document_id'].' '.mb_strtoupper($data['company_data']['ident'],'UTF-8'),0,1,'R');
	
	// EMAIL
	$this->SetX(122);
	$this->Cell(80,5,$data['text']['email'].' '.$data['company_data']['email'],0,1,'R');
	
	// WEB
	$this->SetX(122);
	$this->Cell(80,5,$data['company_data']['web'],0,1,'R');
  
	
	// CUSTOMER DATA /////////////////////////////////////////////////////////////////////////////
	
	$this->SetFont('UniFont','',10);
	$this->SetTextColor(0,0,0);
	
	// TEXT CUSTOMER
	$this->SetY(54);
	$this->SetX(9);
	$this->Cell(80,8,$data['text']['customer'],0,1,'L');
	$this->SetFont('UniFont','',12);
	
	// CUSTOMER NAME
	$this->SetX(9);
	$this->Cell(80,5,mb_strtoupper($data['customer_data']['name'],'UTF-8'),0,1,'L');
	
	// CUSTOMER ADDRESS
	$this->SetX(9);
	$this->Cell(80,5,mb_strtoupper($data['customer_data']['address'],'UTF-8'),0,1,'L');
	
	// CUSTOMER POSTAL CODE
	$this->SetX(9);
	$this->Cell(80,5,$data['customer_data']['postal_code'].' - '.mb_strtoupper($data['customer_data']['city'],'UTF-8'),0,1,'L');
	
	// CUSTOMER COUNTRY 
	$this->SetX(9);
	$this->Cell(80,5,mb_strtoupper($data['customer_data']['country'],'UTF-8'),0,1,'L');
	
	// CUSTOMER IDENTY
	$this->SetX(9);
	$this->Cell(80,5,mb_strtoupper($data['text']['document_id']).' '.mb_strtoupper($data['customer_data']['ident'],'UTF-8'),0,1,'L');
	
	
	// INVOICE DATA
	
	$this->SetFont('UniFont','',12);
	$this->SetTextColor(0,0,0);
	
	// INVOICE DATA
	$this->SetY(58);
	$this->SetX(116);
	$this->Cell(60,7,mb_strtoupper($data['text']['invoice_num'],'UTF-8'),0,1,'R');
	
	$this->SetX(116);
	$this->Cell(60,7,mb_strtoupper($data['text']['date'],'UTF-8'),0,1,'R');
	
	$this->SetX(116);
	$this->Cell(60,7,mb_strtoupper($data['text']['customer_num'],'UTF-8'),0,1,'R');
	
	$this->SetY(58);
	$this->SetX(177);
	$this->Cell(25,7,mb_strtoupper($data['invoce_num'],'UTF-8'),0,1,'R');
	
	$this->SetX(177);
	$this->Cell(25,7,$data['date'],0,1,'R');
	
	$this->SetX(177);
	$this->Cell(25,7,mb_strtoupper($data['customer_data']['num'],'UTF-8'),0,1,'R');
	
	$this->SetX(107);
	$this->Cell(103,7,mb_strtoupper($data['text']['page'],'UTF-8').' '.$this->PageNo().' '.mb_strtoupper($data['text']['of'],'UTF-8').' {nb}',0,0,'R');
	   
}


function THead($text,$color)
{
    $this->SetFillColor($color['red'],$color['green'],$color['blue']);
    $this->SetTextColor(255);
    $this->SetDrawColor(123,122,122);
    $this->SetLineWidth(0.1);
	$this->SetFont('UniFont','',9);

	$this->SetY(90);
	$this->SetX(9);
	
    $w = array(12,90,20,10,20,20,20);

	$this->Cell($w[0],9,$text['type'],1,0,'C',true);
	$this->Cell($w[1],9,$text['desc'],1,0,'L',true);
	$this->Cell($w[2],9,$text['price'],1,0,'C',true);
	$this->Cell($w[3],9,$text['quantity'],1,0,'C',true);
	$this->Cell($w[4],9,$text['sum_price'],1,0,'C',true);
	$this->Cell($w[5],9,$text['sum_tax'],1,0,'C',true);
	$this->Cell($w[6],9,$text['pro_total'],1,0,'C',true);

	$this->Ln();
	
	$this->SetX(9);
	$this->Cell($w[0],138,'','LR',0,'C');
    $this->Cell($w[1],138,'','LR',0,'L');
    $this->Cell($w[2],138,'','LR',0,'C');
    $this->Cell($w[3],138,'','LR',0,'C');
	$this->Cell($w[4],138,'','LR',0,'C');
	$this->Cell($w[5],138,'','LR',0,'R');
	$this->Cell($w[6],138,'','LR',0,'R');
	$this->Ln();
	$this->SetX(9);

    $this->Cell(array_sum($w),0,'','T');
}

function Products($products)
{
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(0.1);
	$this->SetFont('UniFont','',8);
	$this->SetX(9);
	
    $w = array(12,90,20,10,20,20,20);
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
	$this->SetY(99);
	foreach($products as $product)
    {
		$this->SetX(9);
        $this->Cell($w[0],8,$product['type'],'',0,'C',false);
        $this->Cell($w[1],8,$product['description'],'',0,'L',false);
		$this->Cell($w[2],8,$product['price'],'',0,'C',false);
        $this->Cell($w[3],8,$product['quantity'],'',0,'C',false);
        $this->Cell($w[4],8,$product['sum_price'],'',0,'C',false);
		$this->Cell($w[5],8,$product['sum_tax'],'',0,'C',false);
		$this->Cell($w[6],8,$product['total'],'',0,'C',false);
        $this->Ln();
    }
}

function Base($data,$final=true)
{
    $this->SetFillColor($data['color']['red'],$data['color']['green'],$data['color']['blue']);
    $this->SetTextColor(255);
    $this->SetDrawColor(123,122,122);
    $this->SetLineWidth(0.1);
	$this->SetFont('UniFont','',11);
	$this->SetY(240);
	$this->SetX(9);
	
    $w = array(30,30,30,30);
    $this->Cell($w[0],7,$data['text']['sub_total'],1,0,'C',true);
	$this->Cell($w[1],7,$data['text']['tax_rate'],1,0,'C',true);
	$this->Cell($w[2],7,$data['text']['sum_tax'],1,0,'C',true);
	$this->Cell($w[3],7,$data['text']['shipping'],1,0,'C',true);

	$this->SetY(247);
	$this->SetX(9);
	$this->SetFont('UniFont','',10);
	$this->SetTextColor(0);
	$this->Cell($w[0],8,($final)?$data['base']['subtotal']:'- -','LR',0,'C');
    $this->Cell($w[1],8,($final)?$data['tax'].' %':'- -','LR',0,'C');
    $this->Cell($w[2],8,($final)?$data['base']['sum_tax']:'- -','LR',0,'C');
    $this->Cell($w[3],8,($final)?$data['shipping']:'- -','LR',0,'C');
	
	$this->SetY(255);
	$this->SetX(9);
    $this->Cell(array_sum($w),0,'','T');
}

function Payment($payment_method){

	$this->SetY(260);
	$this->SetX(8);
	$this->SetFont('UniFont','',11);
	$this->SetTextColor(0,0,0);
    $this->Cell(100,7,'Payment Method:',0,1,'L');
	
	$this->SetY(260);
	$this->SetX(45);
	$this->Cell(150,7,$payment_method,0,1,'L');
}

function Total($data){
	
	$this->SetY(238);
	$this->SetX(150);
	$this->SetFont('UniFont','',12);
	$this->SetTextColor(0,0,0);
    $this->Cell(27,7,mb_strtoupper($data['text']['sub_total'],'UTF-8').$data['text']['simbol_left'],0,1,'R');
	$this->SetX(150);
	$this->Cell(27,7,mb_strtoupper($data['text']['sum_tax'],'UTF-8').$data['text']['simbol_left'],0,1,'R');
	$this->SetX(150);
	$this->Cell(27,7,mb_strtoupper($data['text']['shipping'],'UTF-8').$data['text']['simbol_left'],0,1,'R');
	$this->SetY(269);
	$this->SetX(150);
	$this->Cell(27,7,mb_strtoupper($data['text']['total'],'UTF-8').$data['text']['simbol_left'],0,1,'R');
	
	
	$this->SetY(238);
	$this->SetX(178);
	$this->Cell(20,7,$data['base']['subtotal'].$data['text']['simbol_right'],0,1,'R');
	$this->SetX(178);
	$this->Cell(20,7,$data['base']['sum_tax'].$data['text']['simbol_right'],0,1,'R');
	$this->SetX(178);
	$this->Cell(20,7,$data['shipping'].$data['text']['simbol_right'],0,1,'R');
	$this->SetY(269);
	$this->SetX(178);
	$this->Cell(20,7,$data['base']['total'].$data['text']['simbol_right'],0,1,'R');
	
	
}

function NextIvoice($text){
	
	$this->SetFont('UniFont','',12);
	$this->SetTextColor(0,0,0);
	$this->SetY(258);
	$this->SetX(168);
	$this->Cell(31,7,$text.($this->PageNo()+1).' ...',0,1,'R');
}

function Footer(){
	
	global $AliasNbPages;
    $this->SetY(-10);
    $this->SetFont('UniFont','',9);
	$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
}}


?>