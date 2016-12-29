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
  <script type="text/javascript" src="jquery-ui.min.js"></script>
  <script type="text/javascript" class="init">
    $(document).ready(function() {
        $('#queues').DataTable({
          "paging": false,
          "info": false,
          "searching": false,
        });
        $('#jobs').DataTable({
          "paging": false,
          "info": false,
          "searching": false,
        });
    } );
  </script>
  <script>
  $( function() {
    $( "#tabs-rta" ).tabs({
       active: 1
    });
  } );
  </script>

<script type="text/javascript">
  function changeIt(view){document.getElementById('rta').src= view;}
</script>
</head>
<body>

<?php
$owner  = $_GET['owner'];
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
    <tr>
      <td>
<br>

	<table id=queues class=display align=center width=100% border="0" cellpadding="0" cellspacing="0">
        <thead>
		<tr>
		<th>Queue</th>
                <th>Load</th>
                <th>Used</th>
                <th>Resv</th>
                <th>Available</th>
                <th>Total</th>
                <th>Temp. disabled</th>
                <th>Manual intervention</th>
                </tr></thead><tbody>

<?php
if ($qstat_reduce != "yes" ) {
	$token = null;
	$token = tempnam(sys_get_temp_dir(), 'PHPQstat-');
	$out = exec("./gexml -u all -R -o $token");

	//printf("System Output: $out\n"); 
	$qstat = simplexml_load_file("$token");

	//$qstat = simplexml_load_file("/home/xadmin/phpqstat/qstat_user.xml");
} else {
	$qstat = simplexml_load_file("/tmp/qstat_queues.xml");
}

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
if ($qstat_reduce != "yes" ) {
	unlink($token);
}

echo "                </tbody>
	</table>

<br>
	<table id=jobs class=display align=center width=100% border='0' cellpadding='0' cellspacing='0'>
        <thead>
		<tr>
		<th>Jobs status</th>
                <th>Total</th>
                <th>Slots</th>
                </tr></thead><tbody>

";

if ($qstat_reduce != "yes" ) {
	$out2 = exec("./gexml -u all -o $token");
	$jobs = simplexml_load_file("$token");
} else {
	$jobs = simplexml_load_file("/tmp/qstat_all.xml");
}
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
if ($qstat_reduce != "yes" ) {
	unlink($token);
}
?>
          </tbody>
        </table>
<br>
<?php
$mapping = array(
    'rta'       => '',
);

$descr = array(
    'rta'       => 'Running Jobs by queue + queue wait',
);

$times = array(
    'hour',
    'day',
    'week',
    'month',
    'year',
);

foreach (array_keys($mapping) as $key) {
    echo '<br>
        <table align=center border="0" cellpadding="0" cellspacing="0">
        <tbody><tr><td>';
    echo "<div id=\"tabs-$key\"><ul>\n";
    foreach ($times as $time) {
          echo "<li><a href=\"#tabs-$key-$time\">$time</a></li>\n";
    }
    echo "</ul>\n";
    foreach ($times as $time) {
        if ($mapping[$key] == 'rta') {
          echo "<div id=\"tabs-$key-$time\"><p><img src=\"img/$time.png\" border='0'></p></div>\n";
        } else {
          echo "<div id=\"tabs-$key-$time\"><p><img src=\"img/$mapping[$key]$time.png\" border='0'></p></div>\n";
        }
    }
    echo "    </div></td></tr>
    </tbody>
    </table>";
}


include("bottom.php");
?>
  </tbody>
</table>



</body>
</html>

