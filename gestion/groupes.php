<?php
/*
 *FONCTION A ECRIR PLUSTARD 
 */
function options_groupes(){
}
/*
 * Code input select
 */
function input_select_groupe(){
	echo '<label for="groupe">Dans quelle categorie souhaitez-vous les enegistrer ? ?</label><br />';
       echo '<select name="groupe" id="groupe" class="liste">';
           echo '<option value="en attente" selected="selected"> EN ATTENTE</option>';     
       echo '</select><br /><br />';
			
}
?>
