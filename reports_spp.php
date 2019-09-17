
<?php
$result = mysql_query("select *from users;", connect()); 

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
?>
<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />

<h3 class="wintitle"><b>PhilHealth</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" class="repbut" value="Monthly Remittance Report"  Onclick="OpenGetID(this,'phq',33,0);">
		</td>
	</tr>
</table>

<br>
<br>
<h3 class="wintitle"><b>Pag-Ibig</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" class="repbut" value="Monthly Contributions" id="pimc"  Onclick="OpenGetID(this,'piq',33,0);">
			<input type="button" class="repbut" value="Monthly Remittance Schedule"  id="pims" Onclick="OpenGetID(this,'pis',33, -250);">
		</td>
	</tr>
</table>


<br>
<br>
<h3 class="wintitle"><b>Social Security System</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" class="repbut" value="Monthly Contributions" id="sssm"  Onclick="OpenGetID(this,'sssm',33,0);">
			<input type="button" class="repbut" value="Monthly Loan Remittance" id="sssl"  Onclick="OpenGetID(this,'sssl',33,-300);">
			<input type="button" class="repbut" value="R1" id="sssn"  Onclick="OpenCalz(this,'sssn',-337,0);">
		</td>
	</tr>
</table>

<div id="getid" class="getid">
	<div class="rephead"><img src="close_icon.gif" class="cls" onclick="CloseGetID();"></img></div>
	<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
		<?php
		$x=0;
		$select = "select `title`, `posted_id`, `from`, `to`, `company_id` from `posted_summary` where `company_id` != 0 group by `posted_id` order by `posted_id` asc";
		$result = mysql_query($select, connect());
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$cinfo = getcompany($row['company_id']);
		?>
		<tr class="listid" id="tr<?php echo $x; ?>">
			<td onclick="clickCH('<?php echo $x; ?>');"><?php echo $row['title']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="100px"><?php echo $cinfo['name']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="60px"><?php echo $row['posted_id']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="170px"><?php echo $row['from'] . " - " . $row['to']; ?></td>
			<td width="10px"><input type="checkbox" name="ch<?php echo $x; ?>" id="ch<?php echo $x; ?>" value="<?php echo $row['posted_id']; ?>"></td>
		</tr>
		<input type="hidden" name="compa<?php echo $x; ?>" id="compa<?php echo $x; ?>" value="<?php echo $row['company_id']; ?>">
		<?php
		$x++;
		}
		?>
	</table>
	<div id="extra"></div>
	<br>
	<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
	<input type="hidden" name="type" id="type" value="">
	
	<input type="button" value="Generate" onclick="GenReport();">
</div>

<div id="calz" class="calz">
	<div>
	<select style="width:200px" name="compa" id="compa">
	<?php
	$result_data = $result_data = get_mycompany();
	while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
	?>
	<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
	<?php
	}
	?>
	</select>
	</div>
	<br>
	<div id="cont"></div>
	<div id="info" style="text-align: center; margin-top: 1em;font-weight:bold;">Click to select start date</div>
	<br>
	ID # <input type="text" name="em_id_calz" id="em_id_calz"><br><br>
	<input type="button" value="Generate" onclick="GenReportCalz();"> | 
	<input type="button" value="Generate All" onclick="GenReportCalzAll();">
	<input type="hidden" name="fdate" id="fdate">
	<input type="hidden" name="tdate" id="tdate">
</div>
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
			var payday = document.getElementById("svar");
			var smm = document.getElementById("smm");
			var syy = document.getElementById("syy");
			var svar = document.getElementById("svar");
			
			fdt = (rangerz[0]).split("-");
			tdt = (rangerz[1]).split("-");
			if(fdt[2] == '20' && tdt[2] == '04'){
				setSelectedIndex(smm,tdt[1]);
				setSelectedIndex(svar,'w1@');
				}
			if(fdt[2] == '05' && tdt[2] == '19'){
				setSelectedIndex(smm,tdt[1]);
				setSelectedIndex(svar,'w2@');
				}
			
			cal.args.min = null;
			cal.refresh();
			}
		};
	};
	
function setSelectedIndex(s, v) {
	for ( var i = 0; i < s.options.length; i++ ) {
		if ( s.options[i].value == v ) {
			s.options[i].selected = true;
			return;
			}
		}
	}

Calendar.setup({
	cont          : "cont",
	fdow          : 1,
	selectionType : Calendar.SEL_SINGLE,
	onSelect      : getSelectionHandler()
	});

</script>