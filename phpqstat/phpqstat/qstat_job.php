<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" href="phpqstat.css" type="text/css" /> 
</head>

<?php
require('time_duration.php');
$owner  = $_GET['owner'];
$jobid  = $_GET['jobid'];
$jobstat  = $_GET['jobstat'];
echo "<body><table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
echo "<tr><td><h1>PHPQstat</h1></td></tr>
      <tr><td CLASS=\"bottom\" align=center><a href='index.php'>Home</a> *  <a href=\"qhost.php?owner=$owner\">Hosts status</a> *  <a href=\"qstat?owner=$owner\">Queue status</a> * <a href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a> * <a href=\"about.php?owner=$owner\">About PHPQstat</a></td></tr>";
?>
      <td>
<br>




<?
$password_length = 20;

function make_seed() {
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}

srand(make_seed());

$alfa = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
$token = "";
for($i = 0; $i < $password_length; $i ++) {
  $token .= $alfa[rand(0, strlen($alfa))];
}
if($jobstat){$jobstatflag="-s $jobstat";}else{$jobstatflag="";}
$out = exec("./gexml -j $jobid $jobstatflag -u all -o /tmp/$token.xml");

$qstat = simplexml_load_file("/tmp/$token.xml");

//foreach ($qstat->xpath('detailed_job_info->djob_info->element') as $element) {
//foreach ($qstat->element[0] as $element) {
$job_name=$qstat->djob_info->element[0]->JB_job_name;
$job_owner=$qstat->djob_info->element[0]->JB_owner;
$job_group=$qstat->djob_info->element[0]->JB_group;
$job_ust=$qstat->djob_info->element[0]->JB_submission_time;
$job_st=date(r,number_format($job_ust, 0, '', ''));
$job_qn=$qstat->djob_info->element[0]->JB_hard_queue_list->destin_ident_list->QR_name;
$job_pe=$qstat->djob_info->element[0]->JB_pe;
$job_slots=$qstat->djob_info->element[0]->JB_pe_range->ranges->RN_min;

echo "	<table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
        <tbody>
		<tr CLASS=\"header\">
		<td>JobID</td>
                <td>Name</td>
                <td>Owner</td>
                <td>Group</td>
                <td>SubmitTime</td>
                <td>Queue</td>
                <td>PE</td>
                <td>Slots</td>
                </tr>
                <tr>
                <td>$jobid</td>
                <td>$job_name</td>
                <td>$job_owner</td>
                <td>$job_group</td>
                <td>$job_st</td>
                <td>$job_qn</td>
                <td>$job_pe</td>
                <td>$job_slots</td>
              </tr>	  
           </tbody>
	</table><br>";


$i=0;
foreach ($qstat->xpath('//scaled') as $usage) {
$usage_stats[$i++]=$usage->UA_value;
}
if ($usage_stats[0] > 0){$cputime = time_duration($usage_stats[0], 'dhms');}else{$cputime = 0;}
echo "	<table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
        <tbody>
		<tr CLASS=\"header\">
		<td>CPUTime (s)</td>
                <td>Mem (GB)</td>
                <td>io</td>
                <td>iow</td>
                <td>VMem (M)</td>
                <td>MaxVMem (M)</td>
                </tr>
                <tr>
                <td>$cputime</td>
                <td>".number_format($usage_stats[1], 2, '.', '')."</td>
                <td>".number_format($usage_stats[2], 2, '.', '')."</td>
                <td>".number_format($usage_stats[3], 2, '.', '')."</td>
                <td>".number_format($usage_stats[4]/1024/1024, 2, '.', '')."</td>
                <td>".number_format($usage_stats[5]/1024/1024, 2, '.', '')."</td>
              </tr>	  
           </tbody>
	</table><br>";

exec("rm /tmp/$token.xml");
?>


<br>

      </td>
    </tr>
<?php
include("bottom.php");
?>
  </tbody>
</table>



</body>
</html>

