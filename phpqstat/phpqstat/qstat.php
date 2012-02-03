<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" href="phpqstat.css" type="text/css" /> 
</head>
<body>
<script type="text/javascript">
function changeIt(rta){document.images.example.src='img/'rta'.png'}
</script>

<?php
$owner  = $_GET['owner'];
echo "<body><table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
echo "<tr><td><h1>PHPQstat</h1></td></tr>
      <tr><td CLASS=\"bottom\" align=center><a href='index.php'>Home</a> *  <a href=\"qhost.php?owner=$owner\">Hosts status</a> *  <a href=\"qstat?owner=$owner\">Queue status</a> * <a href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a> * <a href=\"about.php?owner=$owner\">About PHPQstat</a></td></tr>";
?>
    <tr>
      <td>
<br>

	<table align=center width=95% border="1" cellpadding="0" cellspacing="0">
        <tbody>
		<tr CLASS="header">
		<td>Queue</td>
                <td>Load</td>
                <td>Used</td>
                <td>Resv</td>
                <td>Available</td>
                <td>Total</td>
                <td>Temp. disabled</td>
                <td>Manual intervention</td>
                </tr>

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

$out = exec("./gexml -u all -R -o /tmp/$token.xml");

//printf("System Output: $out\n"); 
$qstat = simplexml_load_file("/tmp/$token.xml");

//$qstat = simplexml_load_file("/home/xadmin/phpqstat/qstat_user.xml");

foreach ($qstat->xpath('//cluster_queue_summary') as $cluster_queue_summary) {
echo "                <tr>
                <td><a href=qstat_user.php?owner=$owner&queue=$cluster_queue_summary->name>$cluster_queue_summary->name</a></td>
                <td>$cluster_queue_summary->load</td>
                <td>$cluster_queue_summary->used</td>
                <td>$cluster_queue_summary->resv</td>
                <td>$cluster_queue_summary->available</td>
                <td>$cluster_queue_summary->total</td>
                <td>$cluster_queue_summary->temp_disabled</td>
                <td>$cluster_queue_summary->manual_intervention</td>
                </tr>";
}
exec("rm /tmp/$token.xml");

echo "                </tbody>
	</table>

<br>
	<table align=center width=95% border='1' cellpadding='0' cellspacing='0'>
        <tbody>
		<tr CLASS='header'>
		<td>Jobs status</td>
                <td>Total</td>
                <td>Slots</td>
                </tr>

";

$out2 = exec("./gexml -u all -o /tmp/$token.xml");
$jobs = simplexml_load_file("/tmp/$token.xml");
$nrun=0;
$srun=0;
$npen=0;
$spen=0;
$nzom=0;
$szom=0;
foreach ($jobs->xpath('//job_list') as $job_list) {
$jobstatus=$job_list['state'];

	if ($jobstatus == "running"){
		$nrun++;
		$srun=$srun+$job_list->slots;
	}
	elseif ($jobstatus == "pending"){
		$npen++;
		$spen=$spen+$job_list->slots;
	}
	elseif ($jobstatus == "zombie"){
		$nzom++;
		$szom=$szom+$job_list->slots;
	}
}
echo "          <tr>
                <td><a href=qstat_user.php?jobstat=r&owner=$owner>running</a></td>
                <td>$nrun</td>
                <td>$srun</td>
                </tr>
                <tr>
                <td><a href=qstat_user.php?jobstat=p&owner=$owner>pending</a></td>
                <td>$npen</td>
                <td>$spen</td>
                </tr>
                <tr>
                <td><a href=qstat_user.php?jobstat=z&owner=$owner>zombie</a></td>
                <td>$nzom</td>
                <td>$szom</td>
                </tr>
";

//exec("rm /tmp/$token.xml");
?>

	  </tbody>
	</table>

<br>
<br>

	<table align=center border="1" cellpadding="0" cellspacing="0">
        <tbody>
		<tr CLASS="header"><td align=center>Real-time Accounting : 
		<a href="#" onclick="changeIt(hour)">hour</a> - 
		<a href="#" onclick="changeIt(day)">day</a> - 
		<a href="#" onclick="changeIt(week)">week</a> - 
		<a href="#" onclick="changeIt(month)">month</a> - 
		<a href="#" onclick="changeIt(year)">year</a></td></tr>
		<tr/><td><img src="img/hour.png" name="rta"></td></tr>
	</tbody>
	</table>

      </td>
    </tr>
<?php
include("bottom.php");
?>
  </tbody>
</table>



</body>
</html>

