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
if (isset($_GET['owner'])) {
        $owner = $_GET['owner'];
} else {
        $owner = 'all';
}
if (isset($_GET['jobid'])) {
        $jobid = $_GET['jobid'];
} else {
        $jobid = '';
}
if (isset($_GET['jobstat'])) {
        $jobstat = $_GET['jobstat'];
} else {
        $jobstat = '';
}
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
$token = null;
$token = tempnam(sys_get_temp_dir(), 'PHPQstat-');

if($jobstat){$jobstatflag="-s $jobstat";}else{$jobstatflag="";}
$out = exec("./gexml -j $jobid $jobstatflag -u all -o $token");

$qstat = simplexml_load_file("$token");

//foreach ($qstat->xpath('detailed_job_info->djob_info->element') as $element) {
//foreach ($qstat->element[0] as $element) {
$job_name=$qstat->djob_info->element[0]->JB_job_name;
$job_owner=$qstat->djob_info->element[0]->JB_owner;
$job_group=$qstat->djob_info->element[0]->JB_group;
$job_pe=$qstat->djob_info->element[0]->JB_pe;
$job_ust=$qstat->djob_info->element[0]->JB_submission_time;
if ($UGE == "yes") {
	$job_qn=$qstat->djob_info->element[0]->JB_hard_queue_list->element->QR_name;
	$job_st=date(r,(int) substr($job_ust,0,-3));
	if (isset($qstat->djob_info->element[0]->JB_ja_tasks->element->JAT_start_time)) {
		// job running (or suspended)
		$job_rust=$qstat->djob_info->element[0]->JB_ja_tasks->element->JAT_start_time;
		$job_rst=date('r',(int) substr($job_rust,0,-3));
		$jobstateflag='r';
	} else {
		// job not running (assume pending)
		$job_rst='';
		$jobstateflag='qw';
	}
} else {
	$job_qn=$qstat->djob_info->element[0]->JB_hard_queue_list->destin_ident_list->QR_name;
	$job_st=date(r,(int) $job_ust);
	if (isset($qstat->djob_info->element[0]->JB_ja_tasks->ulong_sublist->JAT_start_time)) {
		// job running (or suspended)
		$job_rust=$qstat->djob_info->element[0]->JB_ja_tasks->ulong_sublist->JAT_start_time;
		$job_rst=date(r,(int) $job_rust);
		$jobstateflag='r';
	} else {
		// job not running (assume pending)
		$job_rst='';
		$jobstateflag='qw';
	}
}
$job_slots=$qstat->djob_info->element[0]->JB_pe_range->ranges->RN_min;
if (!$job_slots) {
	// not in a pe environment, assume one slot
	$job_slots=1;
}

if ($job_pe) {
	if ($UGE == "yes") {
		$job_qn='';
		foreach ($qstat->xpath('//JAT_granted_destin_identifier_list/element') as $queue_element) {
			$cn_slots=$queue_element->JG_slots;
			while ($cn_slots > 0) {
				$job_qn=$job_qn . "<br/>" . $queue_element->JG_qname;
				$cn_slots=$cn_slots - 1;
			}
		}
		$job_qn = substr($job_qn, 5);
	}
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

if ($jobstateflag == 'r') {
        // Only display if job is running
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
			<td>".number_format($usage_stats[1]+0, 2, '.', '')."</td>
			<td>".number_format($usage_stats[2]+0, 2, '.', '')."</td>
			<td>".number_format($usage_stats[3]+0, 2, '.', '')."</td>
			<td>".number_format($usage_stats[4]/1024/1024, 2, '.', '')."</td>
			<td>".number_format($usage_stats[5]/1024/1024, 2, '.', '')."</td>
		      </tr>
		   </tbody>
		</table><br>";
}

unlink($token);
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

