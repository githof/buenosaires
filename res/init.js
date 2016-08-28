function alert_animation($alert){
    $alert.delay(5000).fadeOut(1000, function(){
        $(this).hide();
        $(this).remove();
    });
}

function alert_show(alert){
    $(alert).mouseenter(function(){
        $(this).stop(true, false);
        $(this).css("opacity", "1");
    });
    $(alert).mouseleave(function(){
        alert_animation($(this));
    });
    alert_animation($(alert));
}

function alert_add(alert){
    $("#alert-container").append($(alert));
    alert_show(alert);
}


function get_list_personne($select){
    if($select.length == 0)
        return;
    $select.html("");
    $.get("get?s=multiselect_list_personne", function(data, status){
        $select.append(data);
        $select.multiSelect("refresh");
    });
}

function fusion_add_personne(id){
    $.get("get?s=personne_infos&id="+id, function(data, status){
        var pers = "personne-A";
        var input_id_checked = "checked";

        var contains_A = $("#fusion-form").find(".personne-A").length > 0;
        var contains_B = $("#fusion-form").find(".personne-B").length > 0;

        if(contains_A && contains_B)
            return;
        else if(contains_A){
            pers = "personne-B";
            input_id_checked = "";
        }

        var $data = $("<div>"+data+"</div>");
        _.map($data.children(".alert").toArray(), alert_add);

        $("#fusion-form").append(
            $("<input class='"+pers+"' type='hidden' name='id-"+pers+"' value='"+id+"'>")
        );

        $(".fusion-ids").append(
            $("<div class='"+pers+"'>").append(
                $("<input type='radio' name='id' id='pers-"+id+"' value='"+id+"' "+input_id_checked+">"),
                $("<label for='pers-"+id+"'>"+id+"</label>")
            )
        );

        $(".fusion-noms").append(
            $data.children(".nom").addClass(pers)
        );

        $(".fusion-prenoms").append(
            $data.children(".prenom").addClass(pers)
        );

        $(".fusion-conditions").append(
            $data.children(".condition").addClass(pers)
        );

        $(".fusion-relations").append(
            $data.children(".relation").addClass(pers)
        );
    });
}

function fusion_rm_personne(id){
    var $input = $("#pers-"+id);
    if($input == null)
        return;

    var pers = ($input.parent().hasClass("personne-A"))? "personne-A" : "personne-B";
    $("."+pers).remove();
}

function dissocier_form_info($where, $info, name){
    var $p = $("<div>");
    $p.append(
        $("<input type='radio' name='"+name+"' value='a' checked>"),
        $("<input type='radio' name='"+name+"' value='b'>"),
        $info
    );
    $where.append($p);
}

function dissocier_add_personne(id){
    $.get("get?s=personne_infos&id="+id, function(data, status){
        var $data = $("<div>"+data+"</div>");
        _.map($data.children(".alert").toArray(), alert_add);

        $("#dissocier-form").append(
            $("<input type='hidden' name='id-source' value='"+id+"'>")
        );

        $(".dissocier-ids").append(
            $("<div class='personne-A'>"+id+"</div>")
        );

        $.each($data.children(".nom").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-noms"), value, "nom")
        });

        $.each($data.children(".prenom").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-prenoms"), value, "prenom")
        });

        $.each($data.children(".condition").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-conditions"), value, "condition")
        });

        $.each($data.children(".relation").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-relations"), value, "relation")
        });
    });
}


$(document).ready(function(){

    $("#acte_noms").multiSelect({
        selectableHeader: "<div class='help-block'>Noms de famille</div>",
        selectionHeader: "<div class='help-block'>Sélection</div>"
    });

    $("#personne_noms").multiSelect({
        selectableHeader: "<div class='help-block'>Noms de famille</div>",
        selectionHeader: "<div class='help-block'>Sélection</div>"
    });

    $("#personne_prenoms").multiSelect({
        selectableHeader: "<div class='help-block'>Prénoms</div>",
        selectionHeader: "<div class='help-block'>Sélection</div>"
    });


    /* MULTI SELECT FUSION  */
    $("#fusion_personne_list").multiSelect({
        selectionHeader: "<div class='help-block'>Sélection (max: 2)</div>",
        selectableHeader: "<input type='text' class='help-block' autocomplete='off' placeholder='Rechercher une personne'>",
        afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length >= 2){
                $("#fusion-submit").removeAttr("disabled");
                that.$selectableUl.children().addClass("disabled");
            }
            fusion_add_personne(values[0]);
        },
        afterDeselect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length < 2){
                $("#fusion-submit").attr("disabled", "");
                that.$selectableUl.children().removeClass("disabled");
            }
            fusion_rm_personne(values[0]);
        }
    });
    get_list_personne($("#fusion_personne_list"));

    /* MULTI SELECT DISSOCIER  */
    $("#dissocier_personne_list").multiSelect({
        selectionHeader: "<div class='help-block'>Sélection (max: 1)</div>",
        selectableHeader: "<input type='text' class='help-block' autocomplete='off' placeholder='Rechercher une personne'>",
        afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length >= 1){
                $("#fusion-submit").removeAttr("disabled");
                that.$selectableUl.children().addClass("disabled");
            }
            dissocier_add_personne(values[0]);
        },
        afterDeselect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length < 1){
                $("#fusion-submit").attr("disabled", "");
                that.$selectableUl.children().removeClass("disabled");
            }

        }
    });
    get_list_personne($("#dissocier_personne_list"));

    /* FUSION */
    $("#fusion-submit").click(function(){
        var id_A = $("#fusion-form input[name='id-personne-A']").attr("value");
        var id_B = $("#fusion-form input[name='id-personne-B']").attr("value");
        var id_select = $("#fusion-form input[name='id']").attr("value");
        var script = $("#fusion-form input[name='s']").val();

        $.get("get?s="+script+"&id-personne-A="+id_A+"&id-personne-B="+id_B+"&id="+id_select, function(data, status){
            $data = $("<div>"+data+"</div>");
            _.map($data.children(".alert").toArray(), alert_add);
            fusion_rm_personne(id_A);
            fusion_rm_personne(id_B);
            get_list_personne();
        });
    });


    /* TOOLTIPS */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });


    /* ALERTS */
    _.map($(".alert").toArray(), alert_show);


    /* ACTE SUPPR BUTTONS */
    $("#acte-suppr-2").hide();
    $("#acte-suppr-3").hide();
    $("#acte-suppr-4").hide();
    $("#acte-suppr-5").hide();

    $("#acte-suppr-1").click(function(){
        $("#acte-suppr-1").hide();
        $("#acte-suppr-2").show();
    });
    $("#acte-suppr-2").click(function(){
        $("#acte-suppr-2").hide();
        $("#acte-suppr-3").show();
    });
    $("#acte-suppr-3").click(function(){
        $("#acte-suppr-3").hide();
        $("#acte-suppr-4").show();
    });
    $("#acte-suppr-4").click(function(){
        $("#acte-suppr-4").hide();
        $("#acte-suppr-5").show();
    });


    /* IMPORT FORM */
    $(".import-form input[type='submit']").click(function(){
        alert_add("<div class='alert alert-info fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>L'importation peut prendre du temps, veuillez patienter</div>");
        $(".import-form input[type='submit']").val("Importation en cours ...");
        $(".import-form input[type='submit']").attr("disabled", "");
    });
})
