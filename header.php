<?php
include('config.inc.php');

if ($qstat_reduce == "yes") {

	if (!file_exists("/tmp/load.xml")) {
		exec("./qinfo.sh");
	}
	$loadcheck = simplexml_load_file("/tmp/load.xml");
	$lastepoch = strtotime($loadcheck->last) + ($cache_time * 60);
	if ($lastepoch < time() ) {
		exec("./qinfo.sh");
		$loadcheck = simplexml_load_file("/tmp/load.xml");
	}
	if ($loadcheck->load == "Not Available") {
		
		echo "<tr><td align=center><b><font color=red>Unable to get load from master server, check snmpd server. </font></b>";
	} else {
		echo "<tr><td align=center>";
	}
	if ($loadcheck->check == "yes") {
		echo "<b><font color=red>Refresh waiting due to high load. Last refresh: $loadcheck->last - headnode 5 minute load average: $loadcheck->load</font></b></td></tr>";
	} else {
		echo "Last refresh: $loadcheck->last</td></tr>";
	}

}
echo "<tr><td><img src=img/logo.png align=left height=80 width=80></td></tr>";

?>
