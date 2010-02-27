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
echo "<tr><td><h1>PHPQstat</h1></td></tr>
      <tr><td CLASS=\"bottom\" align=center><a href='index.php'>Home</a> *  <a href=\"qhost.php?owner=$owner\">Hosts status</a> *  <a href=\"qstat?owner=$owner\">Queue status</a> * <a href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a> * <a href=\"about.php?owner=$owner\">About PHPQstat</a></td></tr>";
?>
    <tr>
      <td>
<br>
	<table align=center width=95% border="1" cellpadding="0" cellspacing="0">
        <tbody>
		<tr>
		<td>JobID</td>
                <td>Owner</td>
                <td>Priority</td>
                <td>Name</td>
                <td>State</td>
                <td>Queue </td>
                <td>Submission Time</td>
                <td>PE</td>
                <td>Slots</td>
                </tr>

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
if($queue){$queueflag="-q $queue";}else{$queueflag="";}

$out = exec("./gexml -u $owner $jobstatflag $queueflag -o /tmp/$token.xml");

$qstat = simplexml_load_file("/tmp/$token.xml");

foreach ($qstat->xpath('//job_list') as $job_list) {
$pe=$job_list->requested_pe['name'];
echo "          <tr>
                <td><a href=qstat_job.php?jobid=$job_list->JB_job_number&owner=$owner>$job_list->JB_job_number</a></td>
                <td><a href=qstat_user.php?owner=$job_list->JB_owner>$job_list->JB_owner</a></td>
                <td>$job_list->JAT_prio</td>
                <td>$job_list->JB_name</td>
                <td>$job_list->state</td>
                <td><a href=qstat.php?queue=$job_list->queue_name>$job_list->queue_name</a></td>
                <td>$job_list->JB_submission_time</td>
                <td>$pe</td>
                <td>$job_list->slots</td>
                </tr>";
}
exec("rm /tmp/$token.xml");
?>

	  </tbody>
	</table>

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

