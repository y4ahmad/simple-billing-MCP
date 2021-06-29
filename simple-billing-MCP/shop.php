<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */


function exec_ogp_module()
{	
	global $db, $view;

	$settings = $db->getSettings();
		
	if (isset($_POST['save']) AND !empty($_POST['description']))
	{
		$new_description = str_replace("\\r\\n", "<br>", $_POST['description']);
		$service = $_POST['service_id'];
		
		$change_description = "UPDATE OGP_DB_PREFIXbilling_services
						       SET description ='".$db->realEscapeSingle($new_description)."'
						       WHERE service_id=".$db->realEscapeSingle($service);
		$save = $db->query($change_description);
	}
	?>
	<table class="center">
	<tr>
	<td>
	<a href="?m=simple-billing-MCP&p=cart"> <?php print_lang('your_cart');?></a>
	</td>
	</tr>
	<tr>
	<td>
	<?php 
	echo date('d-m-Y');
	?>
	</td>
	</tr>
	<tr>
	<td>
	<?php 
	echo date('H:i');
	?>
	</td>
	</tr>
	</table>
	<?php 
	// Shop Form
	if(intval($_REQUEST['service_id']) !==0) $where_service_id = " WHERE service_id=".intval($_REQUEST['service_id']); else $where_service_id = "";
	$qry_services = "SELECT * FROM OGP_DB_PREFIXbilling_services".$where_service_id;
	$services = $db->resultQuery($qry_services);
	
	if (isset($_REQUEST['service_id']) && $services === false) {
		$view->refresh('home.php?m=simple-billing-MCP&p=shop');
		return;
	}
	
	foreach ($services as $key => $row) {
		$service_id[$key] = $row['service_id'];
		$home_cfg_id[$key] = $row['home_cfg_id'];
		$mod_cfg_id[$key] = $row['mod_cfg_id'];
		$service_name[$key] = $row['service_name'];
		$remote_server_id[$key] = $row['remote_server_id'];
		$slot_max_qty[$key] = $row['slot_max_qty'];
		$slot_min_qty[$key] = $row['slot_min_qty'];
		$price_hourly[$key] = $row['price_hourly'];
		$price_weekly[$key] = $row['price_weekly'];
		$price_monthly[$key] = $row['price_monthly'];
		$price_quarterly[$key] = $row['price_quarterly'];
		$price_year[$key] = $row['price_year'];
		$RAM_Allocation[$key] = $row['RAM_Allocation'];
		$description[$key] = $row['description'];
		$img_url[$key] = $row['img_url'];
		$ftp[$key] = $row['ftp'];
		$install_method[$key] = $row['install_method'];
		$manual_url[$key] = $row['manual_url'];
		$access_rights[$key] = $row['access_rights'];
	}
	array_multisort($service_name,
					$service_id,
					$home_cfg_id,
					$mod_cfg_id,
					$remote_server_id,
					$slot_max_qty,
					$slot_min_qty,
					$price_hourly,
					$price_weekly,
					$price_monthly,
					$price_quarterly,
					$price_year,
					$RAM_Allocation,
					$description,
					$img_url,
					$ftp,
					$install_method,
					$manual_url,
					$access_rights, SORT_DESC, $services);
	?>
	<div style="border-left:10px solid transparent;">
	<?php		
	foreach( $services as $row )
	{
		if(!isset($_REQUEST['service_id']))
		{
			?>
			
			<form action="" method="POST">
				<div class="top_heading"><b><?php echo $row['service_name'];?></b></div>
				<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>" />
				<div class="midd_con">
				<input type="image" src="<?php echo $row['img_url'] ;?>" width=280 height=132 border=0 alt="Bad Image" onsubmit="submit-form();" value="More Info" />
				<center><em style="text-align:center;background-color:orange;color:blue;"><?php echo get_lang('starting_on') . " <b>" .
				floatval(round(($row['price_monthly']*$row['slot_min_qty']),2 )) . "</b>&nbsp;" . $settings['currency'] . "/" . get_lang('month') . 
				" (" . $row['slot_min_qty'] . " " . get_lang('slots') . ").";?></em>
				<button class="btn btn-sm btn-primary" onclick="submit-form();"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Order Now</button>
				</center>
			</div>
			</form>
			
			<?php 
		}		else
		{	
			?>
			<div style="float:left; border: 4px solid transparent;border-bottom: 25px solid transparent;">
			<img src="<?php echo $row['img_url'] ;?>" width=280 height=132 border=0 alt="Bad Image">
			<center><b><?php echo $row['service_name']."</b></center>";
			$isAdmin = $db->isAdmin($_SESSION['user_id'] );
			if($isAdmin)
			{
				if(!isset($_POST['edit']))
				{
					echo "<p style='color:gray;width:280px;' >$row[description]<p>";
					echo "<form action='' method='post'>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='edit' value='" . get_lang('edit') . "' />".
						 "</form>";
				}
				else
				{
					echo "<form action='' method='post'>".
						 "<textarea style='resize:none;width:280px;height:132px;' name='description' >".str_replace("<br>", "\r\n", $row['description'])."</textarea><br>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='save' value='" . get_lang('save') . "' />".
						 "</form>";
				}
			}
			else
				echo "<p style='color:gray;width:280px;' >$row[description]<p>";
			?>
			</div>
			<table style="width:420px;float:left;" class="table_bt">
			<form method="post" action="?m=simple-billing-MCP&p=add_to_cart<?php if(isset($_POST['service_id'])) echo "&service_id=".$_POST['service_id'];?>">
			<tr>
			<td align="right"><?php print_lang('service_name');?> ::</td>
			<td align="left">
			<input type="text" name="home_name" size="40" value="<?php echo $row['service_name'];?>">
			</td>
			<tr>
			<td align="right"><?php print_lang('rcon_pass');?> ::</td>
			<td align="left">
			<input type="text" name="remote_control_password" size="15" value="<?php echo genRandomString(10);?>">
			</td>
			</tr>
			<?php
			if($row['ftp'] == "enabled")
			{
			?>
				<tr>
				<td align="right"><?php print_lang('ftp_pass');?> ::</td>
				<td align="left">
				<input type="text" name="ftp_password" size="15" value="<?php echo genRandomString(10);?>">
				</td>
				</tr>
			<?php
			}
			?>
			<tr>
			  <td align="right"><?php print_lang('available_ips');?> ::</td>
			  <td align="left">
			  <select name='ip_id'>
			<?php
			$qry_ip = "SELECT ip_id,ip FROM OGP_DB_PREFIXremote_server_ips WHERE remote_server_id=".$db->realEscapeSingle($row['remote_server_id']);
			$ips = $db->resultQuery($qry_ip);

			foreach($ips as $ip)
			{
				printf("<option value='%s'>%s</option>", $ip['ip_id'], $ip['ip']);
			}?>
			  </select>
			  </td>
			</tr>
			<tr> 
			  <td align="right"><?php print_lang('max_players');?> ::</td>
			  <td  align="left">
			  <select name="max_players">
			  <?php 
			  $players=$row['slot_min_qty'];
			  while($players<=$row['slot_max_qty'])
			  {
			  echo "<option value='$players'>$players</option>";
			  $players++;
			  }
			  ?>
			  </select>
			  </td>
			</tr>
			<tr> 
			  <td align="right"><?php print_lang('invoice_duration');?> ::</td>
			  <td align="left">
			  <!--<select name="qty">
			  <?php 
			  $qty=1;
			  while($qty<=12)
			  {
			  echo "<option value='$qty'>$qty</option>";
			  $qty++;
			  }
			  ?>
			  </select>-->
			  <input type="hidden" name="qty" style="display:none" value="1">
			  <select name="invoice_duration">
			  <?php
			  if( $settings['weekly'] == 1) echo '<option value="weekly">'.get_lang('Weekly').'</option>';
			  if( $settings['monthly'] == 1) echo '<option value="monthly">'.get_lang('monthly').'</option>';
			  if( $settings['quarterly'] == 1) echo '<option value="quarterly">'.get_lang('quarterly').'</option>';
			  if( $settings['annually'] == 1) echo '<option value="annually">'.get_lang('annually').'</option>';
			  ?>
			  </select>
			  </td>
			</tr>
			<!--<tr>
				<td align="right">Billing Period ::</td>
				<td>
			  <select>
			  	<option>Weekly</option>
			  	<option>Monthly</option>
			  	<option>Quarterly ( 3 months)</option>
			  	<option>Yearly</option>
			  </select>
				</td>
			</tr>-->
			<tr>
				<td align="right">Location ::</td>
				<td>
			  <select name="location">
			  	<option value="London, UK">London, UK</option>
			  </select>
				</td>
			</tr>
			<?php if(in_array($row['mod_cfg_id'], array(1,2,3,4,5,11,12,110,111,112,113,114,131,132,133,134,135,234,235))){ ?>
			<tr>
				<td align="right">RAM Allocation ::</td>
				<td>
			  <select name="RAM_Allocation">
			  	<?php 
				  $qty=1;
				  while($qty<=10)
				  {
						if($qty==$row['RAM_Allocation']){
							$selected = "selected='selected'";
						}else{
							$selected = "";
						}
						echo "<option value='$qty' ".$selected.">$qty GB</option>";
						$qty++;
				  }
				  ?>
			  </select>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td align="right">CPU Priority ::</td>
				<td>
			  <select name="CPU_Priority">
				<?php
				  if( $settings['Normal'] == 1) echo '<option value="Normal">'.get_lang('Normal').'</option>';
				  if( $settings['High'] == 1) echo '<option value="High">'.get_lang('High').'</option>';
				  if( $settings['Real_Time'] == 1) echo '<option value="Real Time">'.get_lang('Real_Time').'</option>';
				  ?>
			  </select>
				</td>
			</tr>
			<?php
			if($settings['available_VIP_Ticket'] == 1){
			?>
			<tr>
				<td align="right">VIP Ticket ::</td>
				<td>
			  <select name="VIP_Ticket">
			  	<option value="Yes">Yes</option>
			  	<option value="No">No</option>
			  </select>
				</td>
			</tr>
			<?php } ?>
			<tr class="range_slider">
				<td align="right">Slots ::</td>
				<td>
					<input type="text" class="custom-range-slider" name="slots" value="" data-type="" data-min="0" data-max="100" data-step="10" data-prefix="" data-hide-min-max="true" data-skin="round" data-grid="true" data-grid-num="5" data-grid-snap="true" />
				</td>
			</tr>
			<tr class="bg_remove">
			  <td align="left" colspan="2">
			  	<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>"/>
				<input type="submit" name="add_to_cart" value="<?php print_lang('add_to_cart');?>"/>
			  </form>
			  </td>
			</tr>
			<tr class="bg_remove">
			<td align="left" colspan="2">
			<form action ="?m=simple-billing-MCP&p=shop" method="POST" class="back_btn">
			  <button><< <?php print_lang('back_to_list');?></button>
			</form>
			</td>
			</tr>
			
			</table>
			<?php
		}
	}
	?>
	</div>
	<?php  
}
?>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
<style>
.table_bt td {
  display: block;
  text-align: left;
  border: none !important;
}
table.table_bt tbody {
    width: 600px;
    display: table;
}
.bg_remove td:after, .back_btn:after, .range_slider td:after {
  background: 0 0 !important;
}
.table_bt td select, .table_bt td input {
    border: 1px solid#292929;
}
.bg_remove {
  display: inline-grid;
  margin-top: 25px;
}
.bg_remove td {
  width: auto;
}
table.table_bt {
  border: none;
}
.range-wrap {
  position: relative;
  margin: 0 auto 3rem;
}
.range {
  width: 100%;
}
.bubble {
  background:#00d9ff;
  color: white;
  padding: 4px 12px;
  position: absolute;
  border-radius: 4px;
  left: 50%;
  transform: translateX(-50%);
}
.bubble::after {
	content: "";
	position: absolute;
	top: -10px;
	left: 0;
	border-bottom: 10px solid #00d9ff;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	right: 0;
	width: 10px;
	margin: auto;
}
input[type="range"]:focus {
  outline: none;
  box-shadow: none;
}
input[type="range"] {
  padding: 0;
}

.irs--round .irs-handle{
	border: 4px solid #00d9ff;
}
.irs--round .irs-bar, .irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single{
	background-color: #00d9ff;
}
.irs--round .irs-from:before, .irs--round .irs-to:before, .irs--round .irs-single:before{
	border-top-color: #00d9ff;
}
.irs--round .irs-handle {
    top: 55px;
    border: 4px solid #00d9ff;
    border-radius: 4px;
    background: #00d9ff;
    width: 20px;
    height: 20px;
}
.irs--round .irs-handle.state_hover, .irs--round .irs-handle:hover {
    background-color: #00d9ff;
}
.irs--round .irs-handle:before {
    position: absolute;
    display: block;
    content: "";
    top: -21px;
    left: 0;
    right: 0;
    width: 0;
    margin: auto;
    height: 0;
    margin-left: -3px;
    overflow: hidden;
    border: 9px solid transparent;
    border-bottom-color: #00d9ff;
}
.irs-grid-text {
    bottom: -35px;
 }	
	
</style>

