<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 1);
include("../info/parametre.php");
include("../includes/fonctions_tables.php");
include("../includes/fonctions_all.php");
change_title("Tables");
?>

<h1>VOIR LES TABLES</h1>

<div class="form_form">
    <form method="get" name="form_table" action="">
    	<select name="c">
        	<option value="" selected class="italique">Sélectionnez une table</option>
        	<option value="a" onClick="allerVers('viewTables.php?c=a');">&nbsp;&nbsp;Actes</option>
            <option value="p" onClick="allerVers('viewTables.php?c=p');">&nbsp;&nbsp;Personnes</option>
            <option value="r" onClick="allerVers('viewTables.php?c=r');">&nbsp;&nbsp;Relations</option>
            <option value="m" onClick="allerVers('viewTables.php?c=m');">&nbsp;&nbsp;Mentions</option>
            <option value="pe" onClick="allerVers('viewTables.php?c=pe');">&nbsp;&nbsp;P&eacute;riode</option>
            <option value="c" onClick="allerVers('viewTables.php?c=c');">&nbsp;&nbsp;Conditions</option>
            <option value="s" onClick="allerVers('viewTables.php?c=s');">&nbsp;&nbsp;Statuts</option>
            <option value="ac" onClick="allerVers('viewTables.php?c=ac');">&nbsp;&nbsp;Textes Actes</option>
        </select>
        <input type="submit" value="ok" />
    </form>
</div>

<div class="affiche_table">
<?php 
$tables = array( 'p' => 'personnes',
		 'a' => 'actes',
		 'r' => 'relations',
		 'm' => 'mentions',
		 'pe' => 'periodes',
		 'c' => 'cond',
		 's' => 'statuts',
		 'ac' => 'actes_contenu'
		 );

$titres = array();
foreach($tables as $c => $table)
  $titres[$c] = $table;

$titres['c'] = 'conditions';
$titres['ac'] = 'textes des actes';

if (isset($_GET['c'])
    && (isset($tables[($c = $_GET['c'])])))
{
  $table = $tables[$c];
  $res = mysql_query("SELECT * FROM $table");
  $titre = $titres[$c];
  affiche_table($res, $titre);
  change_title($titre);
  
  mysql_close();
}
?>
</div>

<?php include("../includes/footer.php"); ?>