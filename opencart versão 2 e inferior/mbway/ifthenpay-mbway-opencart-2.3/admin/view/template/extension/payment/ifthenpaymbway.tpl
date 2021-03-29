<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-bank-transfer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <img src="view/image/ifthenpaymbway.png" alt="" /> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
	  
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ifthenpaymbway" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-mbwkey"><span data-toggle="tooltip"><?php echo $entry_mbwkey; ?></span></label>
            <div class="col-sm-10">
              <input type="text"  name="ifthenpaymbway_mbwkey" value="<?php echo  $ifthenpaymbway_mbwkey; ?>" placeholder="<?php  $ifthenpaymbway_mbwkey; ?>" id="input-mbwkey" class="form-control" />
            </div>
          </div>
      
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="ifthenpaymbway_order_status_id" id="input-order-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $ifthenpaymbway_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status_complete"><?php echo $entry_order_status_complete; ?></label>
            <div class="col-sm-10">
              <select name="ifthenpaymbway_order_status_complete_id" id="input-order-status_complete" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $ifthenpaymbway_order_status_complete_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="ifthenpaymbway_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $ifthenpaymbway_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="ifthenpaymbway_status" id="input-status" class="form-control">
                <?php if ($ifthenpaymbway_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="ifthenpaymbway_sort_order" value="<?php echo $ifthenpaymbway_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>

          <?php if ($ifthenpaymbway_show_ap) { ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
              <p><?php echo $entry_cb; ?></p>
              <?php echo $entry_url; ?> <?php echo $ifthenpaymbway_url; ?><br/><br/><?php echo $entry_ap; ?><?php echo $ifthenpaymbway_ap; ?><br/>
              <input type="hidden" name="ifthenpaymbway_ap" value="<?php echo $ifthenpaymbway_ap; ?>"  class="form-control" />

              <!--Button to send auto-->
              <input type="hidden" id="input_token" name="token" value="<?php echo $token; ?>"  class="form-control" />
              <br/><input type="button" value="<?php echo $button_send_cb; ?>" id="button-send" class="btn btn-primary"/>
            </div>
          </div>
          <?php }?>
        </form>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
    $('#button-send').on('click', function () {
      <?php if ($email_cb_sended == '1') { ?>
        if (!confirm('<?php echo $email_sended_info; ?>')) {
          return false;
        }
      <?php } else { ?>
        if (!confirm('<?php echo $email_confirmation; ?>')) {
          return false;
        }
      <?php } ?>

      $.ajax({
			url: 'index.php?route=extensions/payment/ifthenpaymbway/activatecallback&token=' + $("#input_token").val(),
			dataType: 'json',
			type: 'get',
  		success: function (json) {
        if (json['sended']==true)
        { 
          alert('<?php echo $email_success_info; ?>');} else {alert('<?php echo $email_error_info; ?>');
        }
			},
			beforeSend: function() {
				$("#button-send").prop('disabled', true);
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
  });
</script>
<?php echo $footer; ?>