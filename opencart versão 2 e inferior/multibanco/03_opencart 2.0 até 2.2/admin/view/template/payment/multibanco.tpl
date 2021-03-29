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


    <?php if ($text_success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $text_success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>

    <script>
      $( document ).ready(function() {
        $.ajax({

          url: "<?php echo $url_set_modification; ?>"

        }).done(function(data) {
          console.log("<?php echo $text_success; ?>");
        });
      });
    </script>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <img src="view/image/payment/multibanco.png" alt="" /></h3>
      </div>
      <div class="panel-body">


		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-multibanco" class="form-horizontal">
			<div class="form-group required">
				<label class="col-sm-2 control-label" for="input-entidade"><span data-toggle="tooltip"><?php echo $entry_entidade; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="multibanco_entidade" value="<?php echo $multibanco_entidade; ?>" placeholder="<?php echo $entry_entidade; ?>" id="input-entidade" class="form-control" />
				</div>
			</div>
			<div class="form-group required">
				<label class="col-sm-2 control-label" for="input-subentidade"><span data-toggle="tooltip"><?php echo $entry_subentidade; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="multibanco_subentidade" value="<?php echo $multibanco_subentidade; ?>" placeholder="<?php echo $entry_subentidade; ?>" id="input-subentidade" class="form-control" />
				</div>
			</div>
      <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-valorminimo"><span data-toggle="tooltip"><?php echo $entry_valorminimo; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="multibanco_valorminimo" value="<?php echo $multibanco_valorminimo; ?>" placeholder="<?php echo $entry_valorminimo; ?>" id="input-valorminimo" class="form-control" />
				</div>
			</div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="multibanco_order_status_id" id="multibanco_order_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $multibanco_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status_complete; ?></label>
            <div class="col-sm-10">
              <select name="multibanco_order_status_complete_id" id="multibanco_order_status_complete_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $multibanco_order_status_complete_id) { ?>
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
              <select name="multibanco_geo_zone_id" id="multibanco_geo_zone_id" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $multibanco_geo_zone_id) { ?>
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
              <select name="multibanco_status" id="input-status" class="form-control">
                <?php if ($multibanco_status) { ?>
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
              <input type="text" name="multibanco_sort_order" value="<?php echo $multibanco_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_cb; ?></label>
            <div class="col-sm-10">
              <?php echo $entry_url; ?> <?php echo $multibanco_url; ?><br/><br/>
              <?php echo $entry_ap; ?> <?php echo ($multibanco_show_ap?$multibanco_ap:""); ?><br/><input type="hidden" name="multibanco_ap" value="<?php echo $multibanco_ap; ?>"  id="input-sort-order" class="form-control" />
            </div>
          </div>
        </form>


      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
