<table style="width: auto;min-width: 280px;max-width: 320px;padding: 5px;font-size: 11px;color: #374953;border: 1px solid #dddddd; margin-top: 10px;">
	<tbody>
		<tr>
			<td style="padding: 5px;"><div><img src="catalog/view/theme/default/image/payment/logo_mbway.png" alt="mbway"></div></td>
		</tr>
		<tr>
			<td style="padding: 5px 10px;">
				<div><b>Nº de telemóvel associado ao MBWAY:</b></div>
				<div><input type="text" name="telemovel" id="input-telemovel"></div>
				<div id="error-number" style="display: none;color:red;">Número de telemóvel inválido!</div>
			</td>
		</tr>
		<tr>
			<td style="color: #666;font-size: 11px;padding: 5px 10px;">
				<div>Se não tem a aplicação MBWAY no seu smartphone ou ainda não aderiu ao serviço, pode aderir em <a target="_blank" href="https://www.mbway.pt/#como-aderir">mbway.pt</a>.
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary"/>
    </div>
</div>

<script type="text/javascript"><!--
    $('#button-confirm').on('click', function () {
		$("#error-number").hide();
		var re=/^((91|96|92|93)[0-9]{7})$/g;
 
        if (!re.test($("#input-telemovel").val())) {
			$("#error-number").show();
			$("#input-telemovel").focus();
			return false;
		}
		
		$.ajax({
			url: 'index.php?telemovel='+ $("#input-telemovel").val() +'&route=extension/payment/ifthenpaymbway/confirm',
			dataType: 'json',
			type: 'get',
			success: function (json) {
				location = '<?php echo $continue; ?>';
			},
			beforeSend: function() {
				$('#button-confirm').button('loading');
				$("#button-confirm").prop('disabled', true);
			},
			
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});		
    });
//--></script>

