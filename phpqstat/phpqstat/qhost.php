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
echo "<body><table align=center width=95% border=\"1\" cellpadding=\"0\" cellspacing=\"0\"><tbody>";
echo "<tr><td><h1>PHPQstat</h1></td></tr>
      <tr><td CLASS=\"bottom\" align=center><a href=\"qstat.php?owner=$owner\">Home</a> *  <a href=\"qhost.php?owner=$owner\">Hosts status</a> *  <a href=\"qstat?owner=$owner\">Queue status</a> * <a href=\"qstat_user.php?owner=$owner\">Jobs status ($owner)</a> * <a href=\"about.php?owner=$owner\">About PHPQstat</a></td></tr>";
?>
    <tr>
      <td>
<br>


	<table align=center width=95% border="1" cellpadding="0" cellspacing="0">
        <tbody>
		<tr CLASS="header">
		<td>Hostname</td>
                <td>Architecture</td>
                <td>NCPU</td>
                <td>Load avg</td>
                <td>mem_total</td>
                <td>mem_used</td>
                <td>swap_total</td>
                <td>swap_used</td>
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

$out = exec("./qhostout /tmp/$token.xml");

//printf("System Output: $out\n"); 
$qhost = simplexml_load_file("/tmp/$token.xml");
$i=0;
foreach ($qhost->host as $host) {
	echo "<tr>";
	$hostname=$host['name'];
	echo "          <td>$hostname</td>";
	foreach ($qhost->host[$i] as $hostvalue) {
		echo "          <td>$hostvalue</td>";
	}
	echo "</tr>";
	$i++;
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

