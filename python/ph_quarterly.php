
<?php
function random(){
	srand ((double) microtime( )*1000000);
	return rand(0,10000000);
	}

$file = date("YmdHis").".pdf";
$vars = $_GET['vars'];
$compa = $_GET['compa'];
echo $c = "python ph_remit.py " . $file . " " . $vars . " " . $compa;
die();
exec($c);
?>
<script>
self.location='<?php echo $file; ?>';
</script>