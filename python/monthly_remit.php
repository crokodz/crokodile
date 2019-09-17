<?php
function random(){
	srand ((double) microtime( )*1000000);
	return rand(0,10000000);
	}

$from = $_GET['date'];
$file = date("YmdHis").".pdf";

$c = "python monthly_remit.py " . $file;
exec($c);
?>
<script>
self.location='<?php echo $file; ?>';
</script>