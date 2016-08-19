<?php

    include_once(ROOT."src/html_entities.php");

    function all_personnes(){
        global $mysqli;
        $str = "";

        $result = $mysqli->select("personne", ["*"]);
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $personne = new Personne($row["id"]);
                $mysqli->from_db($personne);
                $html = "";

                $html .= " [$personne->id]";
                foreach($personne->prenoms as $prenom)
                    $html .= " $prenom->prenom";

                foreach($personne->noms as $nom)
                    $html .= " " . $nom->to_String();

                $str .= "<option value='$personne->id'>$html</option>";
            }
        }
        return $str;
    }

?>

<section>
    <h4>Choisir deux personnes à fusionner</h4>
    <div>
        <select multiple="multiple" id="fusion_personne_list">
            <?php echo all_personnes(); ?>
        </select>
    </div>
</section>
<form id="fusion-form">
    <section>
        <h4>ID (Choisir l'ID à conserver)</h4>
        <div class="fusion-ids flex-horizontal">
        </div>
    </section>
    <section>
        <h4>Noms</h4>
        <div class="fusion-noms flex-horizontal">
        </div>
    </section>
    <section>
        <h4>Prenoms</h4>
        <div class="fusion-prenoms flex-horizontal">
        </div>
    </section>
    <section>
        <h4>Condition</h4>
        <div class="fusion-conditions flex-vertical">
        </div>
    </section>
    <section>
        <h4>Relations</h4>
        <div class="fusion-relations flex-vertical">
        </div>
    </section>
</form>
