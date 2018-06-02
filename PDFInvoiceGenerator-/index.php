<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PDF Invoice Generator PHP </title>
<link href="css/style.css" rel="stylesheet" type="text/css">
<!--[if IE 6]><link type="text/css" rel="stylesheet" href="css/ie6.css"/><![endif]-->
<!--[if IE 7]><link type="text/css" rel="stylesheet" href="css/ie7.css"/><![endif]-->
<!--[if IE 8]><link type="text/css" rel="stylesheet" href="css/ie8.css"/><![endif]-->
</head>
<body>
<header>
<div id="header">
<div class="container"><h1>PDF Invoice Generator PHP</h1>
</div>
</div>
</header>
<section class="container">
<div id="container">
<?php 
function invoice_num(){
	
$folder= 'invoices/';
$prefix_invoice='INVO-';

if($openFD=opendir($folder)){ 
     while (($folder_date=readdir($openFD))!==false){
		  
      		if ((!is_file($folder_date))and($folder_date!='.')and($folder_date!='..')) 
          		$array_folder_date[]=$folder_date; 
     		} 
     closedir($openFD); 
	 
	 		if($openFI=opendir($folder.@end($array_folder_date))){ 
				 while (($invoice=readdir($openFI))!==false){ 
      					if ((!is_file($invoice))and($invoice!='.')and($invoice!='..')) 
          					$array_invoices[]=$invoice; 
     					} 
     			 closedir($openFI);  
 			}
			
	if(isset($array_invoices)){	
      $end_invoice = end($array_invoices);
	}else{
	  $end_invoice= $prefix_invoice.'0000';
	}
   preg_match_all('/(?P<prefix>\w+)-(?P<num>\d+)/', $end_invoice, $end_number);
   
   $new_num=$end_number['num'][0] +1;
  
  if(strlen($new_num)<=1){
	  $new_num='000'.$new_num;
  }else if(strlen($new_num)<=2){
  	  $new_num= '00'.$new_num;
  }else if(strlen($new_num)<=3){
  	  $new_num='0'.$new_num;
  }
  
  return $prefix_invoice.$new_num;
  
 }
 
}
$row_num = 0;

?>
<form action="invoice.php" method="post" id="formInvoice">
<fieldset id="customer_data">
<legend>Customer Data</legend>
<p class="item_form">
<label for="customer[num]">Customer Nº</label><br />
<input type="text" name="customer[num]" id="customer[num]" class="text_input" value="CUST-0001">
</p>
<p class="item_form">
<label for="customer[name]">Customer Name</label><br />
<input type="text" name="customer[name]" id="customer[name]" class="text_input" value="John Doe">
</p>
<p class="item_form">
<label for="customer[address]">Address</label><br />
<input type="text" name="customer[address]" id="customer[address]" class="text_input" value="Customer Address Nº 11">
</p>
<p class="item_form">
<label for="customer[postal_code]">Postal Code:</label><br />
<input type="text" name="customer[postal_code]" id="customer[postal_code]" class="text_input" value="31659">
</p>
<p class="item_form">
<label for="customer[city]">City</label><br />
<input type="text" name="customer[city]" id="customer[city]" class="text_input" value="New York">
</p>
<p class="item_form">
<label for="customer[country]">Country</label><br />
<input type="text" name="customer[country]" id="customer[country]" class="text_input" value="EEUU">
</p>
<p class="item_form">
<label for="customer[ident]">Identification</label><br />
<input type="text" name="customer[ident]" id="customer[ident]" class="text_input" value="NY-5484EN">
</p>
<br clear="all">
</fieldset>
<fieldset id="company_data">
<legend>Company Data</legend>
<p class="item_form">
<label for="company[name]">Company Name</label><br />
<input type="text" name="company[name]" id="company[name]" class="text_input" value="Company Name">
</p>
<p class="item_form">
<label for="company[address]">Address</label><br />
<input type="text" name="company[address]" id="company[address]" class="text_input" value="Company Address Nº 254">
</p>
<p class="item_form">
<label for="company[postal_code]">Postal Code:</label><br />
<input type="text" name="company[postal_code]" id="company[postal_code]" class="text_input" value="31660">
</p>
<p class="item_form">
<label for="company[city]">City</label><br />
<input type="text" name="company[city]" id="company[city]" class="text_input" value="New York">
</p>
<p class="item_form">
<label for="company[phone]">Phone</label><br />
<input type="text" name="company[phone]" id="company[phone]" class="text_input" value="000-000000">
</p>
<p class="item_form">
<label for="company[fax]">Fax</label><br />
<input type="text" name="company[fax]" id="company[fax]" class="text_input" value="000-000000">
</p>
<p class="item_form">
<label for="company[ident]">Identification</label><br />
<input type="text" name="company[ident]" id="company[ident]" class="text_input" value="NY-5489EN">
</p>
<p class="item_form">
<label for="company[email]">Email</label><br />
<input type="text" name="company[email]" id="company[email]" class="text_input" value="yoycompany@youcompany.com">
</p>
<p class="item_form">
<label for="company[web]">Web</label><br />
<input type="text" name="company[web]" id="company[web]" class="text_input" value="http://youcompany.com">
</p>
<br clear="all">
</fieldset>

<p class="invoce_num">
<label for="invoce_num">Nº Invoice</label><br />
<input type="text" name="invoce_num" id="invoce_num" class="txt_invoce_num" value="<?php echo invoice_num();?>">
</p>

<p class="tax">
<label for="tax">TAX</label><br />
<input type="text" name="tax" id="tax" class="txt_tax" value="20"> %
</p>

<p class="date">
<label for="date">Date</label><br />
<input type="text" name="date" id="date" class="txt_date" value="<?php echo date('Y-m-d');?>">
</p>

<p class="shipping">
<label for="date">Shipping</label><br />
<input type="text" name="shipping" id="shipping" class="txt_shipping" value="12.40">
</p>

<p class="payment_m">
<label for="payment_m" style="width:120px;">Payment Method</label><br />
<input type="text" name="payment_m" id="payment_m" class="txt_payment_m" value="PayPal">
</p>


<a href="#" id="generate">Generate PDF</a>
<a href="#" id="addproduct">+ add product</a>

<br clear="all" /><br />
<table id="list" cellpadding="0" cellspacing="0" width="980">
<thead>
<tr>
<th width="15">Type</th>
<th align="left">Description</th>
<th width="45">Qty.</th>
<th width="90" align="left">Price</th>
<th width="15"></th>
</tr>
</thead>
<tbody id="row-<?php echo $row_num; ?>">
<tr>
<td align="center"><input type="text" name="products[<?php echo $row_num; ?>][type]" id="products[<?php echo $row_num; ?>][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[<?php echo $row_num; ?>][description]" id="products[<?php echo $row_num; ?>][description]" class="txt_name" value="Product example: 331 (EN)"></td>
<td align="center"><input type="text" name="products[<?php echo $row_num; ?>][quantity]" id="products[<?php echo $row_num; ?>][quantity]" class="txt_quantity" maxlength="3" value="1"></td>
<td><input type="text" name="products[<?php echo $row_num; ?>][price]" id="products[<?php echo $row_num; ?>][price]" class="txt_price" maxlength="7" value="1025.95"> $</td>
<td><a onclick="$('#row-<?php echo $row_num; ?>').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-1">
<tr>
<td align="center"><input type="text" name="products[1][type]" id="products[1][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[1][description]" id="products[1][description]" class="txt_name" value="المنتج المثال: 332 (AR)"></td>
<td align="center"><input type="text" name="products[1][quantity]" id="products[1][quantity]" class="txt_quantity" maxlength="3" value="1"></td>
<td><input type="text" name="products[1][price]" id="products[1][price]" class="txt_price" maxlength="7" value="1552.95"> $</td>
<td><a onclick="$('#row-1').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-2">
<tr>
<td align="center"><input type="text" name="products[2][type]" id="products[2][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[2][description]" id="products[2][description]" class="txt_name" value="produkt pøíklad: 333 (CZ)"></td>
<td align="center"><input type="text" name="products[2][quantity]" id="products[2][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[2][price]" id="products[2][price]" class="txt_price" maxlength="7" value="8.95"> $</td>
<td><a onclick="$('#row-2').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-3">
<tr>
<td align="center"><input type="text" name="products[3][type]" id="products[3][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[3][description]" id="products[3][description]" class="txt_name" value="Продукт примера: 334 (RU)"></td>
<td align="center"><input type="text" name="products[3][quantity]" id="products[3][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[3][price]" id="products[3][price]" class="txt_price" maxlength="7" value="19.55"> $</td>
<td><a onclick="$('#row-3').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-4">
<tr>
<td align="center"><input type="text" name="products[4][type]" id="products[4][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[4][description]" id="products[4][description]" class="txt_name" value="Ürün örneği: 335 (TR)"></td>
<td align="center"><input type="text" name="products[4][quantity]" id="products[4][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[4][price]" id="products[4][price]" class="txt_price" maxlength="7" value="15.45"> $</td>
<td><a onclick="$('#row-4').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-5">
<tr>
<td align="center"><input type="text" name="products[5][type]" id="products[5][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[5][description]" id="products[5][description]" class="txt_name" value="
exemple de produit: 336 (FR)"></td>
<td align="center"><input type="text" name="products[5][quantity]" id="products[5][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[5][price]" id="products[5][price]" class="txt_price" maxlength="7" value="25.95"> $</td>
<td><a onclick="$('#row-5').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-8">
<tr>
<td align="center"><input type="text" name="products[8][type]" id="products[8][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[8][description]" id="products[8][description]" class="txt_name" value="Produktbeispiel: 337 (DE)"></td>
<td align="center"><input type="text" name="products[8][quantity]" id="products[8][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[8][price]" id="products[8][price]" class="txt_price" maxlength="7" value="5.95"> $</td>
<td><a onclick="$('#row-8').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tbody id="row-8">
<tr>
<td align="center"><input type="text" name="products[8][type]" id="products[8][type]" class="txt_type" maxlength="1" value="P"></td>
<td><input type="text" name="products[8][description]" id="products[8][description]" class="txt_name" value="Poducto de ejemplo: 338 (ES)"></td>
<td align="center"><input type="text" name="products[8][quantity]" id="products[8][quantity]" class="txt_quantity" maxlength="3" value="2"></td>
<td><input type="text" name="products[8][price]" id="products[8][price]" class="txt_price" maxlength="7" value="1.95"> $</td>
<td><a onclick="$('#row-8').remove();" class="delete">X</a></td>
</tr>
</tbody>
<tfoot>
</tfoot>
</table>
</form>

</div>
</section>
<br /> <br />
<footer><div id="footer"></div></footer>


<div id="modal_box">
<a href="#" id="close_modal">X</a>
<span class="generate_txt">Generating PDF invoice...</span>
<span class="loader"></span>
</div>

<div id="modal"></div>

<script src="js/jquery.min.js" type="text/javascript"></script>
<script>
(function($) {
var row_num=<?php echo $row_num+8; ?>;
$("#addproduct").click(function(e){
	e.preventDefault();
	var html='<tbody id="row-'+row_num+'">'+
	'<tr>'+
	'<td align="center"><input type="text" name="products['+row_num+'][type]" id="products['+row_num+'][type]" class="txt_type" maxlength="1" value="P"></td>'+
	'<td><input type="text" name="products['+row_num+'][description]" id="products['+row_num+'][description]" class="txt_name" value="Product example: 33'+row_num+' (EN)"></td>'+
	'<td align="center"><input type="text" name="products['+row_num+'][quantity]" id="products['+row_num+'][quantity]" class="txt_quantity" maxlength="3" value="1"></td>'+
	'<td><input type="text" name="products['+row_num+'][price]" id="products['+row_num+'][price]" class="txt_price" maxlength="7" value="2.54"> $</td>'+
	'<td><a onclick="$(\'#row-'+row_num+'\').remove();" class="delete">X</a></td>'+
	'</tr>'+
	'</tbody>';
	$('#list thead').after(html);
	row_num++;
});

var txt_redir = '<a href="#" id="close_modal">X</a>'+
				'<span class="generate_txt">Generating PDF invoice...</span>';
var loader = '<span class="loader"></span>';
	
$("#generate").click(function(e){
	e.preventDefault();
	var data = $('#formInvoice').serialize()+'&on=onajax'
	$.ajax({
          url: 'invoice.php',
          type: "POST",
          data:data,
          dataType: "json",
          beforeSend: function() {
            loadModal(true);
          },
          success: function(data){
			  $('.generate_txt').text('Generating PDF invoice...');
			  if(data.success){
				  setTimeout(function(){
					  $('.generate_txt').text('Redirecting...');
					  setTimeout(function(){
					  	$('#modal').fadeOut();
					  	$('#modal_box').fadeOut();
					  	$('#formInvoice').submit()
					  },1500);
					  },2000);
			   }else{
				  var er='<a href="#" id="close_modal">X</a>'+
				  		 '<h2>Errores</h2>'+
						 '<span>Se han encontrado los siguientefdgdfg fdgdfgfdg dfgdfg dfgdfs errores</span><br /><br />';
				  var i=1;
				  $.each(data.error,function(key,val){
					er += '<strong>'+i+'</strong>. '+'<span class="error">'+val+'</span><br />';
					i++;
				  });
				   setTimeout(function(){
				  $('#modal_box').html(er);
				   },1500);
			  }
          }
	});
});
	
$('#modal,#close_modal').live('click',function(){
	$('#modal').hide();
	$('#modal_box').hide();
	$('#modal_box').html(txt_redir+loader)
});
function loadModal(v){
		if(v==false && $('#modal').css('display')=='none'){
		return false;
		}
	    var box = $('#modal_box');
		var box_w = 300;
		var box_h = 200;
		var winH = $(document).height();
        var winBoxH = $(window).height();
	    var winW = $(window).width();
        $('#modal').css({'width':winW,'height':winH});
		box.css({'width':box_w,'height':box_h,'top':(winBoxH/2 - box_h/2),'left':(winW/2 - box_w/2)});
		$('#modal').fadeIn();
		$('#modal_box').fadeIn();
		$('#modal_box').html();
		
}
$(window).resize(function () {
 		loadModal(false);
});
}(jQuery));
</script>
</body>
</html>
