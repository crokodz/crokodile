

<?php
function random(){
	srand ((double) microtime( )*1000000);
	return rand(0,10000000);
	}

$file = date("YmdHis").".pdf";
$f = $_GET['fdate'];
$t = $_GET['tdate'];
$compa = $_GET['compa'];
$c = "python sss_new_reg.py " . $file . " " . $f . " " . $t. " " . $compa;
exec($c);
?>
<script>
self.location='<?php echo $file; ?>';
</script>