<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>

<script type="text/javascript">
function formatInt(input){
  var num = input.value.replace(/\//g,'');
  var num = input.value.replace(',','.');
  if(!isNaN(num)){
    if(num.indexOf('.') > -1) {
      alert("<?php echo $error_no_number2; ?>");
      input.value = input.value.substring(0,input.value.length-1);
    }else if(num.indexOf(',') > -1) {
      alert("<?php echo $error_no_number2; ?>");
      input.value = input.value.substring(0,input.value.length-1);
    }
  } else {
    alert("<?php echo $error_no_number; ?>");
    input.value = input.value.substring(0,input.value.length-1);
  }
}
</script>

<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/payment.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
		<tr>
          <td COLSPAN="2"><?php echo $entry_notice; ?></td>
        </tr>
        <tr>
          <td><?php echo $entidade; ?></td>
          <td><input type="text" name="multibanco_entidade" value="<?php echo $multibanco_entidade; ?>"  onkeyup="formatInt(this);" maxlength="5"/>
            <?php if ($error_entidade) { ?>
            <span class="error"><?php echo $error_entidade; ?></span>
            <?php } ?></td>
        </tr>
		<tr>
          <td><?php echo $sub_entidade; ?></td>
          <td><input type="text" name="multibanco_sub_entidade" value="<?php echo $multibanco_sub_entidade; ?>"  onkeyup="formatInt(this);" maxlength="3"/>
            <?php if ($error_sub_entidade) { ?>
            <span class="error"><?php echo $error_sub_entidade; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_geo_zone; ?></td>
          <td><select name="multibanco_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $multibanco_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="multibanco_status">
              <?php if ($multibanco_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>