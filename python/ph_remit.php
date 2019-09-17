
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
$compa = $_GET['compa'];
$td = spc2txt($_GET['td']);
$ap = spc2txt($_GET['ap']);
$or = spc2txt($_GET['or']);

echo $c = "python ph_remit.py " . $file . " " . $vars . " " . $compa . " " . $td . " " . $ap . " " . $or;
exec($c);

?>
<script>
self.location='<?php echo $file; ?>';
</script>