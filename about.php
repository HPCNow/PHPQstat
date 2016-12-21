<html>

<head>
  <title>PHPQstat</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <link rel="stylesheet" type="text/css" href="jquery-ui.min.css"/>
  <link rel="stylesheet" type="text/css" href="datatables.min.css"/> 
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
<center>
<table align=center width=50% border="0" cellpadding="0" cellspacing="0">
<tr><td align=center>
<b>PHPQstat</b> is a web interface that allows to connect to the usefull commands of the Grid Engine (GE) batch queue system. With this interface, you can monitor your job status and your queues health at real time.<br><br>
<b>AUTHORS</b> Originally written by Jordi Blasco Pallarès.<br>
UGE supprt and additional functionallity and added by Lydia Sevelt<br><br>
<b>LICENSE</b> This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (<a href=http://gnu.org/licenses/gpl.html target=gpl>GPLv3</a>).<br><br>
<b>Version : 0.2.5 (September 2016)</b><br>
<a href=https://github.com/LydiaSevelt/PHPQstat>https://github.com/LydiaSevelt/PHPQstat</a><br>
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

