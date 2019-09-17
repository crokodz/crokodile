<h3 class="wintitle">Lates Summary</h3>

<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />

<form method="post">

<input type="hidden" name="id">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td align="left" width=100px>From</td>
		<td align="left" width=50px><input type="text" maxlength="10" name="fdate" id="fdate" value="<?php echo $_POST['fdate']; ?>" style="width:80px;"></td>
		<td align="left" rowspan=6>
			<div id="cont"></div>
			<div id="info" style="text-align: center; margin-top: 1em;font-weight:bold;">Click to select start date</div>
		</td>
		<td align="left" width=150px rowspan=6>&nbsp;</td>
	</tr>
	<tr>
		<td align="left">To</td>
		<td align="left"><input type="text" maxlength="10" name="tdate" id="tdate" value="<?php echo $_POST['tdate']; ?>" style="width:80px;"></td>
	</tr>
	<tr>
		<td align="left">Pay Code</td>
		<td align="left">
			<select style="width:200px;height:100px;" name="idx[]" id="idx" multiple>
			<?php
			if($_SESSION['user'] == 'mso'){
				$select = "select `name` from `pay` where `group` = 'mso' group by `name`";
				}
			else{
				$select = "select `name` from `pay` where name like '" . $_SESSION['pay_id'] . "%' group by `name`";
				}
			$result_data = mysql_query($select, connect());
			if($_SESSION['user'] == 'mso'){
				?>
				<option value="ALL MSO" selected>ALL MSO</option>
				<?php
				}
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['name']; ?>" <?php if ($_SESSION['dep'] == $data['name']){ echo 'selected'; } ?> selected><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=2 align="left"><input type="submit" name="view" id="view" value="Generate"></td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
</table>
<br>
<br>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=300px align="center">Name</td>
		<td width=150px align="center">Division</td>
		<td width=100px align="center">Days of lates</td>
		<td width=100px align="center">Min. of Lates</td>
		<td width=100px align="center">Min. of Under Time</td>
		<td align="center">&nbsp;</td>
	</tr>
	
	<?php
	$id=$_POST['idx'];
	if ($id){
		foreach ($id as $paycode){
			$select = "select fname,lname,mname, sum(late) as lt, sum(ut) as ut, count(trxnname) as days, division,`em_id` from transaction left join employee using (`em_id`) where trxn_date between '" . $_POST['fdate'] . "' and '" . $_POST['tdate'] . "' and (late > 0 or ut > 0) and employee.status = 'active' and transaction.pay_id = '" . $paycode . "' group by em_id order by lname asc, fname asc, mname asc" ;
			$result = mysql_query($select, connect());
			$ww = 0;
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$name = $row['lname'] . ", " . $row['fname'] . " " . $row['mname'];
			if($ww==0){
			?>
			<tr>
				<td colspan=5>&nbsp;</td>
			</tr>	
			<tr>
				<td colspan=5><b><?php echo $paycode; ?></b></td>
			</tr>	
			<?php
			}
			?>
			<tr  style="cursor:pointer;" id="emlist" onclick="self.location='index.php?menu=50det&fdate=<?php echo $_POST['fdate']; ?>&tdate=<?php echo $_POST['tdate']; ?>&em_id=<?php echo $row['em_id']; ?>'">
				<td><?php echo $name; ?></td>
				<td><?php echo $row['division']; ?></td>
				<td><?php echo $row['days']; ?></td>
				<td><?php echo $row['lt']; ?></td>
				<td><?php echo $row['ut']; ?></td>
				<td>&nbsp;</td>
			</tr>	
			<?php
			$ww++;
			}
			}
		}
	?>
</table>
</form>
<script type="text/javascript">
var SELECTED_RANGE = null;
function getSelectionHandler() {
	var startDate = null;
	var ignoreEvent = false;
	return function(cal) {
		var selectionObject = cal.selection;
		if (ignoreEvent)
			return;
			var selectedDate = selectionObject.get();
		if (startDate == null) {
			startDate = selectedDate;
			SELECTED_RANGE = null;
			document.getElementById("info").innerHTML = "Click to select end date";
			document.getElementById("fdate").value = "";
			document.getElementById("tdate").value = "";
			document.getElementById("view").disabled = true;
			//~ cal.args.min = Calendar.intToDate(selectedDate);
			//~ cal.refresh();
			} 
		else {
			ignoreEvent = true;
			selectionObject.selectRange(startDate, selectedDate);
			ignoreEvent = false;
			SELECTED_RANGE = selectionObject.sel[0];
			startDate = null;
			ranger = selectionObject.print("%Y-%m-%d") + "";
			rangerz = ranger.split(" -> ");
			document.getElementById("info").innerHTML = ranger;
			document.getElementById("fdate").value = rangerz[0];
			document.getElementById("tdate").value =  rangerz[1];
			document.getElementById("view").disabled = false;
			cal.args.min = null;
			cal.refresh();
			}
		};
	};

Calendar.setup({
	cont          : "cont",
	fdow          : 1,
	selectionType : Calendar.SEL_SINGLE,
	onSelect      : getSelectionHandler()
	});

</script>