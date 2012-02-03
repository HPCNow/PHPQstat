<html>
  <head>
    <title>IQTC Accounting for group $group</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares "> 
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
  <meta name="KEYWORDS" content="gridengine sge sun hpc supercomputing batch queue linux xml qstat qhost jordi blasco solnu">
  <meta http-equiv="refresh" content="180">
  <link rel="stylesheet" href="phpqstat.css" type="text/css" /> 
  </head>
  <body>
<?php
$group=$_GET['group']; 
$view=$_GET['view']; 
?>
<body><table align=center width=95% border="1" cellpadding="0" cellspacing="0"><tbody><tr><td><h1> IQTC Accounting</h1></td></tr> 
      <tr><td CLASS="bottom" align=center><a href='qacct_grup.php?view=hourly&group=<?php echo $group; ?>'>Hourly</a> * <a href='qacct_grup.php?view=daily&group=<?php echo $group; ?>'>Daily</a> *  <a href='qacct_grup.php?view=weekly&group=<?php echo $group; ?>'>Weekly</a> *  <a href='qacct_grup.php?view=monthly&group=<?php echo $group; ?>'>Monthly</a> * <a href='qacct_grup.php?view=yearly&group=<?php echo $group; ?>'>Yearly</a></td></tr> 
    <tr> 
      <td> 
<br> 
<center> 
<table align=center width=60% border="0" cellpadding="0" cellspacing="0"> 
<tr><td align=center> 
<?php echo "<img src=\"$view.$group.png\">" ?>
<br>

El valor Share acaba representant un % de la màquina i es calcula a partir de la següent equació:<br>
<br><img src="eq.png"><br><br>

<!-- a href="http://www.codecogs.com/eqnedit.php?latex=Share(group) = \frac{1}{Share_{total}}$\displaystyle\sum\limits_{cluster} N_{cores}(cluster)*UsageScaling(cluster)" target="_blank"><img src="http://latex.codecogs.com/png.latex?Share(group) = \frac{1}{Share_{total}}$\displaystyle\sum\limits_{cluster} N_{cores}(cluster)*UsageScaling(cluster)" title="Share(group) = \frac{1}{Share_{total}}$\displaystyle\sum\limits_{cluster} N_{cores}(cluster)*UsageScaling(cluster)" /></a>
<!Share(group) = \frac{\sum_{cluster} N_{cores}(cluster)*Usage_Scaling(cluster)}{Share_{total}}> -->

El gestor de cues gridengine calcula el <a href=http://en.wikipedia.org/wiki/CPU_time target=cputime>CPUTIME</a> consumit en funció del UsageScaling de cada clúster, és a dir, les hores de càlcul en el clúster IQTC04 equivalen a 2,1 vegades les del CERQT2 (amd).<br><br>

En verd apareix una línia que representa el número de cores total multiplicat pel % de Share del grup, aquest número de cores no és el que realment disposa aquest grup, ja que en aquest cas es consideren a tots els processadors iguals, pero ens ajuda a tenir una idea del % de màquina que li correspon al grup i ens ajuda a calcular el límit de processadors als que un grup pot optar.<br><br>

Cal teniu en compte que els valors que revisa el gestor de cues per calcular la prioritat és el CPUTIME acumulat i no pas els cores ocupats. El consum acumulat es pot consultar en el següent <a href="http://portal.qt.ub.es:8080/sge/qacct.html" target=qacct >link</a> (actualitzat 1 cop per setmana).<br><br>

</td></tr> 
</table> 
</center> 
<br> 
 
      </td> 
    </tr> 
<tr> 
      <td CLASS="bottom"> 
	<b>Version : 0.1 (April 2011)</b><br> 
        <a href="http://phpqstat.sourceforge.net">http://phpqstat.sourceforge.net</a><br> 
      </td> 
    </tr>  </tbody> 
</table> 

</body>
</html>
