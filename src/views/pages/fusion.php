<?php

    include_once(ROOT."src/html_entities.php");


    function has_same_id($array, $id){
        foreach($array as $obj){
            if($obj->id == $id)
                return TRUE;
        }
        return FALSE;
    }

    function has_condition($condition, $personne){
        foreach($personne->conditions as $c){
            if($c->text == $condition->text)
                return $c;
        }
        return FALSE;
    }

    function find_acte($id, $actes){
        foreach($actes as $acte){
            if($acte->id == $id)
                return TRUE;
        }
        return FALSE;
    }

    function dispatch_actes($field, $id, $keep_actes)
    // $field = 'condition' ou 'relation'
    // $id = id d'une condition ou d'une relation
    {
      global $mysqli;

      $dispatch_actes = array(
        'delete' => [],
        'update' => []
      );

      $result = $mysqli->select("acte_has_$field",
                               ["acte_id"],
                                "$field"."_id = '$id'");

      if($result && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $acte_id = $row["acte_id"];
            if(find_acte($acte_id, $keep_actes))
                $dispatch_actes['delete'][] = $acte_id;
            else
                $dispatch_actes['update'][] = $acte_id;
        }
      }
      return $dispatch_actes;
    }

    function traite_dispatch_actes($field, $operation, $dispatch_actes,
                                   $throw_id, $keep_id = null)
    // $field = 'condition' ou 'relation'
    // $operation = 'delete' ou 'update'
    {
      global $mysqli;

      if(count($dispatch_actes[$operation]) == 0)
        return;

      $str = implode(', ', $dispatch_actes[$operation]);
      $req = "$field"."_id = '$throw_id' AND acte_id IN ($str)";
      if($operation = 'delete')
        $mysqli->delete("acte_has_$field", $req);
      else
        $mysqli->update("acte_has_$field",
                       ["$field"."_id" => "$keep_id"],
                       $req);
    }

    function fusion_condition_ou_relation($which, $throw, $keep)
    // $which = 'condition' ou 'relation'
    {
      global $mysqli;

      $dispatch_actes = dispatch_actes($which,
                                              $throw->id,
                                              $keep->actes);
      traite_dispatch_actes($which, 'delete',
                                   $dispatch_actes, $throw->id);
      traite_dispatch_actes($which, 'update',
                                   $dispatch_actes,
                                   $throw->id, $keep->id);
      $mysqli->delete($which, "id = '$throw->id'");
    }

    function fusion_condition($throw, $keep)
    {
      fusion_condition_ou_relation('condition', $throw, $keep);
    }

    function fusion_conditions($personne_throw, $personne_keep){
        global $mysqli, $log;

        $log->d("fusion conditions");
        foreach($personne_throw->conditions as $condition_throw)
        {
            $condition_keep = has_condition($condition_throw,
                                             $personne_keep);
            if($condition_keep)
              fusion_condition($condition_throw, $condition_keep);
            else
              $mysqli->update("condition",
                             ["personne_id" => "$personne_keep->id"],
                             "id = '$condition_throw->id'");
        }
    }

    function same_half_relation($which_half, $r1, $r2)
    {
      $personne = "personne_$which_half";
      return $r1->{$personne}->id
          == $r2->{$personne}->id;
    }

    function has_relation($relation, $personne, $is_source)
    /*
      J'ai l'impression que dans la version précédente, le test était pas correct, on testait qu'une des deux extrémités,
      du coup il a pu y avoir des faux positifs, qui ont pu entraîner des suppressions ou des updates intempestifs
    */
    {
      foreach($personne->relations as $r){

        if($r->statut_id != $relation->statut_id)
          continue;

        if($is_source)
        {
          if($r->personne_source->id == $personne->id
             && same_half_relation('destination', $r, $relation))
            return $r;
        }
        else
        {
          if($r->personne_destination->id == $personne->id
             && same_half_relation('source', $r, $relation))
            return $r;
        }

        return FALSE;
      }
    }

    function fusion_relation($throw, $keep)
    {
      fusion_condition_ou_relation('relation', $throw, $keep);
    }

    function fusion_relations($personne_throw, $personne_keep){
      global $mysqli, $log;

      $log->d("fusion relations");
      foreach($personne_throw->relations as $relation_throw){
        $is_source = $relation_throw->check_source_id($personne_throw->id);

        $relation_keep = has_relation($relation_throw,
                                      $personne_keep,
                                      $is_source);
        if($relation_keep)
          fusion_relation($relation_throw, $relation_keep);
        else
        {
          if($is_source)
              $pers = "pers_source_id";
          else
              $pers = "pers_destination_id";

          $mysqli->update("relation",
                         [$pers => "$personne_keep->id"],
                         "id = '$relation_throw->id'");
        }
      }
    }

    function fusion_actes($throw, $keep)
    {
      global $mysqli;

      foreach (['epoux', 'epouse'] as $ep)
        $mysqli->update("acte",
                       [$ep => "$keep->id"],
                        "$ep='$throw->id'");
    }

    function fusion_update_contenu_acte($personne_id_old, $personne_id_new){
        global $mysqli;

        $personne = new Personne($personne_id_old);
        $mysqli->from_db($personne);

        $actes = [];

        /*
          Dans ce qui suit,
          Je pense pas qu'il y ait de raison de traiter séparément
          epoux/se et le reste,
          et surtout de faire une requête alors qu'on a tout dans
          les conditions et relations
        */

        $results = $mysqli->select(
            "acte",
            ["id"],
            "epoux='$personne_id_old' OR epouse='$personne_id_old'"
        );
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc())
                $actes[] = $row["id"];
        }

        foreach($personne->conditions as $condition){
            foreach($condition->actes as $acte)
                $actes[] = $acte->id;
        }

        foreach($personne->relations as $relation){
            foreach($relation->actes as $acte)
                $actes[] = $acte->id;
        }

        $actes = array_unique($actes);

        foreach($actes as $acte){
            $results = $mysqli->select(
                "acte_contenu",
                ["contenu"],
                "acte_id='$acte'"
            );
            $contenu = $results->fetch_assoc()["contenu"];
            $xml = new SimpleXMLElement($contenu);

	    /* Ouch, d'où c'est exhaustif ?
	       Faudrait pas mettre ça ailleurs, un peu plus paramétrable ?
	       Voire, faire un parcours systématique plutôt ? */
            $paths = [
                "epoux", "epouse", "epoux/pere", "epoux/mere", "epouse/pere",
                "epouse/mere", "temoins/temoin", "temoins/temoin/pere",
                "temoins/temoin/mere"
            ];

            foreach($paths as $path){
                $results = $xml->xpath($path);
                while(list( , $node) = each($results)){
                    $attr = $node->attributes();
                    if($attr["id"] == $personne_id_old)
                        $attr["id"] = $personne_id_new;
                }
            }
            $contenu = $xml->asXML();

            $mysqli->update(
                "acte_contenu",
                ["contenu" => $contenu],
                "acte_id='$acte'"
            );
        }
    }

    function change_prenoms_ou_noms($field, $noms, $personne)
    // $field = 'prenom' ou 'nom'
    {
      global $mysqli;

      if(count($noms) == 0) return;

      $cond = "personne_id='$personne->id'";
      $mysqli->delete($field."_personne", $cond);
      $i = 1;
      foreach($noms as $nom){
          $mysqli->into_db($nom);
          $into_db = 'into_db_'.$field.'_personne';
          $mysqli->{$into_db}($personne, $nom, $i);
          $i++;
      }
    }

function fusion_tables($personne_throw, $personne_keep)
{
  foreach (['conditions', 'relations', 'actes'] as $element) {
    $f = "fusion_$element";
    $f($personne_throw, $personne_keep);
  }
}

function renomme_personne($personne, $noms, $prenoms)
{
  foreach(['prenom', 'nom'] as $field)
  {
    $liste = "$field".'s';
    change_prenoms_ou_noms($field, ${$liste}, $personne);
  }
}

function recense_actes($personne)
{
  $actes = [];
  foreach(['conditions', 'relations'] as $field)
    $liste = $personne->{$field};
    foreach($liste as $element)
      foreach($element->actes as $acte)
        $actes[] = $acte;

  return array_unique_by_id($actes);
}

$balises_personnes = ['epoux', 'epouse', 'pere', 'mere',
  'temoin', 'parrain', 'veuf-de', 'veuf', 'veuve-de', 'veuve'];
/*
  Ça ça devrait être défini en global, a minima dans XMLActeReader.php,
  mais peut-être même dans un truc plus général.
  Et mis en cohérence avec la dtd et avec ce qui est utilisé dans la saisie
  en js (pas réussi à retrouver où c'est défini pour le js).
  Pour l'instant
  - je laisse ça là parce que j'ai peur de la dépendance
    mais dès que c'est testé, je le déplace
    (sinon ce sera super chiant à débusquer si je change un truc dans les
    defs xml)
  - je mets toutes les combinaisons pour veuf/ve mais faudra se fixer
    (pour l'instant les veuf-de etc n'ont pas d'id il me semble)
*/

function change_id_personne_xml($xml, $old_id, $new_id)
{
  global $balises_personnes;

  foreach($xml->children() as $node)
  {
    if(in_array($node->getName(), $balises_personnes))
      if(isset($node['id']) && $node['id'] == $old_id)
      {
        $node['id'] = $new_id;
      }
    change_id_personne_xml($node, $old_id, $new_id);
  }
}

function xml_without_header($xmltext)
{
  return explode("\n", $xmltext, 2)[1];
}

function change_id_personne_contenu($acte, $old_id, $new_id)
{
  global $mysqli;

  $contenu = $acte->get_contenu();
  $xml = new SimpleXMLElement($contenu);
  change_id_personne_xml($xml, $old_id, $new_id);
  $new_contenu = xml_without_header($xml->asXML());
  $mysqli->update(
      "acte_contenu",
      ["contenu" => $new_contenu],
      "acte_id='$acte->id'"
  );
}

function change_id_personne_contenus($personne, $new_id)
// nouvelle version de fusion_update_contenu_acte (plus haut)
/*
  Peut-être que pour dissoc on a besoin exactement de la même fonction,
  auquel cas il faudrait la mettre qq part genre utils.php:
    // code...
    break;
*/
{
  $actes = recense_actes($personne);
  foreach($actes as $acte)
  {
    change_id_personne_contenu($acte, $personne->id, $new_id);
  }
}

/*__ FUSION __ */
/*
BUG : la fusion ne se fait que sur les actes où la personne est époux/se
MAIS le prénom et le nom sont virés même quand la fusion n'est pas faite !!
Ce que je ne comprends pas encore c'est pourquoi l'id n'est pas modifié sur les relations et les conditions dans ce cas, parce que les appels sont quand même faits

 */
    function fusion($personne_throw, $personne_keep, $noms, $prenoms)
    // Voir l'ancienne version, bugged_fusion, juste après
    {
      global $mysqli;

      $mysqli->start_transaction();
      fusion_tables($personne_throw, $personne_keep);
      change_id_personne_contenus($personne_throw, $personne_keep->id);
      $personne_throw->remove_from_db(TRUE);
      renomme_personne($personne_keep, $noms, $prenoms);
      $mysqli->end_transaction();
    }

    function bugged_fusion($personne_keep, $personne_throw, $noms, $prenoms){
        global $mysqli, $log;

	/* Déjà faudrait ptet attendre d'être sûrs que les trucs soient créés
	   avant de supprimer */
        $mysqli->delete("prenom_personne", "personne_id='$personne_keep->id' OR personne_id='$personne_throw->id'");
        $i = 1;
        foreach($prenoms as $prenom){
            $mysqli->into_db($prenom);
            $mysqli->into_db_prenom_personne($personne_keep, $prenom, $i);
            $i++;
        }

        $mysqli->delete("nom_personne", "personne_id='$personne_keep->id' OR personne_id='$personne_throw->id'");
        $i = 1;
        foreach($noms as $nom){
            $mysqli->into_db($nom);
            $mysqli->into_db_nom_personne($personne_keep, $nom, $i);
            $i++;
        }

	/* là-dedans on ne s'occupe que des actes où la personne est époux/se */
  /* (juil 2020 : non, on les traite séparément, mais y'a pas trop de raison) */
        fusion_update_contenu_acte($personne_throw->id, $personne_keep->id);

        $log->d("fusion actes"); // pourquoi ici ??

	/* idem, du coup */
        $mysqli->update("acte", ["epoux" => "$personne_keep->id"], "epoux='$personne_throw->id'");
        $mysqli->update("acte", ["epouse" => "$personne_keep->id"], "epouse='$personne_throw->id'");

        fusion_conditions($personne_keep, $personne_throw);
        fusion_relations($personne_keep, $personne_throw);

        $log->d("fusion remove personne");
        $mysqli->delete_personne($personne_throw->id);
    }


/*__ SELCTION PERSONNES __ */

    function html_select_personnes(){
        return "
            <section class='max-2'>
                <h4>Choisir deux personnes à fusionner</h4>
                <div>
                    <input type='text' name='autocomplete' placeholder='Recherche parmis les personnes'>
                    <span class='autocomplete-search'>recherche en cours ...</span>
                </div>
                <div id='auto-complete-results'>
                </div>
                <form id='form-fusion-select-personnes' method='get'>
                    <div></div>
                    <input type='submit' value='Prévisualisez la fusion'>
                </form>
            </section>
        ";
    }

/*__ PREVIEW_FUSION __ */
/*
  Formulaire pour prévisualiser la fusion
  (fonction html_preview_fusion tout en bas)
*/

    function html_fusion_debut(){
      return '
      <form method="get" id="fusion-form">
          <button class="btn btn-primary" id="fusion-submit">Fusionner</button>
      ';
    }
    function html_fusion_fin(){
      return "
      </form>
      ";
    }

    function html_fusion_section($titre, $classe,
                                 $flex_orientation,
                                 $contenu,
                                 $input_suite = "", $help = ""){
        if($input_suite != "")
          $div_suite = "
          <div>
              <div class=\"help-block\">$help</div>
              $input_suite
          </div>
          ";
        else $div_suite = "";

        return "
        <section>
            <h4>$titre</h4>
            <div class=\"fusion-$classe flex-$flex_orientation\">
                $contenu
            </div> $div_suite
        </section>
        ";
    }

    function html_fusion_radio_id($AB, $id){
        return "
            <div>
                <input type='radio' name='id' id='radio-pers-$AB' value='$id' checked='checked'>
                <label for='radio-pers-$AB'>$id</label>
                <input type='hidden' name='personne-$AB' value='$id'>
            </div>
        ";
        // je comprends pas à quoi sert le hidden ici
    }

    function html_fusion_section_keep($id_A, $id_B){
      $radioA = html_fusion_radio_id('A', $id_A);
      $radioB = html_fusion_radio_id('B', $id_B);

      return html_fusion_section(
          'ID  <i>(Choisir l\'ID à conserver)</i>',
          'ids', 'horizontal',
          "$radioA $radioB");
    }

    function html_fusion_div_prenoms($prenoms){
        $html = "";
        foreach($prenoms as $prenom){
            $html .=
                "<div id='prenom-$prenom->id' class='prenom'>"
                .$prenom->to_string().
                "</div>";
        }
        return $html;
    }

    function html_fusion_section_prenoms($prenomsA, $prenomsB)
    {
      $html_prenomsA = html_fusion_div_prenoms($prenomsA);
      $html_prenomsB = html_fusion_div_prenoms($prenomsB);
      $input_prenoms = default_input_prenoms($prenomsA, $prenomsB);
      $input_suite = "
              <input type=\"text\" name=\"prenoms\" placeholder=\"Ex: Maria, Josefa\" value=\"$input_prenoms\">
      ";

      return html_fusion_section(
          'Prénoms', 'prenoms', 'horizontal',
          "$html_prenomsA $html_prenomsB",
          $input_suite,
          'Les prénoms séparés par une virgule'
        );
    }

    function html_fusion_div_noms($noms){
        $html = "";
        foreach($noms as $nom){
            $attr = "";
            if(isset($nom->attribut))
                $attr = "<div class='nom-attribut'>$nom->attribut</div>";
            $html .= "
                <div id='nom-$nom->id' class='nom'>
                    $attr
                    <div class='nom-nom'>$nom->nom</div>
                </div>";
        }
        return $html;
    }

    function html_fusion_section_noms($nomsA, $nomsB)
    {
      $html_nomsA = html_fusion_div_noms($nomsA);
      $html_nomsB = html_fusion_div_noms($nomsB);
      $input_noms = default_input_noms($nomsA, $nomsB);
      $input_suite = "
              <input type=\"text\" name=\"noms\" placeholder=\"Ex: PERI, (de) BELGRANO\" value=\"$input_noms\">
      ";

      return html_fusion_section(
          'Noms', 'noms', 'horizontal',
          "$html_nomsA $html_nomsB",
          $input_suite,
          'Les noms séparés par une virgule et leurs attributs entre parenthèses si besoin'
      );
    }

    function html_fusion_conditions($conditions){
        $html = "";
        foreach($conditions as $condition){
            $html .= html_condition($condition);
        }
        return $html;
    }

    function html_fusion_relations($relations){
        $html = "";
        foreach($relations as $relation){
            $html .= html_relation($relation);
        }
        return $html;
    }

    // Une fois qu'on a sélectionné qui on fusionne, on arrive ici
    function html_preview_fusion($pA, $pB)
    {
        echo html_fusion_debut();

        echo html_fusion_section_keep($pA->id, $pB->id);
        echo html_fusion_section_prenoms($pA->prenoms, $pB->prenoms);
        echo html_fusion_section_noms($pA->noms, $pB->noms);

        $html_conditions = html_fusion_conditions($pA->conditions)
            . html_fusion_conditions($pB->conditions);
        echo html_fusion_section(
          'Conditions', 'conditions', 'vertical',
          $html_conditions);

        $html_relations = html_fusion_relations($pA->relations)
              . html_fusion_relations($pB->relations);
        echo html_fusion_section(
          'Relations', 'relations', 'vertical',
          $html_relations);

        echo html_fusion_fin();
    }

/*__ MAIN __ */

    if(isset($ARGS["personne-A"],
            $ARGS["personne-B"],
            $ARGS["id"],
            $ARGS["noms"],
            $ARGS["prenoms"])){
        $personne_A = new Personne($ARGS["personne-A"]);
        $personne_B = new Personne($ARGS["personne-B"]);

        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        $noms = parse_noms($ARGS["noms"]);
        $prenoms = parse_prenoms($ARGS["prenoms"]);

/* L'appel à fusion est ici */
        $log->d("fusion possible");
        if($ARGS["id"] == $personne_A->id || $ARGS["id"] == $personne_B->id){
            if($ARGS["id"] == $personne_A->id)
              fusion($personne_B, $personne_A, $noms, $prenoms);
            else
              fusion($personne_A, $personne_B, $noms, $prenoms);
            $mysqli->remove_unused_prenoms_noms();
            $alert->success("Succès de la fusion");
        }else{
            $alert->warning("Erreur dans les ID des personnes");
        }
    }else if(isset($ARGS["personne-A"], $ARGS["personne-B"])){
        $personne_A = new Personne($ARGS["personne-A"]);
        $personne_B = new Personne($ARGS["personne-B"]);
        $mysqli->from_db($personne_A);
        $mysqli->from_db($personne_B);

        echo html_preview_fusion($personne_A, $personne_B);
    }else{
        echo html_select_personnes();
    }

?>
