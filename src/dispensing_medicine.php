<?
require('db_connect.php');

function procs($db_object,$queue){
	$delete = "DELETE FROM h_procs WHERE `SYSTEM QUEUE NUMBER` = '" . $queue . "' AND `SYSTEM ITEM CODE` = '3100000'";
	$db_object->query($delete);
	$insert = "INSERT INTO h_procs (`SYSTEM QUEUE NUMBER`, `SYSTEM ITEM CODE`) VALUES ('" . $queue . "','3100000') ";
	$db_object->query($insert);
	$update_payq=("UPDATE  h_queue 
		SET `SYSTEM QUEUE STATUS`='250' 
		WHERE `SYSTEM QUEUE NUMBER` = '".$queue."'");
	$db_object->query($update_payq);
	}

function get_available($db_object,$code,$bcode){
	$select = "SELECT SUM(`REMAINING QUANTITY`) AS TQUANTITY
	FROM h_receive 
	WHERE CODE = '" . $code . "' 
	AND BRANCH = '" . $bcode . "'
	GROUP BY CODE, BRANCH";
	$query=$db_object->query($select);
	$count = $query->NumRows();
	$data = $query->fetchRow();
	if ($count==0){
		$insert = "INSERT INTO h_receive (
		ID,
		RCV_DATE,
		BRANCH,
		DISTRIBUTOR,
		QUANTITY,
		`BUNDLE QTY`,
		`TOTAL QUANTITY`,
		`REMAINING QUANTITY`,
		CODE,
		USERNAME,
		EXP_DATE,
		COST,
		`LOT NO`,
		`REMARKS`,
		`DATE OF ORDER`,
		`INVOICE`,
		`STATUS`
		) VALUES (
		'NULL',
		CURDATE(),
		'" . $bcode . "',
		'0',
		'0',
		'0',
		'0',
		'0',
		'" . $code . "',
		'" . $_SESSION['username'] . "',
		'" . $expiry_date . "',
		'0',
		'0',
		'0',
		CURDATE(),
		CURDATE(),
		'INITIAL'
		)";
		$db_object->query($insert);
		return 0;
		}
	else{
		return $data['TQUANTITY'];
		}
	}
	
function deductquantity($db_object,$code,$bcode,$quantity){
	$available = get_available($db_object,$code,$bcode);
	if ($available >= $quantity){
		$select = "SELECT * FROM h_receive 
			WHERE CODE = '" . $code . "' 
			AND BRANCH = '" . $bcode . "'
			AND `REMAINING QUANTITY` > '0'
			ORDER BY `ID` ASC";
		$queryq=$db_object->query($select);
		$a = $quantity;
		$cost = 0;
		$x = 0;
		$data = 0;
		$id = "";
		while($data = $queryq->fetchRow()){
			if ($a > 0){
				if ($a >= $data['REMAINING QUANTITY']){
					$update = "UPDATE h_receive set `REMAINING QUANTITY` = '0'
						WHERE ID = '" . $data['ID'] . "'";
					$db_object->query($update);
					$a = $a - $data['REMAINING QUANTITY'];
					$cost = $cost + ($data['COST'] * $data['REMAINING QUANTITY']);
					$id = $id . $data['ID']. "??" . $data['REMAINING QUANTITY'] . "||";
					}
				else{
					$q = $data['REMAINING QUANTITY'] - $a;
					$update = "UPDATE h_receive set `REMAINING QUANTITY` = '" . $q . "'
						WHERE ID = '" . $data['ID'] . "'";
					
					$db_object->query($update);
					$cost = $cost + ($data['COST'] * $a);
					$id = $id . $data['ID']. "??" . $a . "||";
					$a = 0;
					}
				$x++;
				}
			}
		return $cost . "@@0@@" . $id;
		}
	else{
		$available = $available - $quantity;
		$select = "SELECT * FROM h_receive 
			WHERE CODE = '" . $code . "' 
			AND BRANCH = '" . $bcode . "'
			ORDER BY `RCV_TIME` DESC LIMIT 1";
		$queryq=$db_object->query($select);
		$data = $queryq->fetchRow();
		
		$update = "UPDATE h_receive set `REMAINING QUANTITY` = '0'
			WHERE CODE = '" . $code . "' 
			AND BRANCH = '" . $bcode . "'";
		$db_object->query($update);
		
		$update = "UPDATE h_receive set `REMAINING QUANTITY` = '" . $available . "'
			WHERE ID = '" . $data['ID'] . "'";
		$db_object->query($update);
		
		return $available . "@@1@@" . $data['ID'] . "??" . $quantity;
		}
	}

function getDrugName($db_object,$code){
	$select = "SELECT `BRAND NAME` FROM h_lmeds WHERE `MEDICINE CODE` = '" . $code . "'";
	$query=$db_object->query($select);
	$row=$query->fetchRow();
	return $row['BRAND NAME'];
	}

function refresh(){
	echo '<script>';
	echo 'var url = window.location;';
	echo 'window.location=url;';
	echo '</script>';
	}
	
function roundoff($amount){
	if ($amount != 0){
		return number_format($amount, 2, '.', '');
		}
	else{
		return 0;
		}
	}
	
function putback($db_object,$id,$bcode){
	$select = "SELECT * FROM h_meds WHERE `ID` = '" . $id . "'";
	$query=$db_object->query($select);
        $row=$query->fetchRow();
	
	$code = $row['MEDICINE CODE'];
	$quantity = $row['CHQNTY'];
	$ids = explode("||", $row['IDS']);
	for($x=0;$x<count($ids);$x++){
		if ($ids[$x]){
			$a = explode("??", $ids[$x]);
			$select = "SELECT * FROM h_receive 
				WHERE `ID` = '" . $a[0] . "' 
				ORDER BY `ID` DESC LIMIT 1";
			$queryq=$db_object->query($select);
			$data = $queryq->fetchRow();
			
			$available = $data['REMAINING QUANTITY'] + $a[1];
			
			$update = "UPDATE h_receive set `REMAINING QUANTITY` = '" . $available . "'
				WHERE ID = '" . $data['ID'] . "'";
			$db_object->query($update);
			}
		}
	}
	
function getauth($db_object,$user){
	$select = "SELECT * FROM dispensing_auth
	WHERE `AUTHORIZED` = '" . $user . "'
	AND `FROM` <= '" .date('Y-m-d'). "'
	AND `TO` >= '" .date('Y-m-d'). "'";
	$query=$db_object->query($select);
        $count=$query->NumRows();
	return $count;
	}


if (isset($_POST['receive'])){
	$select = "SELECT h_procs.*
		FROM h_queue 
		JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
		WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
		AND h_procs.`SYSTEM ITEM CODE` = '3100000'";
	$data = $db_object->query($select);
	$nums = $data->numRows();
	if($nums ==0){
		echo "<script>alert('Alert!!!  Drug is empty..');</script>";
		}
	else{
		$select1 = "SELECT h_procs.*
			FROM h_queue 
			JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
			WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
			AND h_procs.`SYSTEM ITEM CODE` = '3100000'
			AND h_procs.`DATE SPECIMEN/ANCILLARY` = '0000-00-00'";
		$data1 = $db_object->query($select1);
		$nums1 = $data1->numRows();
		if($nums1 ==0){
			echo "<script>alert('Drug is already receive...');</script>";
			refresh();
			}
		else{
			$update = "UPDATE h_procs SET 
				`DATE SPECIMEN/ANCILLARY` = '" .date('Y-m-d'). "',
				`CODE RECEIVED BY` = '" .$_SESSION['nurse']. "'
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'
				AND `SYSTEM ITEM CODE` = '3100000'";
			$db_object->query($update);
			
			$update_payq=("UPDATE  h_queue 
				SET `SYSTEM QUEUE STATUS`='250' 
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
			$db_object->query($update_payq);
			
			$select = "select * from h_meds where `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' and `STATUS` = 'PRESCRIBE'";
			$query=$db_object->query($select);
			while ($row=$query->fetchRow()){
				$cost = deductquantity($db_object,$row['MEDICINE CODE'],$_SESSION['branch_code'],$row['CHQNTY']);
				$c = explode("@@", $cost);
				if ($c[1] == "1"){
					$cost = "0";
					$bc = $c[0];
					}
				else{
					$cost = $c[0];
					$bc = "0";
					}
				
				$update = "update h_meds set 
					`IDS` = '" . $c[2] . "', 
					`TOTAL COST` = '" . $cost * $row['CHQNTY'] . "', 
					`BALANCE COST` = '" . $bc . "', 
					`STATUS` = 'DISPENCED'
					where `ID` = '" . $row['ID'] . "'";
				$db_object->query($update);
				}
			}		
		}
	$update_payq=("UPDATE  h_queue 
		SET `PAYMENT STATUS`='1' 
		WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
	$db_object->query($update_payq);
	
	$update = "UPDATE h_procs SET `DISCOUNT` = '" . $_POST['discount'] . "' WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND `SYSTEM ITEM CODE` = '3100000'";
	$db_object->query($update);
	refresh();
	}

if (isset($_POST['receivebilled'])){
	$select = "SELECT h_procs.*
		FROM h_queue 
		JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
		WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
		AND h_procs.`SYSTEM ITEM CODE` = '3100000'";
	$data = $db_object->query($select);
	$nums = $data->numRows();
	if($nums ==0){
		echo "<script>alert('No drug to be dispenced...');</script>";
		}
	else{
		$select1 = "SELECT h_procs.*
			FROM h_queue 
			JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
			WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
			AND h_procs.`SYSTEM ITEM CODE` = '3100000'
			AND h_procs.`DATE SPECIMEN/ANCILLARY` = '0000-00-00'";
		$data1 = $db_object->query($select1);
		$nums1 = $data1->numRows();
		if($nums1 ==0){
			echo "<script>alert('Drug is already receive...');</script>";
			refresh();
			}
		else{
			$update = "UPDATE h_procs SET 
				`DATE SPECIMEN/ANCILLARY` = '" .date('Y-m-d'). "',
				`CODE RECEIVED BY` = '" .$_SESSION['nurse']. "'
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'
				AND `SYSTEM ITEM CODE` = '3100000'";
			$db_object->query($update);
			
			$update_payq=("UPDATE  h_queue 
				SET `SYSTEM QUEUE STATUS`='300' 
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
			$db_object->query($update_payq);
			
			$select = "select * from h_meds where `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' and `STATUS` = 'PRESCRIBE'";
			$query=$db_object->query($select);
			while ($row=$query->fetchRow()){
				$cost = deductquantity($db_object,$row['MEDICINE CODE'],$_SESSION['branch_code'],$row['CHQNTY']);
				$c = explode("@@", $cost);
				if ($c[1] == "1"){
					$cost = "0";
					$bc = $c[0];
					}
				else{
					$cost = $c[0];
					$bc = "0";
					}
				
				$update = "update h_meds set 
					`IDS` = '" . $c[2] . "', 
					`TOTAL COST` = '" . $cost * $row['CHQNTY'] . "', 
					`BALANCE COST` = '" . $bc . "', 
					`STATUS` = 'DISPENCED'
					where `ID` = '" . $row['ID'] . "'";
				$db_object->query($update);
				}
			}		
		}
	$update_payq=("UPDATE  h_queue 
		SET `PAYMENT STATUS`='1' 
		WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
	$db_object->query($update_payq);
	
	$update = "UPDATE h_procs SET `DISCOUNT` = '" . $_POST['discount'] . "' WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND `SYSTEM ITEM CODE` = '3100000'";
	$db_object->query($update);
	refresh();
	}

if (isset($_POST['new'])){
	refresh();
	}

if (isset($_POST['save'])){
	$select = "select * from h_meds where 
		`MEDICINE CODE` = '" . $_POST['drug_code'] . "' and 
		`SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' and
		(`STATUS` = 'DISPENCED' or `STATUS` = 'PRESCRIBE')
		";
	$query=$db_object->query($select);
	$count=$query->numRows();

	if ($_POST['name'] == "" || $_POST['quantity'] == "" || $_POST['dosage'] == "" || $_POST['frequency'] == ""){
		echo '<center><BLINK><b><font color="red">Some Fields must have a value</font></b></BLINK><center>';
		}
	else{
		if($_POST['id']>0){
			$delete = "DELETE from h_meds where `ID` = '" . $_POST['id'] . "'";
			$db_object->query($delete);
			//~ $update = "UPDATE h_meds SET STATUS = 'DELETED', `OUTPUT` = '" . $_SESSION['userid'] . "' WHERE `ID` = '" . $_POST['id'] . "'";
			//~ $query=$db_object->query($update);
			}
		
		$cost = 0;
		$bc = 0;
		$c[2] = "";

		if($_POST['deduct_1'] == "Y"){
			$quantity = 1;
			}
		else{
			$quantity = $_POST['dosage'] * $_POST['frequency'] * $_POST['day']; 
			}
			
		if($_POST['username']){
			$user = $_POST['username'];
			}
		else{
			$user = $_SESSION['username'];
			}

		$insert = "INSERT INTO `h_meds` (
			`SYSTEM QUEUE NUMBER` ,
			`MEDICINE CODE` ,
			`QUANTITY` ,
			`QUANTITY_BEFORE` ,
			`AMOUNT` ,
			`REMARKS` ,
			`UNIT PRICE` ,
			`DATE RELEASED` ,
			`DOSAGE` ,
			`FREQUENCY` ,
			`DAY` ,
			`TOTAL PRICE` ,
			`INTAKE` ,
			`CAUTION`,
			`STATUS`,
			`INPUT`,
			`TOTAL COST`,
			`DOSAGE CODE`,
			`FREQUENCY CODE`,
			`DAY CODE`,
			`USERNAME`,
			`CHQNTY`,
			`CHDAY`,
			`BALANCE COST`,
			`REASON`,
			`IDS`,
			`CATEGORY4`
			)
			VALUES (
			'" . $_GET['qnumber'] . "', 
			'" . $_POST['drug_code'] . "', 
			'" . $quantity . "', 
			'" . $_POST['available'] . "', 
			'" . $_POST['price_code'] * $_POST['quantity'] . "', 
			'" . $_POST['remarks'] . "', 
			'" . $_POST['price_code'] . "', 
			CURDATE(), 
			'" . $_POST['dosage'] . "', 
			'" . $_POST['frequency'] . "', 
			'" . $_POST['day'] . "', 
			'" . $_POST['price'] . "', 
			'" . $_POST['intake'] . "', 
			'" . $_POST['caution'] . "',
			'PRESCRIBE',
			'" . $_SESSION['userid'] . "',
			'" . $cost . "',
			'" . $_POST['dosage_code'] . "',
			'" . $_POST['frequency_code'] . "',
			'" . $_POST['day_code'] . "',
			'" . $user . "',
			'" . $_POST['quantity'] . "', 
			'" . $_POST['day'] . "',
			'" . $bc . "',
			'" . $_POST['reason1'] . "',
			'" . $c[2] . "',
			'" . $_POST['deduct_1'] . "'
			)";
		$db_object->query($insert);
		if($_POST['intake']){
			$insert = "insert into history values (
			NULL, 
			'h_intake',
			CURDATE(),
			CURTIME(),
			'" . $_GET['qnumber'] . "',
			'" . $_POST['drug_code'] . "',
			'" . $_POST['intake'] . "', 
			'" . $_SESSION['username'] . "',
			'0',
			'0'
			)";
			$db_object->query($insert);
			}
		if($_POST['caution']){
			$insert = "insert into history values (
			NULL, 
			'h_caution',
			CURDATE(),
			CURTIME(),
			'" . $_GET['qnumber'] . "',
			'" . $_POST['drug_code'] . "',
			'" . $_POST['caution'] . "', 
			'" . $_SESSION['username'] . "',
			'0',
			'0'
			)";
			$db_object->query($insert);
			}		
		
		$insert = "insert into history values (
			NULL, 
			'h_quantity',
			CURDATE(),
			CURTIME(),
			'" . $_GET['qnumber'] . "',
			'" . $_POST['drug_code'] . "',
			'" . $_POST['reason1'] . "',
			'" . $_SESSION['username'] . "',
			'" . $_POST['quantity'] . "', 
			'" . $_POST['day'] . "'
			)";
		$db_object->query($insert);
		
		procs($db_object,$_GET['qnumber']);
		refresh();
		}
	}
	
if (isset($_POST['delete'])){	
	$update = "UPDATE h_meds SET STATUS = 'DELETED', `OUTPUT` = '" . $_SESSION['userid'] . "' WHERE `ID` = '" . $_POST['id'] . "'";
	$db_object->query($update);
	refresh();
	}
	
if (isset($_POST['savemed'])){	
	$update = "UPDATE h_procs SET `DISCOUNT` = '" . $_POST['discount'] . "' WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND `SYSTEM ITEM CODE` = '3100000'";
	$db_object->query($update);
	refresh();
	}
	
if (isset($_POST['billmed'])){	
	$select = "SELECT h_procs.*
		FROM h_queue 
		JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
		WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
		AND h_procs.`SYSTEM ITEM CODE` = '3100000'";
	$data = $db_object->query($select);
	$nums = $data->numRows();
	if($nums ==0){
		echo "<script>alert('No drug to be dispenced...');</script>";
		}
	else{
		$select1 = "SELECT h_procs.*
			FROM h_queue 
			JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
			WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
			AND h_procs.`SYSTEM ITEM CODE` = '3100000'
			AND h_procs.`DATE SPECIMEN/ANCILLARY` = '0000-00-00'";
		$data1 = $db_object->query($select1);
		$nums1 = $data1->numRows();
		if($nums1 !=0){
			$update = "UPDATE h_procs SET 
				`DATE SPECIMEN/ANCILLARY` = '" .date('Y-m-d'). "',
				`CODE RECEIVED BY` = '" .$_SESSION['nurse']. "'
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'
				AND `SYSTEM ITEM CODE` = '3100000'";
			$db_object->query($update);
			
			$update_payq=("UPDATE  h_queue 
				SET `SYSTEM QUEUE STATUS`='300' 
				WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
			$db_object->query($update_payq);
			
			$select = "select * from h_meds where `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' and `STATUS` = 'PRESCRIBE'";
			$query=$db_object->query($select);
			while ($row=$query->fetchRow()){
				$cost = deductquantity($db_object,$row['MEDICINE CODE'],$_SESSION['branch_code'],$row['CHQNTY']);
				$c = explode("@@", $cost);
				if ($c[1] == "1"){
					$cost = "0";
					$bc = $c[0];
					}
				else{
					$cost = $c[0];
					$bc = "0";
					}
				
				$update = "update h_meds set 
					`IDS` = '" . $c[2] . "', 
					`TOTAL COST` = '" . $cost * $row['CHQNTY'] . "', 
					`BALANCE COST` = '" . $bc . "', 
					`STATUS` = 'DISPENCED'
					where `ID` = '" . $row['ID'] . "'";
				$db_object->query($update);
				}
			}		
		}
	$update_payq=("UPDATE  h_queue 
		SET `PAYMENT STATUS`='1' , `SYSTEM QUEUE STATUS`='300' 
		WHERE `SYSTEM QUEUE NUMBER` = '".$_GET['qnumber']."'");
	$db_object->query($update_payq);
	
	$update = "UPDATE h_procs SET `DISCOUNT` = '" . $_POST['discount'] . "' WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND `SYSTEM ITEM CODE` = '3100000'";
	$db_object->query($update);
	refresh();
	}
	
if (isset($_POST['finalchange'])){
	$update = "update h_meds set `REASON` = '" . $_POST['reason'] . "', `IDS` = '" . $c[2] . "', `CHQNTY` = '" . $_POST['chqnty'] . "', `CHDAY` = '" . $_POST['chday'] . "', `TOTAL PRICE` = '" . $_POST['chprice'] . "' WHERE `ID` = '" . $_POST['id'] . "' ";
	$db_object->query($update);
	
	$select = "select * from h_meds WHERE `ID` = '" . $_POST['id'] . "' ";
	$query=$db_object->query($select);
	$row=$query->fetchRow();
	
	$insert = "insert into history values (
		NULL, 
		'h_quantity',
		CURDATE(),
		CURTIME(),
		'" . $_GET['qnumber'] . "',
		'" . $row['MEDICINE CODE']. "',
		'" . $_POST['reason'] . "', 
		'" . $_SESSION['username'] . "',
		'" . $_POST['chday'] . "', 
		'" . $_POST['chqnty'] . "'
		)";
	$db_object->query($insert);
	
	refresh();
	}
	
if (isset($_POST['finalchange1'])){
	$update = "update h_meds set `REASON` = '" . $_POST['reason'] . "', `IDS` = '" . $c[2] . "', `CHQNTY` = '" . $_POST['chqnty'] . "', `CHDAY` = '" . $_POST['chday'] . "', `TOTAL PRICE` = '" . $_POST['chprice'] . "' WHERE `ID` = '" . $_POST['id'] . "' ";
	$db_object->query($update);
	
	$select = "select * from h_meds WHERE `ID` = '" . $_POST['id'] . "' ";
	$query=$db_object->query($select);
	$row=$query->fetchRow();
	
	$insert = "insert into history values (
		NULL, 
		'h_quantity',
		CURDATE(),
		CURTIME(),
		'" . $_GET['qnumber'] . "',
		'" . $row['MEDICINE CODE']. "',
		'" . $_POST['reason'] . "', 
		'" . $_SESSION['username'] . "',
		'" . $_POST['chday'] . "', 
		'" . $_POST['chqnty'] . "'
		)";
	$db_object->query($insert);
	
	refresh();
	}
	
if (isset($_POST['cancel'])){
	putback($db_object,$_POST['id'],$_SESSION['branch_code']);
	procs($db_object,$_GET['qnumber']);
	$update = "UPDATE h_meds SET STATUS = 'DELETED', `OUTPUT` = '" . $_SESSION['userid'] . "' WHERE `ID` = '" . $_POST['id'] . "'";
	$query=$db_object->query($update);
	refresh();
	}
	
if (isset($_POST['duplicate'])){
	$select = "SELECT * FROM h_meds WHERE `SYSTEM QUEUE NUMBER` = '" . $_POST['idqueue'] . "' and `STATUS` != 'DELETED'";
	$query=$db_object->query($select);
	while ($row=$query->fetchRow()){
		$cost = 0;
		$bc = 0;
		$c[2] = "";
	
		$insert = "INSERT INTO `h_meds` (
			`SYSTEM QUEUE NUMBER` ,
			`MEDICINE CODE` ,
			`QUANTITY` ,
			`QUANTITY_BEFORE` ,
			`AMOUNT` ,
			`REMARKS` ,
			`UNIT PRICE` ,
			`DATE RELEASED` ,
			`DOSAGE` ,
			`FREQUENCY` ,
			`DAY` ,
			`TOTAL PRICE` ,
			`INTAKE` ,
			`CAUTION`,
			`STATUS`,
			`INPUT`,
			`TOTAL COST`,
			`DOSAGE CODE`,
			`FREQUENCY CODE`,
			`DAY CODE`,
			`USERNAME`,
			`CHQNTY`,
			`CHDAY`,
			`BALANCE COST`,
			`REASON`,
			`IDS`,
			`CATEGORY4`
			)
			VALUES (
			'" . $_GET['qnumber'] . "', 
			'" . $row['MEDICINE CODE'] . "', 
			'" . $row['QUANTITY'] . "', 
			'" . $row['QUANTITY_BEFORE'] . "', 
			'" . $row['AMOUNT'] . "', 
			'" . $_POST['remarks'] . "', 
			'" . $row['UNIT PRICE'] . "', 
			CURDATE(), 
			'" . $row['DOSAGE'] . "', 
			'" . $row['FREQUENCY'] . "', 
			'" . $row['DAY'] . "', 
			'" . $row['TOTAL PRICE'] . "', 
			'" . $row['INTAKE'] . "', 
			'" . $row['CAUTION'] . "',
			'PRESCRIBE',
			'" . $_SESSION['userid'] . "',
			'" . $cost . "',
			'" . $row['DOSAGE CODE'] . "',
			'" . $row['FREQUENCY CODE'] . "',
			'" . $row['DAY CODE'] . "',
			'" . $_SESSION['username'] . "',
			'" . $row['QUANTITY'] . "', 
			'" . $row['DAY'] . "',
			'" . $bc . "',
			'',
			'" . $c[2] . "',
			'" . $row['CATEGORY4'] . "'
			)";
		$db_object->query($insert);
		procs($db_object,$_GET['qnumber']);
		refresh();
		}
	}
	
#CAUTION
$select = "SELECT `ENGLISH` FROM h_caution
	ORDER BY `ENGLISH`";
$query=$db_object->query($select);
$count = $query->numRows();
$x = 0;
while($med = $query->fetchRow()){
	if($x != $count-1){
		$caution = $caution . '"' . $med['ENGLISH'] . '",';
		}
	else{
		$caution = $caution . '"' . $med['ENGLISH'] . '"';
		}
	$x++;
	}	
	
#INTAKE
$select = "SELECT `ENGLISH` FROM h_intake
	ORDER BY `ENGLISH`";
$query=$db_object->query($select);
$count = $query->numRows();
$x = 0;
while($med = $query->fetchRow()){
	if($x != $count-1){
		$intake = $intake . '"' . $med['ENGLISH'] . '",';
		}
	else{
		$intake = $intake . '"' . $med['ENGLISH'] . '"';
		}
	$x++;
	}
	
#INFO
$select = "SELECT h_lpatie.* FROM `h_queue` JOIN `h_lpatie` USING(`SYSTEM PATIENT ID#`) WHERE `SYSTEM QUEUE NUMBER`='" . $_GET['qnumber'] . "'";
$query=$db_object->query($select);
$row=$query->fetchRow();
$name = $row['FULL NAME'] . " " . $row['CHINESE NAME'];
$id = $row['SYSTEM PATIENT ID#'];

$bdate = $row['DATE OF BIRTH'];
$m=substr($bdate,5,-3);
$d=substr($bdate,8);
$y=substr($bdate,0,-6);

$birth=$y.$m.$d;
$todate=date(Ymd);
$dte=floor($todate);
$births=floor($birth);
$aage=($dte-$births);
$age=substr($aage,0,-4);
$gender = $row['GENDER'];
$auth = getauth($db_object,$_SESSION['username']);

$select1 = "SELECT h_procs.*
		FROM h_queue 
		JOIN h_procs USING(`SYSTEM QUEUE NUMBER`)
		WHERE h_queue.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
		AND h_procs.`SYSTEM ITEM CODE` = '3100000'
		AND h_procs.`DATE SPECIMEN/ANCILLARY` = '0000-00-00'";
$data1 = $db_object->query($select1);
$nums1 = $data1->numRows();
if($nums1 ==0){
	$dc = "Completed";
	$disabledc = 'Disabled';
	}
else{
	$dc = "Dispensing Completed";
	$disabledc = '';
	}
?>
<style>
	body {font-family: verdana; arial, sans-serif; font-size: 12px; }
	#search, ul { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:scroll;height:450px;}
	ul { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	li { margin: 0 0 0px 0; cursor: default; color: red;}
	li:hover { background: #ffc; }
	li.selected { background: #FCC;}
</style>
<script type="text/javascript" src="md5.js"></script>
<link href="images_files/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="main.js" type="text/javascript"></script>
<script type="text/javascript" src="lib/prototype.js"></script>
<script type="text/javascript" src="src/scriptaculous.js"></script>
<body onKeyup="OnEvent(5,event);">
<form name="pharma" method="POST">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr bgcolor="#999999">
<td colspan="3"><strong>Medication</strong></td>
</tr>
<tr>
	<td width=24%><b>Queue# :</b> <? echo $_GET['qnumber']; ?></td>
	<td width=52%><b>Patient Name:</b> <? echo $name; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>DOB : <? echo $bdate; ?><b></td>
	<td width=24%><b>Gender / Age:</b> <? echo $gender . " / " . $age; ?></td>
</tr>
</table>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr bgcolor="#CCCCCC">
	<td width=45% align="center">Drug Name</td>
	<td width=7% align="center">U. Price</td>
	<td width=10% align="center">Dos.</td>
	<td width=10% align="center">Freq.</td>
	<td width=10% align="center">Day</td>
	<td width=10% align="center">Qty/Avl</td>
	<td width=8% align="center">Total Price</td>
</tr>
<input type="button" name="hide" id="hide" onclick="xstooltip_hide('tooltip_123');" style="display:none;">
<tr>
	<td>			
		<input type="text" name="name" id="name" style="width:100%;" onKeyup="OnEvent(0,event);">
		<div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("name","hint","server.php");
		new Ajax.Autocompleter("name","hint","server.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
		myMeds = li.id.split("@@");		
		$('drug_code').value=myMeds[0];
		$('available').value=myMeds[1];
		$('price_code').value=myMeds[2];
		$('name').value=myMeds[3];
		$('deduct_1').value=myMeds[4];
		$('dosage_code').value=myMeds[5];
		$('frequency_code').value=myMeds[6];
		$('day_code').value=myMeds[7];
		$('quantity').value=myMeds[8];
		$('intake').value=(myMeds[9]).replace(',', '\n');
		$('caution').value=(myMeds[10]).replace(',', '\n');
		$('dosage').value=myMeds[11];
		$('frequency').value=myMeds[12];
		$('day').value=myMeds[13];
		$('medstat').value=myMeds[14];
		
		calculate();
		}
		</script>
		<input type="hidden" name="drug_code" id="drug_code">
	</td>
	<td>
		<input type="text" name="price_code" id="price_code" style="width:100%;" readonly="true" value="0.00">
	</td>
	<td>
		<input type="text" name="dosage_code" id="dosage_code" style="width:100%;"  onKeyup="OnEvent(1,event);">
		<input type="hidden" name="dosage" id="dosage">
		<div id="hint1"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("dosage_code","hint1","server1.php");
		new Ajax.Autocompleter("dosage_code","hint1","server1.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
		myDosage = li.id.split("@@");
		$('dosage').value=myDosage[1];
		$('dosage_code').value=myDosage[0];
		calculate();
		}
		</script>
	</td>
	<td>
		<input type="text" name="frequency_code" id="frequency_code" style="width:100%;" onKeyup="OnEvent(2,event);">
		<input type="hidden" name="frequency" id="frequency" value="">
		<div id="hint2"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("frequency_code","hint2","server2.php");
		new Ajax.Autocompleter("frequency_code","hint2","server2.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
		myFrequency = li.id.split("@@");
		$('frequency').value=myFrequency[1];
		$('frequency_code').value=myFrequency[0];
		calculate();
		}
		</script>
	</td>
	<td>
		<input type="text" name="day_code" id="day_code" style="width:100%;" onKeyup="OnEvent(3,event);">
		<input type="hidden" name="day" id="day" value="">
		<div id="hint3"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("day_code","hint3","server3.php");
		new Ajax.Autocompleter("day_code","hint3","server3.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
		myDay = li.id.split("@@");
		$('day').value=myDay[1];
		$('day_code').value=myDay[0];
		calculate();
		}
		</script>
	</td>
	<td>
		<input type="text" name="quantity" id="quantity" style="width:45%;" onfocus="this.form.hide.click();" onkeyup="pricing();">
		<input type="text" name="available" id="available" readonly="true" style="width:45%;" onfocus="this.form.hide.click();">
		<input type="hidden" name="deduct_1" id="deduct_1" />
	</td>
	<td>
		<input type="text" name="price" id="price" style="width:100%;" onfocus="this.form.hide.click();">
	</td>
</tr>
</table>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr>
	<td width="50%" align="center">Intake</td>
	<td width="50%" align="center">Caution</td>
</tr>
<tr>
	<td width="50%">
		<textarea rows=5 style="width:100%; height:50px;" id="intake" autocomplete="off" name="intake" onkeyup="this.value = this.value.replace('\n\n','\n');"></textarea>
		<div id="intake_div" style="display:none;background-color:white;"></div>
		<script type="text/javascript" language="javascript" charset="utf-8">
		// <![CDATA[
		  new Autocompleter.Local('intake','intake_div',
		  new Array(<? echo $intake; ?>), { tokens: new Array(',','\n'), fullSearch: true, partialSearch: true });
		// ]]>
		</script>
	</td>
	<td width="50%">
		<textarea rows=5 style="width:100%; height:50px;" id="caution" name="caution" autocomplete="off" onkeyup="this.value = this.value.replace('\n\n','\n');"></textarea>
		<div id="caution_div" style="display:none;background-color:white;"></div>
		<script type="text/javascript" language="javascript" charset="utf-8">
		// <![CDATA[
		  new Autocompleter.Local('caution','caution_div',
		  new Array(<? echo $caution; ?>), { tokens: new Array(',','\n'), fullSearch: true, partialSearch: true });
		// ]]>
		</script>
	</td>
</tr>
</table>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr>
	<td align="center" width=50%>Remarks
		<textarea name="remarks" id="remarks" style="width:100%;"></textarea>
	</td>
	<td align="center"  width=50%>	Plan
		<textarea name="plan" style="width:100%;" readonly="readonly"><? 
	$select = "SELECT tb2.*
	FROM h_queue tb1
	LEFT JOIN h_pnsoap tb2 ON (tb1.`SYSTEM QUEUE NUMBER` = tb2.`SYSTEM QUEUE NUMBER`)
	WHERE tb1.`SYSTEM QUEUE NUMBER` ='".$_GET['qnumber']."'
	ORDER BY tb2.`SYSTEM DATETIME ENTERED` DESC
	LIMIT 1";
	$data = $db_object->query($select); 
	$row_soap = $data->fetchRow();
	echo $row_soap['NOTES-PLAN'];
	?>
	</textarea>
	</td>
</tr>
</table>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr>
	<td colspan="4" align="center">
	<!-- <input type="button" value="Drug Mixture" style="width:10%;" onclick="self.location='mixture.php?id=<? echo $_GET['qnumber']; ?>'"> -->
	<input type="submit" name="new" id="new" style="width:10%;" value="New (f7)" <? if($_GET['qstatus'] > 300){echo 'disabled';}?>>
	<input type="submit" name="duplicate" id="duplicate" style="width:10%;" value="Copy (f8)">
	<input type="submit" name="save" id="save" value="Save (f2)" onclick="return xstooltip_show('tooltip_12345', 'save', -100, 30,9);" style="width:10%;" <? if($_GET['qstatus'] > 300){echo 'disabled';}?>>
	<input type="button" name="chinese" value="Chi Label" style="width:10%;" onclick='newReg(<? echo $_GET['qnumber']; ?>,2);'>
	<input name="button" value="Eng Label" type=button onclick='newReg(<? echo $_GET['qnumber']; ?>,1);'   style="width:10%;">
	<input type="submit" name="receive" id="receive" value="<? echo $dc; ?> (f6)" <? echo $disabledc; ?> onclick="return checkReceive();" style="width:20%; color:#FF0000; font-weight:bold;">
	<? if($_GET['from'] !='dispensing'){?>
	<input name="cancel" type="button" id="cancel" value="Return"  style="width:10%;" onClick="window.location='dispensing_item.php?qnumber=<? echo $_GET['qnumber']; ?>&pricecode=<? echo $_GET['pricecode']?>&PatientID=<? echo $_GET['PatientID'];?>';"/>
	<? }else{?>
		<input type="button" name="closing" value="Return" onclick="window.close();"  style="width:10%;">
	<? }?>
	</td>
</tr>
</table>

<input type="hidden" name="code" id="code" value="0">
<input type="hidden" name="id" id="id" value="0">
<input type="hidden" name="idqueue" id="idqueue">
<input type="hidden" name="qty" id="qty" value="0">
<input type="hidden" name="chqnty" id="chqnty">
<input type="hidden" name="origpassword" id="origpassword" value="<? echo $_SESSION['password']; ?>">
<input type="hidden" name="chday" id="chday">
<input type="hidden" name="chprice" id="chprice">
<input type="hidden" name="medstat" id="medstat">
<input type="hidden" name="origqnty" id="origqnty">
<input type="hidden" name="username" id="username">
<input type="hidden" name="usertype" id="usertype" value="<? echo $_SESSION['physician_code']; ?>">
<input type="hidden" name="auth" id="auth" value="<? echo $auth; ?>">
<input type="hidden" name="cusername" id="cusername" value="<? echo $_SESSION['username']; ?>">
<div class="ListScroll1">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr bgcolor="#66FFFF">
	<td colspan="13"><b>Current Prescription</b></td>
</tr>
<tr bgcolor="#CCCCCC">
	<td width=25%><b>Drug Name</b></td>
	<td width=7%><b>U. Price</b></td>
	<td width=5%><b>Dos.</b></td>
	<td width=5%><b>Freq.</b></td>
	<td width=5%><b>Day</b></td>
	<td width=8%><b>Pres. Qty</b></td>
	<td width=10%><b>Pres. By</b></td>
	<td width=10%><b>D Adjust By</b></td>
	<td width=5%><b>D Day</b></td>
	<td width=5%><b>D Qty</b></td>
	<td width=5%><b>Price</b></td>
	<td width=5px>&nbsp;</td>
	<td width=5px>&nbsp;</td>
</tr>
<?
$select = "select *  from mixture left join h_lpatie on (mixture.patient_id = h_lpatie.`SYSTEM PATIENT ID#`) where queue_number = '" . $_GET['qnumber'] . "' group by name";
$query = $db_object->query($select);
$y = $query->numRows();
$x=0;
while($row=$query->fetchRow()){
	$drugName = $row['name'];
	?>
	<tr ondblclick="self.location='mixture_edit.php?id=<? echo $row['mix_id']; ?>&qnumber=<? echo $_GET['qnumber']; ?>'" style="cursor:pointer;" title="Click to edit entry">
		<td><? echo  $drugName; ?></td>
		<td><? echo $row['price']; ?></td>
		<td><? echo $row['dosage']; ?></td>
		<td><? echo $row['frequency']; ?></td>
		<td><? echo $row['day']; ?></td>
		<td><? echo $row['quantity']; ?></td>
		<td><? echo $row['quantity']; ?></td>
		<td><? echo $row['quantity']; ?></td>
		<td><? echo $row['quantity']; ?></td>
		<td><? echo $row['quantity']; ?></td>
		<td><? echo $row['price']; ?></td>
		<td align="center"><input type="submit" name="delete1" value="X" style="width:70%; color:#FF0000; font-weight:bold;" onClick="return OnDelete(this.form,'<? echo $row['queue_number']; ?>','<? echo $row['name']; ?>')"></td>
	</tr>
	<?
	}

$select = "SELECT * FROM h_meds WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND 
	(STATUS = 'PRESCRIBE' OR STATUS = 'DISPENCED')";
$query=$db_object->query($select);
$y = $query->numRows();
$total = 0;

while($row=$query->fetchRow()){
	$drugName = getDrugName($db_object,$row['MEDICINE CODE']);
	$intake1 = explode("\n",$row['INTAKE']);
	$caution1 = explode("\n",$row['CAUTION']);
	$intake = "";
	$caution = "";
	for($c=0;$c<count($intake1);$c++){
		$intake = $intake . trim($intake1[$c])  . "@";
		}
	for($c=0;$c<count($caution1);$c++){
		$caution = $caution . trim($caution1[$c])  . "@";
		}
	if($_SESSION['username'] != $row['USERNAME']){
		//$disable = "disabled='true'";
		$disable = "";
		$value = 1;
		}
	else{
		$disable = "";
		$value = 2;
		}
		
	$row['REMARKS'] = str_replace("\n"," ",$row['REMARKS']);
	
	$select = "select * from history where `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' and `MEDICINE CODE` = '" . $row['MEDICINE CODE'] . "' and `TABLE` = 'h_quantity' order by `ID` desc limit 1";
	$queryl=$db_object->query($select);
	$lastrow = $queryl->fetchRow();
	$last = $lastrow['USERNAME'];
	if ($row['STATUS'] == 'PRESCRIBE'){
	?>
	<input type="hidden" id="dontsave<? echo $x; ?>" id="dontsave<? echo $x; ?>" value="<? echo $value; ?>">
	<tr name = "td<? echo $x; ?>" id = "td<? echo $x; ?>" style="cursor:pointer;" title="Click to edit entry">
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo  $drugName; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['UNIT PRICE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['DOSAGE CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['FREQUENCY CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['DAY CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['QUANTITY']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $row['USERNAME']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');" ><? echo $last; ?></td>
		<td bgcolor="#3090C7"><input type="text" name="editday<? echo $x; ?>" id="editday<? echo $x; ?>" style="width:100%;" value="<? echo $row['CHDAY']; ?>" onkeyup="chchange(this.form,'<? echo $x; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['UNIT PRICE']; ?>',1)"></td>
		<td bgcolor="#3090C7"><input type="text" name="editqnty<? echo $x; ?>" id="editqnty<? echo $x; ?>" style="width:100%;" value="<? echo $row['CHQNTY']; ?>" onkeyup="chchange(this.form,'<? echo $x; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['UNIT PRICE']; ?>',2)"></td>
		<td bgcolor="#3090C7"><input type="text" style="width:100%;" id="editprice<? echo $x; ?>" name="editprice<? echo $x; ?>" value="<? echo roundoff($row['TOTAL PRICE']); ?>" readonly="true"></td>
		<td align="center" bgcolor="#3090C7"><input type="button" name="finalchange" id="change<? echo $x; ?>" value="C" <? echo $disable; ?> style="width:100%; color:#FF0000; font-weight:bold;" onclick="this.form.id.value=<? echo $row['ID']; ?>;this.form.chqnty.value=this.form.editqnty<? echo $x; ?>.value;this.form.chday.value=this.form.editday<? echo $x; ?>.value;this.form.origqnty.value=<? echo $row['QUANTITY']; ?>;this.form.chprice.value=this.form.editprice<? echo $x; ?>.value;this.form.username.value='<? echo $row['USERNAME']; ?>';xstooltip_show('tooltip_1234', 'change<? echo $x; ?>', -460, 0,4);" ondblclick="xstooltip_hide('tooltip_1234');"></td>
		<td align="center"><input type="submit" name="delete" value="X" style="width:100%; color:#FF0000; font-weight:bold;" onClick="return OnDelete(this.form,'<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>')"></td>
	</tr>
	<?
	}
	else{
	?>
	<input type="hidden" id="dontsave<? echo $x; ?>" id="dontsave<? echo $x; ?>" value="3">
	<tr name = "td<? echo $x; ?>" id = "td<? echo $x; ?>" style="cursor:pointer;" title="Click to edit entry">
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo  $drugName; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['UNIT PRICE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['DOSAGE CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['FREQUENCY CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['DAY CODE']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['QUANTITY']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $row['USERNAME']; ?></td>
		<td onclick="return CheckAll(<? echo $y; ?>,<? echo $x; ?>,'<? echo $intake; ?>','<? echo $caution; ?>','<? echo $row['DOSAGE CODE']; ?>','<? echo $row['FREQUENCY CODE']; ?>','<? echo $row['DAY CODE']; ?>','<? echo $row['CHQNTY']; ?>','<? echo roundoff($row['TOTAL PRICE']); ?>','<? echo $row['UNIT PRICE']; ?>','<? echo  $drugName; ?>','<? echo $row['QUANTITY_BEFORE']; ?>','<? echo $row['DOSAGE']; ?>','<? echo $row['FREQUENCY']; ?>','<? echo $row['DAY']; ?>','<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>','<? echo $row['CATEGORY4']; ?>','<? echo $row['REMARKS']; ?>');"><? echo $last; ?></td>
		<td><? echo $row['CHDAY']; ?></td>
		<td><? echo $row['CHQNTY']; ?></td>
		<td><? echo roundoff($row['TOTAL PRICE']); ?></td>
		<td colspan="2"><input type="submit" name="cancel" value="cancel" style="width:100%;" onClick="return OnDelete(this.form,'<? echo $row['MEDICINE CODE']; ?>','<? echo $row['ID']; ?>','<? echo $row['USERNAME']; ?>')"></td>
	</tr>
	<?
	}
	$x++;
	$total = $total + $row['TOTAL PRICE'];
	$net = $net + $row['NET PRICE'];
	}
	?>
	<input type="submit" name="finalchange1" id="finalchange1" style="display:none;">
	</table>
</div>

<?
$select = "select `DISCOUNT` from h_procs WHERE `SYSTEM QUEUE NUMBER` = '" . $_GET['qnumber'] . "' AND `SYSTEM ITEM CODE` = '3100000'";
$query=$db_object->query($select);
$row=$query->fetchRow();

if ($row['DISCOUNT']){
	$discount = $row['DISCOUNT'];
	$net = $total - $discount;
	}
else{
	$discount = 0;
	$net = 0;
	}
?>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr>
	<td align="right" width=26%>Ttl Med Price : <input type="text" name="total_price" style="width:50%;" value="<? echo roundoff($total,2); ?>" readonly></td>
	<td align="right" width=27%>Discount Amount : <input type="text" name="discount" style="width:50%;" value="<? echo $discount; ?>" onkeyup="this.form.net_price.value=(this.form.total_price.value-this.value).toFixed(2);"></td>
	<td align="right" width=27%>Net Amount : <input type="text" name="net_price" style="width:50%;" readonly value="<? echo $net; ?>"></td>
	<td align="right" width=20%><input type="submit" name="savemed" value="Save" style="width:33%;"><input type="button" name="billmed" value="Bill Medication" style="width:65%;" onclick="checkbillmed();"></td>
</tr>
</table>
<div class="ListScroll">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
<tr bgcolor="#66FFFF">
	<td colspan="11"><b>Prescription History </b></td>
</tr>
<tr bgcolor="#CCCCCC">
	<td width=10%><b>Date</b></td>
	<td width=30%><b>Drug Name</b></td>
	<td width=7%><b>U. Price</b></td>
	<td width=5%><b>Dos.</b></td>
	<td width=5%><b>Freq.</b></td>
	<td width=5%><b>Day</b></td>
	<td width=8%><b>Pres. Qty</b></td>
	<td width=15%><b>Enter By</b></td>
	<td width=5%><b>D Day</b></td>
	<td width=5%><b>D Qty</b></td>
	<td width=5%><b>Price</b></td>
</tr>
<?
$select = "SELECT h_meds.* 
FROM h_meds 
JOIN h_queue USING(`SYSTEM QUEUE NUMBER`) 
WHERE STATUS != 'DELETED' 
AND `SYSTEM PATIENT ID#` = '" . $id . "' 
ORDER BY `SYSTEM QUEUE NUMBER`";
$query=$db_object->query($select);
$y = $query->numRows();
$x=0;
$q = '';
while($row=$query->fetchRow()){
	$drugName = getDrugName($db_object,$row['MEDICINE CODE']);
	?>
	<tr name = "prev<? echo $x; ?>" id = "prev<? echo $x; ?>" style="cursor:pointer;" onclick="Check(<? echo $y; ?>,<? echo $x; ?>,'<? echo $row['SYSTEM QUEUE NUMBER']?>');pharma.idqueue.value='<? echo $row['SYSTEM QUEUE NUMBER']; ?>';">
		<td><? if ($q !=$row['SYSTEM QUEUE NUMBER']){ echo $row['DATE RELEASED']; } ?></td>
		<td><? echo  $drugName; ?></td>
		<td><? echo $row['UNIT PRICE']; ?></td>
		<td><? echo $row['DOSAGE CODE']; ?></td>
		<td><? echo $row['FREQUENCY CODE']; ?></td>
		<td><? echo $row['DAY CODE']; ?></td>
		<td><? echo $row['QUANTITY']; ?></td>
		<td><? echo $row['USERNAME']; ?></td>
		<td><? echo $row['CHDAY']; ?></td>
		<td><? echo $row['CHQNTY']; ?></td>
		<td><? echo roundoff($row['TOTAL PRICE']); ?></td>
	</tr>
	<?
	$x++;
	$q = $row['SYSTEM QUEUE NUMBER'];
	}
	?>
</table>
</div>
<div id="tooltip_123" class="xstooltip">
	<iframe name="medicine_list" width=100% height=300 frameborder="0"></iframe>
</div>
<div id="tooltip_1234" class="xstooltips">
	<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
	<tr>
		<td width="5%"><input type="radio" name="rdo" id="rdo" onclick="this.form.reason.value='Patient has a stock at home.';"></td>
		<td width="95%">Patient has a stock at home.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason.value='Patient prefers to buy from outside stores.';"></td>
		<td>Patient prefers to buy from outside stores.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason.value='Patient refuses to take the medicine';"></td>
		<td>Patient refuses to take the medicine</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason.value='Patient request to increase no. of days / qty of drug dispensed.';"></td>
		<td>Patient request to increase no. of days / qnt of drug dispenced.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason.value='';"></td>
		<td>Others : <input type="text" name="reason" id="reason" style="width:80%;"></td>
	</tr>
	<tr>
		<td colspan="2">password : <input type="password" name="password" id="password" style="width:50%;"  autocomplete="off"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="finalchange" id="finalchange" onclick="return Onsave('password');" value="Update"><input type="button" value="Cancel" onclick="xstooltip_hide('tooltip_1234');"></td>
	</tr>
	</table>
</div> 
<div id="tooltip_12345" class="xstooltips">
	<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0" rules="all" style="border-style:solid;">
	<tr>
		<td width="5%"><input type="radio" name="rdo" id="rdo" onclick="this.form.reason1.value='Patient has a stock at home.';"></td>
		<td width="95%">Patient has a stock at home.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason1.value='Patient prefers to buy from outside stores.';"></td>
		<td>Patient prefers to buy from outside stores.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason1.value='Patient refuses to take the medicine';"></td>
		<td>Patient refuses to take the medicine</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason1.value='Patient request to increase no. of days / qty of drug dispensed.';"></td>
		<td>Patient request to increase no. of days / qnt of drug dispenced.</td>
	</tr>
	<tr>
		<td><input type="radio" name="rdo" id="rdo" onclick="this.form.reason1.value='';"></td>
		<td>Others : <input type="text" name="reason1" id="reason1" style="width:80%;"></td>
	</tr>
	<tr>
		<td colspan=2>password : <input type="password" name="password1" id="password1" style="width:50%;" autocomplete="off"></td>
	</tr>
	<tr>
		<td colspan=2><input type="submit" name="save" value="Update" onclick="return Onsave('password1');"><input type="button" value="Cancel" onclick="xstooltip_hide('tooltip_12345');"></td>
	</tr>
	</table>
</div> 
<div id="showlabel">
	<table width=100%  height=100%>
		<tr>
			<td align="center">
				<div>
					<img src="images_files/we.jpg" width=100% height=25 onClick="return chooselabel(0);">
					<iframe name="listlabel" width=100% height=375 frameborder="0"></iframe>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id="showlabel1">
	<table width=100%  height=100%>
		<tr>
			<td align="center">
				<div>
					<img src="images_files/we.jpg" width=100% height=25 onClick="return checkReceive();">
					<table width=100%>
					<tr height=50>
						<td width=50%><input type="button" value="Cancel" id="cancelbut" name="cancelbut" onClick="return checkReceive();" style="width:100%;"></td>
						<td width=50%>Return</td>
					</tr>
					<tr height=50>
						<td><input type="submit" name="receive" value="Dispense Complete" style="width:100%;"></td>
						<td>Drug has been released to patient</td>
					</tr>
					<tr height=50>
						<td><input type="submit" name="receivebilled" value="Dispense Complete and Bill Medication" style="width:100%;"></td>
						<td>Drug has been released to patient and bill patient on medication</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>
<div id="showlabel2">
	<table width=100%  height=100%>
		<tr>
			<td align="center">
				<div>
					<img src="images_files/we.jpg" width=100% height=25 onClick="return checkbillmed();">
					<table width=100% height="175">
					<tr>
						<td colspan=2 align="center">Click Ok To Bill Medication</td>
					</tr>
					<tr>
						<td width=50% align="center"><input type="button" value="Cancel" id="cancelbutmed" name="cancelbutmed" onClick="return checkbillmed();" style="width:90%;"></td>
						<td width=50% align="center"><input type="submit" name="billmed" value="Ok" style="width:90%;"></td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>
</form>
</body>
<style>
.ListScroll{
height:175px;
overflow:scroll;
border: 1px solid #000000;
}

.ListScroll1{
height:175px;
overflow:scroll;
border: 1px solid #000000;
}

#showlabel div { 
	width:500; 
	height:400;
	background-color: #fff;
	}
	
#showlabel { 
	visibility: hidden; 
	position: absolute; 
	left: 0px; 
	top: 0px; 
	width:100%; 
	height:100%; 
	text-align:center; 
	z-index: 1000; 
	background-image:url(images_files/overlay.gif);
	}
#showlabel1 div { 
	width:500; 
	height:200;
	background-color: #fff;
	}
	
#showlabel1 { 
	visibility: hidden; 
	position: absolute; 
	left: 0px; 
	top: 0px; 
	width:100%; 
	height:100%; 
	text-align:center; 
	z-index: 1000; 
	background-image:url(images_files/overlay.gif);
	}
#showlabel2 div { 
	width:500; 
	height:200;
	background-color: #fff;
	}
	
#showlabel2 { 
	visibility: hidden; 
	position: absolute; 
	left: 0px; 
	top: 0px; 
	width:100%; 
	height:100%; 
	text-align:center; 
	z-index: 1000; 
	background-image:url(images_files/overlay.gif);
	}
</style>
<style type="text/css">
.xstooltip 
{
    visibility: hidden; 
    position: absolute; 
    top: 0;  
    left: 0; 
    z-index: 2; 
    width:600px;

    font: normal 8pt sans-serif; 
    margin: 0px 0px 0px 0px;
    padding: 0 0 0 0;
    border: solid 1px;
    background-color: white;
}

.xstooltips 
{
    visibility: hidden; 
    position: absolute; 
    top: 0;  
    left: 0; 
    z-index: 2; 
    width:460px;

    font: normal 8pt sans-serif; 
    margin: 0px 0px 0px 0px;
    padding: 0 0 0 0;
    border: solid 1px;
    background-color: white;
}
</style>	
<script language="JavaScript">
function xstooltip_findPosX(obj){
	var curleft = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
			}
		}
	else if (obj.x){
		curleft += obj.x;
		}
	return curleft;
	}

function xstooltip_findPosY(obj){
	var curtop = 0;
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop
			obj = obj.offsetParent;
			}
		}
	else if (obj.y){
		curtop += obj.y;
		}
	return curtop;
	}

function xstooltip_show(tooltipId, parentId, posX, posY,id){
	it = document.getElementById(tooltipId);
    	img = document.getElementById(parentId); 
    
	x = xstooltip_findPosX(img) + posX;
	y = xstooltip_findPosY(img) + posY;
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	
	if(document.getElementById("medstat").value == 'INACTIVE'){
		if (document.getElementById("quantity").value > document.getElementById("available").value){
			alert("this meds is on inactive you can only dispence " + document.getElementById("available").value);
			return false;
			}		
		}
	
	if (id == 9){
		dos = document.getElementById("dosage").value;
		fre = document.getElementById("frequency").value;
		day = document.getElementById("day").value;
		quantity = dos * day * fre;
		
		if(document.getElementById("deduct_1").value == "Y"){
			if(parseFloat(document.getElementById("quantity").value) == 1){
				return true;
				}
			else{
				if (parseFloat(document.getElementById("auth").value) > 0){
					it.style.visibility = 'visible'; 
					return false;
					}
				else{
					if (document.getElementById("username").value == document.getElementById("cusername").value){
						//it.style.visibility = 'visible'; 
						return true;
						}
					else{
						//alert("You are not authorized to adjust dispense qty which are greater than prescribed qty");
						if(document.getElementById("usertype").value){
							return true;
							}
						else{
							it.style.visibility = 'visible'; 
							return false;
							}
						}
					}			
				}
			}
		else{
			if(parseFloat(document.getElementById("quantity").value) == parseFloat(quantity)){
				return true;
				}
			else{
				if (parseFloat(document.getElementById("auth").value) > 0){
					it.style.visibility = 'visible'; 
					return false;
					}
				else{
					if (document.getElementById("username").value == document.getElementById("cusername").value){
						it.style.visibility = 'visible'; 
						return false;
						}
					else{
						if(parseFloat(quantity) == 0){
							var agree=confirm("system auto compute... dosage * frequency * day = 0!!!!  Do you want to contineu?");
							if (agree)
								return true ;
							else
								return false ;
							}
						else{
							//alert("You are not authorized to adjust dispense qty which are greater than prescribed qty");
							return true;
							}
						}
					}			
				}
			}
		}
	if (id == 4){
		if (parseFloat(document.getElementById("chqnty").value) == parseFloat(document.getElementById("origqnty").value)){
			it.style.visibility = 'visible'; 
			}
		else{
			if (document.getElementById("username").value == document.getElementById("cusername").value){
				it.style.visibility = 'visible'; 
				}
			else{
				if (parseFloat(document.getElementById("chqnty").value) > parseFloat(document.getElementById("origqnty").value)){
					if (parseFloat(document.getElementById("auth").value) > 0){
						it.style.visibility = 'visible'; 
						}
					else{
						it.style.visibility = 'visible'; 
						//alert("You are not authorized to adjust dispense qty which are greater than prescribed qty");
						}
					}
				else{
					it.style.visibility = 'visible'; 
					}
				}
			}
		}	
	}

function xstooltip_hide(id){
	it = document.getElementById(id); 
	it.style.visibility = 'hidden'; 	
	}

function OnEvent(id,evt){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (id == 5){
		if (charCode == 113){
			document.getElementById("save").click();
			}
		if (charCode == 117){
			document.getElementById("receive").click();
			}
		if (charCode == 118){
			document.getElementById("new").click();
			}
		if (charCode == 116){
			document.getElementById("new").click();
			}
		if (charCode == 119){
			document.getElementById("duplicate").click();
			}
		}
	else{
		if (charCode == 13){
			if (id == 0){
				document.getElementById("dosage_code").focus();
				}
			if (id == 1){
				document.getElementById("frequency_code").focus();
				}
			if (id == 2){
				document.getElementById("day_code").focus();
				}
			if (id == 3){
				document.getElementById("quantity").focus();
				calculate();
				}
			if (id == 4){
				document.getElementById("intake").focus();
				}
			}
		else if (charCode == 113){
			document.getElementById("save").click();
			}
		else if (charCode == 33 || charCode == 34 || charCode == 35 || charCode == 36 || charCode == 37 || charCode == 38 || charCode == 39 || charCode == 40 || charCode == 9  || charCode == 27 || charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20){
			calculate();
			}
		else{
			if (id == 1){
				document.getElementById("dosage").value=document.getElementById("dosage_code").value;
				}
			if (id == 2){
				document.getElementById("frequency").value=document.getElementById("frequency_code").value;
				}
			if (id == 3){
				document.getElementById("day").value=document.getElementById("day_code").value;
				}
			calculate();
			}
		}
	}

function calculate(){	
	if (document.getElementById("dosage").value == "" || document.getElementById("dosage").value == "NONE"){
		document.getElementById("dosage").value = document.getElementById("dosage_code").value;
		}
	if (document.getElementById("frequency").value == "" || document.getElementById("frequency").value == "NONE"){
		document.getElementById("frequency").value = document.getElementById("frequency_code").value;
		}
	if (document.getElementById("day").value == "" || document.getElementById("day").value == "NONE"){
		document.getElementById("day").value = document.getElementById("day_code").value;
		}
		
	dos = document.getElementById("dosage").value;
	fre = document.getElementById("frequency").value;
	day = document.getElementById("day").value;
		
	quantity = dos * day * fre;
	if(document.getElementById("deduct_1").value == "Y"){
		document.getElementById("quantity").value = 1;
		}
	else{
		document.getElementById("quantity").value = quantity;
		}
	document.getElementById("price").value = document.getElementById("quantity").value * document.getElementById("price_code").value;
	}
	
function pricing(){
	price = document.getElementById("quantity").value * document.getElementById("price_code").value;
	document.getElementById("price").value = price
	}
	
function chooselabel(id){
	el = window.document.getElementById("showlabel");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	
	if (id != 0){
		window.frames["listlabel"].location = 'label/label_med_pdf.php?code=' + id;
		}
	
	else if(id ==0){
		window.document.getElementById("showlabel").close;
	}	
}
function newReg(id,idd){
	el = window.document.getElementById("showlabel");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	
	if (idd == 1){
		window.frames["listlabel"].location = 'label_med_english.php?qnumber='+id;
		}
	else{
		window.frames["listlabel"].location = 'label_med_chinese.php?qnumber='+id;
		}

}

function trim (str) {
        str = this != window? this : str;
        return str.replace(/^s+/, '').replace(/s+$/, '');
    }

function CheckAll(count,x,intake,caution,dosage_code,frequency_code,day_code,quantity,total_price,unit_price,drug_code,available,dosage,frequency,day,drug,id,username,category4,remarks){
	if(document.getElementById("dontsave" + x).value == 2){
		intake = intake.split("@");
		cintake = "";
		for(w=0;w<intake.length;w++){
			test = (intake[w]).replace(' ','');
			if (test != ""){
				if (w == intake.length){
					cintake = cintake + intake[w];
					}
				else{
					}
					cintake = cintake + intake[w] + "\n";
				}
			}
			
		caution = caution.split("@");
		ccaution = "";
		for(w=0;w<caution.length;w++){
			test = (caution[w]).replace(" ","");
			if (test != ""){
				if (w == caution.length){
					ccaution = ccaution + caution[w];
					}
				else{
					ccaution = ccaution + caution[w] + "\n";
					}
				}
			}
		
		document.getElementById("intake").value = cintake;
		document.getElementById("caution").value = ccaution;
		document.getElementById("remarks").value = remarks;
		
		
		document.getElementById("username").value = username;
		
		document.getElementById("dosage_code").value = dosage_code;
		document.getElementById("frequency_code").value = frequency_code;
		document.getElementById("day_code").value = day_code;
		document.getElementById("drug_code").value = drug;
		document.getElementById("code").value = drug;
		
		document.getElementById("dosage_code").readOnly = false;
		document.getElementById("frequency_code").readOnly = false;
		document.getElementById("day_code").readOnly = false;
		document.getElementById("drug_code").readOnly = false;
		document.getElementById("quantity").readOnly = false;
		document.getElementById("name").readOnly = false;
		document.getElementById("price").readOnly = false;
		
		document.getElementById("available").value = available;
		document.getElementById("name").value = drug_code;
		document.getElementById("dosage").value = dosage;
		document.getElementById("frequency").value = frequency;
		document.getElementById("day").value = day;
		document.getElementById("quantity").value = quantity;
		document.getElementById("qty").value = quantity;
		document.getElementById("price").value = total_price;
		document.getElementById("price_code").value = unit_price;
		document.getElementById("deduct_1").value = category4;	
		document.getElementById("id").value = id;	
		document.getElementById("save").disabled = false;
		}
	
	else if(document.getElementById("dontsave" + x).value == 3){
		intake = intake.split("@");
		cintake = "";
		for(w=0;w<intake.length;w++){
			test = (intake[w]).replace(' ','');
			if (test != ""){
				if (w == intake.length){
					cintake = cintake + intake[w];
					}
				else{
					}
					cintake = cintake + intake[w] + "\n";
				}
			}
			
		caution = caution.split("@");
		ccaution = "";
		for(w=0;w<caution.length;w++){
			test = (caution[w]).replace(" ","");
			if (test != ""){
				if (w == caution.length){
					ccaution = ccaution + caution[w];
					}
				else{
					ccaution = ccaution + caution[w] + "\n";
					}
				}
			}
		
		document.getElementById("intake").value = cintake;
		document.getElementById("caution").value = ccaution;
		document.getElementById("remarks").value = remarks;
		
		document.getElementById("username").value = username;
		
		document.getElementById("dosage_code").value = dosage_code;
		document.getElementById("frequency_code").value = frequency_code;
		document.getElementById("day_code").value = day_code;
		document.getElementById("drug_code").value = drug;
		
		if (document.getElementById("auth").value > 0){
			document.getElementById("dosage_code").readOnly = false;
			document.getElementById("frequency_code").readOnly = false;
			document.getElementById("day_code").readOnly = false;
			document.getElementById("drug_code").readOnly = false;
			document.getElementById("quantity").readOnly = false;
			document.getElementById("name").readOnly = false;
			document.getElementById("price").readOnly = false;
			}
		else{
			document.getElementById("dosage_code").readOnly = true;
			document.getElementById("frequency_code").readOnly = true;
			document.getElementById("day_code").readOnly = true;
			document.getElementById("drug_code").readOnly = true;
			document.getElementById("quantity").readOnly = true;
			document.getElementById("name").readOnly = true;
			document.getElementById("price").readOnly = true;
			}
		
		document.getElementById("code").value = drug;
		
		document.getElementById("available").value = available;
		document.getElementById("name").value = drug_code;
		document.getElementById("dosage").value = dosage;
		document.getElementById("frequency").value = frequency;
		document.getElementById("day").value = day;
		document.getElementById("quantity").value = quantity;
		document.getElementById("qty").value = quantity;
		document.getElementById("price").value = total_price;
		document.getElementById("price_code").value = unit_price;
		document.getElementById("deduct_1").value = category4;	
		document.getElementById("id").value = id;		
		document.getElementById("save").disabled = true;
		}
	
	else{
		intake = intake.split("@");
		cintake = "";
		for(w=0;w<intake.length;w++){
			test = (intake[w]).replace(' ','');
			if (test != ""){
				if (w == intake.length){
					cintake = cintake + intake[w];
					}
				else{
					}
					cintake = cintake + intake[w] + "\n";
				}
			}
			
		caution = caution.split("@");
		ccaution = "";
		for(w=0;w<caution.length;w++){
			test = (caution[w]).replace(" ","");
			if (test != ""){
				if (w == caution.length){
					ccaution = ccaution + caution[w];
					}
				else{
					ccaution = ccaution + caution[w] + "\n";
					}
				}
			}
			
		document.getElementById("intake").value = cintake;
		document.getElementById("caution").value = ccaution;
		document.getElementById("remarks").value = remarks;
		
		document.getElementById("username").value = username;
		
		document.getElementById("dosage_code").value = dosage_code;
		document.getElementById("frequency_code").value = frequency_code;
		document.getElementById("day_code").value = day_code;
		document.getElementById("drug_code").value = drug;
		
		if (document.getElementById("auth").value > 0){
			document.getElementById("dosage_code").readOnly = false;
			document.getElementById("frequency_code").readOnly = false;
			document.getElementById("day_code").readOnly = false;
			document.getElementById("drug_code").readOnly = false;
			document.getElementById("quantity").readOnly = false;
			document.getElementById("name").readOnly = false;
			document.getElementById("price").readOnly = false;
			}
		else{
			document.getElementById("dosage_code").readOnly = true;
			document.getElementById("frequency_code").readOnly = true;
			document.getElementById("day_code").readOnly = true;
			document.getElementById("drug_code").readOnly = true;
			document.getElementById("quantity").readOnly = true;
			document.getElementById("name").readOnly = true;
			document.getElementById("price").readOnly = true;
			}
		
		document.getElementById("code").value = drug;
		
		document.getElementById("available").value = available;
		document.getElementById("name").value = drug_code;
		document.getElementById("dosage").value = dosage;
		document.getElementById("frequency").value = frequency;
		document.getElementById("day").value = day;
		document.getElementById("quantity").value = quantity;
		document.getElementById("qty").value = quantity;
		document.getElementById("price").value = total_price;
		document.getElementById("price_code").value = unit_price;
		document.getElementById("deduct_1").value = category4;	
		document.getElementById("id").value = id;	
		
		document.getElementById("save").disabled = false;
		}
	
	if(count>1){
		for (y=0; y<count; y++){
			if (x == y){
				document.getElementById("td"+x).style.backgroundColor='lightblue';
				}
			else{
				document.getElementById("td"+y).style.backgroundColor='white';
				}
			}
		}
	else{
		document.getElementById("td"+x).style.backgroundColor='lightblue';
		}
	}

function OnDelete(thisform,code,id,username){
	if (username == document.getElementById("cusername").value){
		if(confirm("Do you really want to DELETE this item")==false){ 
			return false; 
			}
		else{
			thisform.code.value=code;
			thisform.id.value=id;
			return true;
			}
		}
	else{
		alert('You are not authorized to delete this item')
		return false;
		}
	}

function chchange(thisform,x,fre,dos,price,id){
	if (id == 1){
		qnty =  document.getElementById("editday"+x).value * fre * dos;
		document.getElementById("editqnty"+x).value = qnty;
		}
	document.getElementById("editprice"+x).value = document.getElementById("editqnty"+x).value * price;
	}
	
function Check(count,x,q){
	document.getElementById("idqueue").value = q;
	if(count>1){
		for (y=0; y<count; y++){
			if (x == y){
				document.getElementById("prev"+x).style.backgroundColor='lightblue';
				}
			else{
				document.getElementById("prev"+y).style.backgroundColor='white';
				}
			}
		}
	else{
		document.getElementById("prev"+x).style.backgroundColor='lightblue';
		}
	}

function stopRKey(evt) {
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
	if(evt.keyCode == 113){ return false;}
	if(evt.keyCode == 116){ return false;}
	if(evt.keyCode == 117){ return false;}
	if(evt.keyCode == 118){ return false;}
	if(evt.keyCode == 119){ return false;}
	}
	
function Onsave(id){
	p1 = document.getElementById("origpassword").value;
	p2 = hex_md5(document.getElementById(id).value);
	
	if (p1 == p2){
		return true;
		}
	else{
		alert ('incorrect password!!!')
		return false;
		}
	}
	
function checkReceive(){
	el = window.document.getElementById("showlabel1");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	document.getElementById("cancelbut").focus();
	return false ;
	}
	
function checkbillmed(){
	el = window.document.getElementById("showlabel2");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	document.getElementById("cancelbutmed").focus();
	return false ;
	}
	
document.onkeypress = stopRKey; 

document.getElementById("name").focus();

var Thiswidth=screen.width;
var Thisheight=screen.height;
window.moveTo(0,0);
window.resizeTo(Thiswidth,Thisheight-40);
</script>
