<?php



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

        return $html;
    }

    function html_users(){
        global $mysqli;
        $html = "";

        $results = $mysqli->select("utilisateurs", ["*"]);
        if($results != FALSE && $results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                $html .= html_user($row);
            }
        }

        return "
            <table class='table'>
                <thead>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>E-Mail</th>
                    <th>Date d'inscription</th>
                    <th>Rang</th>
                </thead>
                <tbody>
                    $html
                </tbody>
            </table>
        ";
    }

?>
<section>
    <h4>Utilisateurs</h4>
    <div>
        <?php echo html_users(); ?>
    </div>
</section>
