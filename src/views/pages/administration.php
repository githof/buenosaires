<?php



    function edit_rang($user_id, $rang){
        global $mysqli, $alert, $account;
        $previous_rang = 0;

        $results = $mysqli->select("utilisateurs", ["rang"], "id='$user_id'");
        if($results != FALSE && $results->num_rows > 0){
            $previous_rang = $results->fetch_assoc()["rang"];
        }else{
            $alert->warning("Utilisateur introuvable");
            return;
        }

        if($previous_rang == 3){
            $alert->warning("Il n'est pas autorisé de modifier le rang d'un administrateur");
            return;
        }

        $mysqli->update("utilisateurs", ["rang" => $rang], "id='$user_id'");
        $alert->success("Edition du rang avec succès");
    }

    function html_editer_rang($row){
        global $account;
        $html = "";

        if($row["rang"] == 3)
            return "";

        $disabled = "";
        if($row["rang"] == 1)
            $disabled = "disabled";
        $html .= "
            <a href='administration?user={$row["id"]}&rang=1'>
                <button class='btn btn-info btn-sm' $disabled>Lecteur</button>
            </a>";

        $disabled = "";
        if($row["rang"] == 2)
            $disabled = "disabled";
        $html .= "
            <a href='administration?user={$row["id"]}&rang=2'>
                <button class='btn btn-info btn-sm' $disabled>Editeur</button>
            </a>";

        $disabled = "";
        if($row["rang"] == 3)
            $disabled = "disabled";
        $html .= "
            <a href='administration?user={$row["id"]}&rang=3'>
                <button class='btn btn-info btn-sm' $disabled>Admin</button>
            </a>";

        return $html;
    }

    function html_user($row){
        global $level_access_name;
        $html = "";

        if(isset($row["nom"]))
            $html .= "<td>{$row["nom"]}</td>";
        else
            $html .= "<td></td>";

        if(isset($row["prenom"]))
            $html .= "<td>{$row["prenom"]}</td>";
        else
            $html .= "<td></td>";

        if(isset($row["email"]))
            $html .= "<td>{$row["email"]}</td>";
        else
            $html .= "<td></td>";

        if(isset($row["date_inscr"]))
            $html .= "<td>{$row["date_inscr"]}</td>";
        else
            $html .= "<td></td>";

        if(isset($row["rang"])){
            $html .= "<td>".$level_access_name[$row["rang"]]."</td>";
        }else
            $html .= "<td></td>";

        $html .= "<td>".html_editer_rang($row)."</td>";

        return $html;
    }

    function html_users(){
        global $mysqli;
        $html = "";

        $results = $mysqli->select("utilisateurs", ["*"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                $html .= "<tr>".html_user($row)."</tr>";
            }
        }

        return "
            <table class='table'>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>E-Mail</th>
                        <th>Date d'inscription</th>
                        <th>Rang</th>
                        <th>Editer rang</th>
                    </tr>
                </thead>
                <tbody>
                    $html
                </tbody>
            </table>
        ";
    }


    if(isset($ARGS["user"], $ARGS["rang"])){
        edit_rang($ARGS["user"], $ARGS["rang"]);
    }

?>
<section>
    <h4>Utilisateurs</h4>
    <div>
        <div class='help-block'>
            Il n'est pas possible de modifier le rang des administrateurs
        </div>
        <?php echo html_users(); ?>
    </div>
</section>
