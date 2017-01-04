<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" type="text/css" href="jquery-ui.min.css"/>
  <link rel="stylesheet" type="text/css" href="datatables.min.css"/>
  <script type="text/javascript" src="datatables.min.js"></script>
  <script type="text/javascript" class="init">
    $(document).ready(function() {
        $('#jobtable').DataTable({
          "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ]
        });
        $('#pendingtable').DataTable({
          "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
	  "order": [[ 2, "desc" ]]
        });
    } );
  </script>


 
</head>

<?php
if (isset($_GET['owner'])) {
	$owner  = $_GET['owner'];
} else {
	$owner = 'all';
}
if (isset($_GET['jobstat'])) {
        $jobstat = $_GET['jobstat'];
} else {
        $jobstat = '';
}
if (isset($_GET['queue'])) {
        $queue = $_GET['queue'];
} else {
        $queue = '';
}
echo "<body><table align=center width=100% border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
include("header.php");

if ($qstat_reduce != "yes" ) {
	$token = null;
	$token = tempnam(sys_get_temp_dir(), 'PHPQstat-');
}

function show_run($qstat,$owner,$queue) {
  global $UGE;
  echo "<table id=\"jobtable\" class=\"display\" align=center cellspacing=\"0\" width=\"100%\">
	  <thead>
		  <tr>
		  <th>JobID</th>
		  <th>Owner</th>
		  <th>Priority</th>
		  <th>Name</th>
		  <th>State</th>
		  <th>Project</th>
		  <th>Queue</th>
		  <th>Start Time</th>
		  <th>PE</th>
		  <th>Slots</th>
		  </tr></thead>
	  <tfoot>
		  <tr>
		  <th>JobID</th>
		  <th>Owner</th>
		  <th>Priority</th>
		  <th>Name</th>
		  <th>State</th>
		  <th>Project</th>
		  <th>Queue</th>
		  <th>Start Time</th>
		  <th>PE</th>
		  <th>Slots</th>
		  </tr></tfoot><tbody>";


  $pe_job_num='';
  $pe_queue='';
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

	  if ($pe_job_num) {
		if ("$job_list->JB_job_number" == "$pe_job_num") {
			$pe_queue=$pe_queue . "<br/><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>" . $job_list->queue_name . "</a>";
			continue;
		} else {
			$pe_queue = substr($pe_queue, 5);
			echo "    <tr>
                                  <td><a href=qstat_job.php?jobid=$pe_job_num&owner=$owner>$pe_job_num</a></td>
                                  <td><a href=qstat_user.php?owner=$pe_owner>$pe_owner</a></td>
                                  <td>$pe_prio</td>
                                  <td>$pe_job_name</td>
                                  <td>$pe_state</td>
                                  <td>$pe_project</td>
                                  <td>$pe_queue</a></td>
                                  <td>$pe_start</td>
                                  <td>$pe</td>
                                  <td>$slots</td>
                                  </tr>";
			$pe_job_num='';
			$pe_queue='';
		}
	  }
	  $pe=$job_list->requested_pe['name'];
	  $job_num=$job_list->JB_job_number;
	  $JAT_start=str_replace('T', ' ', $job_list->JAT_start_time);
	  if (isset($job_list->granted_pe)) {
		// parallel job
		$pe_job_num=$job_list->JB_job_number;
		$slots=$job_list->granted_pe;
		$pe_owner=$job_list->JB_owner;
		$pe_prio=$job_list->JAT_prio;
		$pe_job_name=$job_list->JB_name;
		$pe_state=$job_list->state;
		$pe_project=$job_list->JB_project;
		$pe_start=$JAT_start;
		if ($UGE == "yes" && $job_list->master == "MASTER") {
			continue;
		}
		$pe_queue="<br/><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>" . $job_list->queue_name . "</a>";
		continue;
	  } else {
		$slots=$job_list->slots;
	  }
	  echo "          <tr>
			  <td><a href=qstat_job.php?jobid=$job_list->JB_job_number&owner=$owner>$job_list->JB_job_number</a></td>
			  <td><a href=qstat_user.php?owner=$job_list->JB_owner>$job_list->JB_owner</a></td>
			  <td>$job_list->JAT_prio</td>
			  <td>$job_list->JB_name</td>
			  <td>$job_list->state</td>
			  <td>$job_list->JB_project</td>
			  <td><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>$job_list->queue_name</a></td>
			  <td>$JAT_start</td>
			  <td>$pe</td>
			  <td>$slots</td>
			  </tr>";
  }
  if ($pe_job_num) {
	$pe_queue = substr($pe_queue, 5);
	echo "    <tr>
                  <td><a href=qstat_job.php?jobid=$pe_job_num&owner=$owner>$pe_job_num</a></td>
                  <td><a href=qstat_user.php?owner=$pe_owner>$pe_owner</a></td>
                  <td>$pe_prio</td>
                  <td>$pe_job_name</td>
                  <td>$pe_state</td>
                  <td>$pe_project</td>
                  <td>$pe_queue</a></td>
                  <td>$pe_start</td>
                  <td>$pe</td>
                  <td>$slots</td>
                  </tr>";
	$pe_job_num='';
	$pe_queue='';
  }
  echo "</tbody></table><br><br>";

}

function show_pend($qstat,$owner,$queue) {
  echo "<table id=\"pendingtable\" class=\"display\" align=center cellspacing=\"0\" width=\"100%\">
	  <thead>
		  <tr>
		  <th>JobID</th>
		  <th>Owner</th>
		  <th>Priority</th>
		  <th>Name</th>
		  <th>State</th>
		  <th>Project </th>
		  <th>Queue </th>
		  <th>Submission Time</th>
		  <th>PE</th>
		  <th>Slots</th>
		  </tr></thead>
	  <tfoot>
		  <tr>
		  <th>JobID</th>
		  <th>Owner</th>
		  <th>Priority</th>
		  <th>Name</th>
		  <th>State</th>
		  <th>Project </th>
		  <th>Queue </th>
		  <th>Submission Time</th>
		  <th>PE</th>
		  <th>Slots</th>
		  </tr></tfoot><tbody>";

  foreach ($qstat->xpath('//job_list') as $job_list) {
          if ($job_list->state != 'qw' && $job_list->state != 'Eqw') {
	    continue;
	  }
	  if ($owner != "all" && $job_list->JB_owner != $owner) {
	    continue;
	  }
	  if ($queue != "" && $job_list->queue_name != $queue) {
	    continue;
	  }
	  $pe=$job_list->requested_pe['name'];
	  if (isset($job_list->hard_req_queue)) {
		$queue_req=$job_list->hard_req_queue;
	  } else {
		$queue_req='';
	  }
	  $job_num=$job_list->JB_job_number;
	  $JB_submission=str_replace('T', ' ', $job_list->JB_submission_time);
	  if ($job_list->state == 'Eqw') {
		$state = "<font color=red>" . $job_list->state . "</font>";
	  } else {
		$state = $job_list->state;
	  }
	  echo "          <tr>
			  <td><a href=qstat_job.php?jobid=$job_list->JB_job_number&owner=$owner>$job_list->JB_job_number</a></td>
			  <td><a href=qstat_user.php?owner=$job_list->JB_owner>$job_list->JB_owner</a></td>
			  <td>$job_list->JAT_prio</td>
			  <td>$job_list->JB_name</td>
			  <td>$job_list->state</td>
			  <td>$job_list->JB_project</td>
			  <td><a href=qstat_user.php?queue=$job_list->queue_name&owner=$owner>$job_list->queue_name</a></td>
			  <td>$JB_submission</td>
			  <td>$pe</td>
			  <td>$job_list->slots</td>
			  </tr>";
  }
  echo "</tbody></table><br>";

}


echo "<tr><td align=center>
<a class='ui-button ui-widget ui-corner-all' href=\"index.php\">Home</a> 
<a class='ui-button ui-widget ui-corner-all' href=\"qhost.php?owner=$owner\">Hosts status</a>
<a class='ui-button ui-widget ui-corner-all' href=\"qstat.php?owner=$owner\">Queue status</a>
<a class='ui-button ui-widget ui-corner-all' href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a>
<a class='ui-button ui-widget ui-corner-all' href=\"about.php?owner=$owner\">About PHPQstat</a>
</td></tr>";

if($queue){$queueflag="-q $queue";}else{$queueflag="";}

if($jobstat){$jobstatflag="-s $jobstat";}else{$jobstatflag="";}

if ($qstat_reduce == "yes" ) {
	$qstat = simplexml_load_file("/tmp/qstat_all.xml");
}

switch ($jobstat) {
    // All this stuff can probably go away
    case "r":
        $jobstatflag="-s r";
	if ($qstat_reduce != "yes" ) {
        	$out = exec("./gexml -u $owner $jobstatflag $queueflag -o $token");   
        	show_run("",$owner,$queue);
		unlink($token); 
	} else {
        	show_run($qstat,$owner,$queue);
	}
        break;
    case "p":
        $jobstatflag="-s p";
        if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o $token");
	        show_pend("",$owner,$queue);
		unlink($token); 
	} else {
        	show_pend($qstat,$owner,$queue);
	}
        break;
    default:
/*
        $jobstatflag="-s r";
	if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o $token");
	        show_run("",$owner,$queue);
		unlink($token); 
	} else {
	        show_run($qstat,$owner,$queue);
	}

        $jobstatflag="-s p";
	if ($qstat_reduce != "yes" ) {
	        $out = exec("./gexml -u $owner $jobstatflag $queueflag -o $token");
	        show_pend("",$owner,$queue);
		unlink($token); 
	} else {
        	show_pend($qstat,$owner,$queue);
	}
*/
	if ($qstat_reduce != "yes" ) {
		$out = exec("./gexml -u $owner $queueflag -o $token");
		$qstat = simplexml_load_file("$token");
		show_run($qstat,$owner,$queue);
		show_pend($qstat,$owner,$queue);
		unlink($token);
	} else {
		show_run($qstat,$owner,$queue);
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
