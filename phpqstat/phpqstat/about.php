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
<center>
<table align=center width=50% border="0" cellpadding="0" cellspacing="0">
<tr><td align=center>
<b>PHPQstat</b> is a web interface that allows to connect to the usefull commands of the Sun Grid Engine (SGE) batch queue system. With this interface, you can control your job status and your queues health at real time.<br><br>

	<b>Version : 0.1 (February 2010)</b><br><br>

        <a href=\"http://phpqstat.sourceforge.net\">http://phpqstat.sourceforge.net</a><br>
</td></tr>
</table>
</center>
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

