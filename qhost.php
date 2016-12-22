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
        $('#hosts').DataTable({
          "paging": false,
          "info": false,
          "searching": true,
        });
    } );
  </script>

</head>

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


	<table id=hosts class="display" align=center width=100% border="0" cellpadding="0" cellspacing="0">
        <thead>
		<tr>
		<th>Hostname</th>
                <th>Architecture</th>
                <th>NCPU</th>
                <th>Load Avgerage</th>
                <th>Memory Total</th>
                <th>Memory Used</th>
                <th>Swap Total</th>
                <th>Swap Used</th>
                </tr></thead><tbody>
<?php
if ($qstat_reduce != "yes") {

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

	$out = exec("./qhostout /tmp/$token.xml");

	//printf("System Output: $out\n"); 
	$qhost = simplexml_load_file("/tmp/$token.xml");
} else {
	$qhost = simplexml_load_file("/tmp/qhost.xml");
}

$i=0;
foreach ($qhost->host as $host) {
	$hostname=$host['name'];
	if ($hostname == "global") {
		$i++;
		continue;
	}
	echo "<tr>";
	echo "          <td>$hostname</td>";
	foreach ($qhost->host[$i] as $hostvalue) {
		echo "          <td>$hostvalue</td>";
	}
	echo "</tr>";
	$i++;
}

if ($qstat_reduce != "yes") {
	exec("rm /tmp/$token.xml");
}

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

