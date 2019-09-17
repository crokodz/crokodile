
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("employee.xls");

$column_header = array('employee id','name','address','contact number','birthday','gender','civil status','citizenship','contact person','cp number','cp address','position','salary','salary based','department','description','branch','status','input by','input date','date employed','date permanent','sss','pi','tin','ph','sss number','pagibig number','tax  number','philhealth number','tax status','height','weight','race','employee status','allowed ot','allowed late','reason leaving','date of leaving','shift code','allowed ut','company id','pay code','pin number','biometrics number','manager','picture','login id','icn','ename','zipcode','email','idd','fname','lname','mname','division','section','daily','hourly','minly','bank account','bank name','payment type','employee type','file status','allowance','union name','mag card','date department','city','wtax','pdm','blood type', 'salary date change', 'old salary', 'new salary', '1st 13', '2nd 13', 'bonus');
$excel->writeLine($column_header);

$appendsql = " ( ";
$var = explode("@@",$_GET['vars']);
for($x=0;$x<count($var);$x++){
	if ($var[$x]){
		if ($x==count($var)-2){
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "') ";
			}
		else{
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "' or ";
			}
		}
	}
$company = "";
if ($_SESSION['company'] != 0){
	$company = " where company_id = " . $_SESSION['company'] . " AND status = 'ACTIVE'";
}

$select = "select *,
	(select date from employee_salary t2 where t2.em_id =  employee.em_id and type = 'INCREASE' ORDER BY date desc LIMIT 1) as salary_date_change,
	(select old_salary from employee_salary t2 where t2.em_id =  employee.em_id and type = 'INCREASE' ORDER BY date desc  LIMIT 1) as old_salary,
	(select new_salary from employee_salary t2 where t2.em_id =  employee.em_id and type = 'INCREASE' ORDER BY date desc LIMIT 1) as new_salary
	   from employee" . $company;
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$excel->writeLine(array($row['em_id'],$row['name'],$row['em_address'],$row['em_number'],$row['birthdate'],$row['gender'],$row['civil_status'],$row['citizenship'],$row['contact_person'],$row['cp_number'],$row['cp_address'],$row['position'],$row['salary'],$row['salary_based'],$row['department'],$row['description'],$row['branch'],$row['status'],$row['username'],$row['datetime'],$row['date_employed'],$row['date_permanent'],$row['sss'],$row['pi'],$row['tin'],$row['ph'],$row['sssn'],$row['pin'],$row['tinn'],$row['phn'],$row['ts'],$row['height'],$row['weight'],$row['race'],$row['employee_status'],$row['allowed_ot'],$row['allowed_late'],$row['reason_living'],$row['reason_living_date'],$row['shift_code'],$row['allowed_ut'],$row['company_id'],$row['pay_id'],$row['pin_number'],$row['finger'],$row['manager'],$row['empicture'],$row['login_id'],$row['icn'],$row['ename'],$row['zipcode'],$row['email'],$row['idd'],$row['fname'],$row['lname'],$row['mname'],$row['division'],$row['section'],$row['daily'],$row['hourly'],$row['minly'],$row['bank_account'],$row['bank_name'],$row['payment_type'],$row['employee_type'],$row['file_status'],$row['allowance'],$row['union_name'],$row['mag_card'],$row['date_department'],$row['city'],$row['wtax'],$row['pdm'],$row['blood_type'],$row['salary_date_change'],$row['old_salary'],$row['new_salary'],$row['half_13th'],$row['last_13th'],$row['bonus']));
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=employe.xls');
header("Location: employee.xls");
exit;

?>