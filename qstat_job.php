<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" >
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" type="text/css" href="jquery-ui.min.css"/>
  <link rel="stylesheet" type="text/css" href="datatables.min.css"/>
  <script type="text/javascript" src="datatables.min.js"></script>
  <script type="text/javascript" class="init">
    $(document).ready(function() {
        $('#jobtable').DataTable({
          "paging": false,
          "info": false,
          "searching": false,
        });
        $('#jobinfo').DataTable({
          "paging": false,
          "info": false,
          "searching": false,
        });
    } );
  </script>

</head>

<?php
require('time_duration.php');
$owner  = $_GET['owner'];
$jobid  = $_GET['jobid'];
$jobstat  = $_GET['jobstat'];
echo "<body><table align=center width=100% border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
include("header.php");
echo "<tr><td align=center>
<a class='ui-button ui-widget ui-corner-all' href=\"index.php\">Home</a>
<a class='ui-button ui-widget ui-corner-all' href=\"qhost.php?owner=$owner\">Hosts status</a>
<a class='ui-button ui-widget ui-corner-all' href=\"qstat.php?owner=$owner\">Queue status</a>
<a class='ui-button ui-widget ui-corner-all' href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a>
<a class='ui-button ui-widget ui-corner-all' href=\"about.php?owner=$owner\">About PHPQstat</a>
</td></tr>";
?>
      <td>
<br>




<?php
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
$job_st=date(r,(int) $job_ust);
//$job_st=date(r,(int) substr($job_ust,0,-3)); UGE specific
$job_rust=$qstat->djob_info->element[0]->JB_ja_tasks->ulong_sublist->JAT_start_time;
if ($job_rust) {
	$job_rst=date(r,(int) $job_rust);
}
$job_qn=$qstat->djob_info->element[0]->JB_hard_queue_list->destin_ident_list->QR_name;
//$job_qn=$qstat->djob_info->element[0]->JB_hard_queue_list->element->QR_name; UGE specific
$job_pe=$qstat->djob_info->element[0]->JB_pe;
$job_slots=$qstat->djob_info->element[0]->JB_pe_range->ranges->RN_min;

if (!$job_slots) { //SGE only?
	// not in a pe environment, assume one slot
	$job_slots=1;
}

echo "	<table id=\"jobtable\" class=\"display\" align=left cellspacing=\"0\" width=\"100%\">
        <thead>
		<tr>
		<th>JobID</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Group</th>
                <th>Submit Time</th>
                <th>Start Time</th>
                <th>Queue</th>
                <th>PE</th>
                <th>Slots</th>
                </tr>
        </thead>
           <tbody>
                <tr>
                <td>$jobid</td>
                <td>$job_name</td>
                <td>$job_owner</td>
                <td>$job_group</td>
                <td>$job_st</td>
                <td>$job_rst</td>
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
echo "	<table id=\"jobinfo\" class=\"display\" align=center width=100% cellspacing=\"0\">
        <thead>
		<tr>
		<th>CPUTime (s)</th>
                <th>Mem (GB)</th>
                <th>io</th>
                <th>iow</th>
                <th>VMem (M)</th>
                <th>MaxVMem (M)</th>
                </tr>
        </thead>
           <tbody>
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

