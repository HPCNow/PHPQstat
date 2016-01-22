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
	if ($loadcheck->check == "yes") {
		echo "<tr><td><b><font color=red>Refresh waiting due to high load. Last refresh: $loadcheck->last - 5 minute load average: $loadcheck->load</font></b></td></tr>";
	} else {
		echo "<tr><td>Last refresh: $loadcheck->last - 5 minute load average: $loadcheck->load</td></tr>";
	}

}

?>
