<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" href="phpqstat.css" type="text/css" /> 

 
</head>

<?php
$owner  = $_GET['owner'];
$jobstat  = $_GET['jobstat'];
$queue  = $_GET['queue'];
echo "<body><table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
include("header.php");


if ($qstat_reduce != "yes" ) {

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
}

function show_run($qstat,$owner,$queue) {
  global $qstat_reduce;
  global $token;
  echo "<table align=center width=95%xml border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
	  <tbody>
		  <tr>
		  <td CLASS=\"bottom\" width=120><b>Running Jobs</b></td></tr>
		  <tr>
		  <td>JobID</td>
		  <td>Owner</td>
		  <td>Priority</td>
		  <td>Name</td>
		  <td>State</td>
		  <td>Project </td>
		  <td>Queue </td>
		  <td>Start Time</td>
		  <td>PE</td>
		  <td>Slots</td>
		  </tr>";
  
  if ($qstat_reduce != "yes" ) {
  	$qstat = simplexml_load_file("/tmp/$token.xml");
  }
  foreach ($qstat->xpath('//job_list') as $job_list) {
	  if ($job_list->state != 'r') {
	    continue;
	  }
	  if ($owner != "all" && $job_list->JB_owner != $owner) {
	    continue;
	  }
	  if ($queue != "" && $job_list->queue_name != $queue) {
	    continue;
	  }
	  $pe=$job_list->requested_pe['name'];
	  echo "          <tr>
			  <td><a href=qstat_job.php?jobid=$job_list->JB_job_number&owner=$owner>$job_list->JB_job_number</a></td>
			  <td><a href=qstat_user.php?owner=$job_list->JB_owner>$job_list->JB_owner</a></td>
			  <td>$job_list->JAT_prio</td>
			  <td>$job_list->JB_name</td>
			  <td>$job_list->state</td>
			  <td>$job_list->JB_project</td>
			  <td><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>$job_list->queue_name</a></td>
			  <td>$job_list->JAT_start_time</td>
			  <td>$pe</td>
			  <td>$job_list->slots</td>
			  </tr>";
  }
  echo "</tbody></table><br><br>";

}

function show_pend($qstat,$owner,$queue) {
  global $qstat_reduce;
  global $token;
  echo "<table align=center width=95%xml border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
	  <tbody>
		  <tr>
		  <td CLASS=\"bottom\" width=120><b>Pending Jobs</b></td></tr>
		  <tr>
		  <td>JobID</td>
		  <td>Owner</td>
		  <td>Priority</td>
		  <td>Name</td>
		  <td>State</td>
		  <td>Project </td>
		  <td>Queue </td>
		  <td>Submission Time</td>
		  <td>PE</td>
		  <td>Slots</td>
		  </tr>";
  if ($qstat_reduce != "yes" ) {
  	$qstat = simplexml_load_file("/tmp/$token.xml");
  }
  foreach ($qstat->xpath('//job_list') as $job_list) {
	  if ($job_list->state != 'qw') {
	    continue;
	  }
	  if ($owner != "all" && $job_list->JB_owner != $owner) {
	    continue;
	  }
	  if ($queue != "" && $job_list->queue_name != $queue) {
	    continue;
	  }
	  $pe=$job_list->requested_pe['name'];
	  echo "          <tr>
			  <td><a href=qstat_job.php?jobid=$job_list->JB_job_number&owner=$owner>$job_list->JB_job_number</a></td>
			  <td><a href=qstat_user.php?owner=$job_list->JB_owner>$job_list->JB_owner</a></td>
			  <td>$job_list->JAT_prio</td>
			  <td>$job_list->JB_name</td>
			  <td>$job_list->state</td>
			  <td>$job_list->JB_project</td>
			  <td><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>$job_list->queue_name</a></td>
			  <td>$job_list->JB_submission_time</td>
			  <td>$pe</td>
			  <td>$job_list->slots</td>
			  </tr>";
  }
  echo "</tbody></table><br>";

}


echo "<tr><td><h1>PHPQstat</h1></td></tr>
      <tr><td CLASS=\"bottom\" align=center><a href='index.php'>Home</a> *  <a href=\"qhost.php?owner=$owner\">Hosts status</a> *  <a href=\"qstat.php?owner=$owner\">Queue status</a> * <a href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a> * <a href=\"about.php?owner=$owner\">About PHPQstat</a></td></tr><tr><td><br>";

if($queue){$queueflag="-q $queue";}else{$queueflag="";}

if($jobstat){$jobstatflag="-s $jobstat";}else{$jobstatflag="";}

if ($qstat_reduce == "yes" ) {
	$qstat = simplexml_load_file("/tmp/qstat_all.xml");
}

switch ($jobstat) {
    case "r":
        $jobstatflag="-s r";
	if ($qstat_reduce != "yes" ) {
        	$out = exec("./gexml -u $owner $jobstatflag $queueflag -o /tmp/$token.xml");   
        	show_run("",$owner,$queue);
		exec("rm /tmp/$token.xml");
	} else {
        	show_run($qstat,$owner,$queue);
	}
        break;
    case "p":
        $jobstatflag="-s p";
        if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o /tmp/$token.xml");
	        show_pend("",$owner,$queue);
		exec("rm /tmp/$token.xml");
	} else {
        	show_pend($qstat,$owner,$queue);
	}
        break;
    default:
        $jobstatflag="-s r";
	if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o /tmp/$token.xml");
	        show_run("",$owner,$queue);
		exec("rm /tmp/$token.xml");
	} else {
	        show_run($qstat,$owner,$queue);
	}

        $jobstatflag="-s p";
	if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o /tmp/$token.xml");
	        show_pend("",$owner,$queue);
		exec("rm /tmp/$token.xml");
	} else {
        	show_pend($qstat,$owner,$queue);
	}
        break;
}

?>
	  

      </td>
    </tr>
<?php
include("bottom.php");
?>
  </tbody>
</table>



</body>
</html>

