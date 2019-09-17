
<?php
function random(){
	srand ((double) microtime( )*1000000);
	return rand(0,10000000);
	}

function spc2txt($txt){
	return str_replace(" ","@@",$txt);
	}

$file = date("YmdHis").".pdf";
$vars = $_GET['vars'];
$id = $_GET['id'];
$tdz = $_GET['tdz'];


$c = "/usr/bin/python bpipdf.py " . $file . " " . $id . " " . $tdz . " 2>&1";
exec($c, $a);
?>
<script>
self.location='<?php echo $file; ?>';
</script>
