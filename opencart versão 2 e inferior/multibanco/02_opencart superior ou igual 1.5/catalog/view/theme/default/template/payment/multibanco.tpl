<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
	<table cellpadding="3" width="300px" cellspacing="0" style="margin-top: 10px;border: 1px solid #45829F; background-color: #FFFFFF;" align="center">
			<tr>
				<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #45829F; background-color: #45829F; color: White" colspan="3"><div align="center">Pagamento por Multibanco ou Homebanking</div></td>
			</tr>
			<tr>
		        <td rowspan="3"><div align="center"><img src="https://dl.dropboxusercontent.com/u/1966551/mb_logo_large.png" alt="" width="52" height="60"/></div></td>
		        <td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>
		        <td style="font-size: x-small; text-align:left"><?php echo $entry_entidade; ?></td>
			</tr>
			<tr>
				<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>
				<td style="font-size: x-small; text-align:left"><?php echo $entry_referencia; ?></td>
			</tr>
			<tr>
				<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>
				<td style="font-size: x-small; text-align:left"><?php echo $entry_valor; ?></td>
			</tr>
			<tr>
				<td style="font-size: xx-small;border-top: 1px solid #45829F; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #45829F; color: White" colspan="3">O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</td>
			</tr>
		</table>
		<h2>NOTA: Anote estes dados antes de confirmar a encomenda pf.</h2>
</div>
<div class="buttons">
  <div class="right"><a id="button-confirm" class="button"><span><?php echo $button_confirm; ?></span></a></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'GET',
		url: 'index.php?route=payment/multibanco/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script> 
