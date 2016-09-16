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

function fusion_set_input_noms_prenoms(){
    noms_str = "";
    $.each($(".fusion-noms").children(".nom"), function(index, value){
        value = $(value);
        if(noms_str.length > 0)
            noms_str += ", ";
        if(value.children(".nom-attribut").length > 0)
            noms_str += "(" + value.children(".nom-attribut").text() + ") ";
        noms_str += value.children(".nom-nom").text();
    });
    $("#fusion-form input[name='noms']").val(noms_str);

    prenoms_str = "";
    $.each($(".fusion-prenoms").children(".prenom"), function(index, value){
        value = $(value);
        if(prenoms_str.length > 0)
            prenoms_str += ", ";
        prenoms_str += value.text();
    });
    $("#fusion-form input[name='prenoms']").val(prenoms_str);
}

function dissocier_set_input_noms_prenoms(){
    noms_str = "";
    $.each($(".dissocier-noms").children(".nom"), function(index, value){
        value = $(value);
        if(noms_str.length > 0)
            noms_str += ", ";
        if(value.children(".nom-attribut").length > 0)
            noms_str += "(" + value.children(".nom-attribut").text() + ") ";
        noms_str += value.children(".nom-nom").text();
    });
    $("#dissocier-form input[name='noms-A']").val(noms_str);
    $("#dissocier-form input[name='noms-B']").val(noms_str);

    prenoms_str = "";
    $.each($(".dissocier-prenoms").children(".prenom"), function(index, value){
        value = $(value);
        if(prenoms_str.length > 0)
            prenoms_str += ", ";
        prenoms_str += value.text();
    });
    $("#dissocier-form input[name='prenoms-A']").val(prenoms_str);
    $("#dissocier-form input[name='prenoms-B']").val(prenoms_str);
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

        $("#fusion-form").append(
            $("<input class='"+pers+"' type='hidden' name='id-"+pers+"' value='"+id+"'>")
        );

        $(".fusion-ids").append(
            $("<div class='"+pers+"'>").append(
                $("<input type='radio' name='id' id='pers-"+id+"' value='"+id+"' "+input_id_checked+">"),
                $("<label for='pers-"+id+"'>"+id+"</label>")
            )
        );

        fusion_set_input_noms_prenoms();
    });
}

function fusion_rm_personne(id){
    var $input = $("#pers-"+id);
    if($input == null)
        return;

    var pers = ($input.parent().hasClass("personne-A"))? "personne-A" : "personne-B";
    $("."+pers).remove();

    fusion_set_input_noms_prenoms();
}

function dissocier_form_info($where, info, id){
    var $container = $("<div class='flex-horizontal'>");
    var $info = $(info);
    var name = $info.attr("id");

    var $acte_refs = $info.find(".acte-ref");
    if($acte_refs.length > 1){
        $.each($acte_refs.toArray(), function(index, value){
            var $value = $(value);
            var id_acte = $value.html();
            var $radios = $("<div class='dissocier-radios'>");
            $radios.append(
                $("<div>Acte "+id_acte+"</div>"),
                $("<div><input type='radio' id='"+name+"-"+id_acte+"-A' name='"+name+"-"+id_acte+"' value='a' checked><label for='"+name+"-"+id_acte+"-A'>"+id+"</label></div>"),
                $("<div><input type='radio' id='"+name+"-"+id_acte+"-B' name='"+name+"-"+id_acte+"' value='b'><label for='"+name+"-"+id_acte+"-B'>Nouveau</label></div>"),
                $("<div><input type='radio' id='"+name+"-"+id_acte+"-2' name='"+name+"-"+id_acte+"' value='2'><label for='"+name+"-"+id_acte+"-2'>Les 2</label></div>")

            );
            $container.append($radios);
        });
        $info.find(".list-acte").remove();
        $container.append($info);
        $where.append($container);
    }else{
        var $radios = $("<div class='dissocier-radios'>");
        $radios.append(
            $("<div><input type='radio' id='"+name+"-A' name='"+name+"' value='a' checked><label for='"+name+"-A'>"+id+"</label></div>"),
            $("<div><input type='radio' id='"+name+"-B' name='"+name+"' value='b'><label for='"+name+"-B'>Nouveau</label></div>"),
            $("<div><input type='radio' id='"+name+"-2' name='"+name+"' value='2'><label for='"+name+"-2'>Les 2</label></div>")
        );

        $container.append(
            $radios,
            $info
        );
        $where.append($container);
    }
}

function dissocier_add_personne(id){
    $.get("get?s=personne_infos&id="+id, function(data, status){
        var $data = $("<div>"+data+"</div>");
        _.map($data.children(".alert").toArray(), alert_add);

        $("#dissocier-form").append(
            $("<input type='hidden' name='id' value='"+id+"'>")
        );

        $(".dissocier-ids").append(
            $("<div>Personne d'origine: "+id+"</div>"),
            $("<div>Nouvelle personne : automatiquement généré</div>")
        );

        $(".dissocier-noms").append(
            $data.children(".nom")
        );

        $(".dissocier-prenoms").append(
            $data.children(".prenom")
        );

        $.each($data.children(".condition").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-conditions"), value, id)
        });

        $.each($data.children(".relation").toArray(), function(index, value){
            dissocier_form_info($(".dissocier-relations"), value, id)
        });

        dissocier_set_input_noms_prenoms();
    });
}

function dissocier_rm_personne(id){
    var $root = $("#dissocier-form");

    $root.find(".dissocier-ids").children().remove();
    $root.find(".nom").remove();
    $root.find(".prenom").remove();
    $root.find(".condition").parent().remove();
    $root.find(".relation").parent().remove();

    dissocier_set_input_noms_prenoms();
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
                $("#dissocier-submit").removeAttr("disabled");
                that.$selectableUl.children().addClass("disabled");
            }
            dissocier_add_personne(values[0]);
        },
        afterDeselect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length < 1){
                $("#dissocier-submit").attr("disabled", "");
                that.$selectableUl.children().removeClass("disabled");
            }
            dissocier_rm_personne(values[0]);
        }
    });
    get_list_personne($("#dissocier_personne_list"));

    /* FUSION */
    $("#fusion-submit").click(function(){
        var id_A = $("#fusion-form input[name='id-personne-A']").attr("value");
        var id_B = $("#fusion-form input[name='id-personne-B']").attr("value");
        var id_select = $("#fusion-form input[name='id']").attr("value");
        var noms = $("#fusion-form input[name='noms']").val();
        var prenoms = $("#fusion-form input[name='prenoms']").val();

        noms = noms.replace(" ", "+");
        prenoms = prenoms.replace(" ", "+");

        var url = "get?s=fusion_exec&id-personne-A="+id_A
            +"&id-personne-B="+id_B
            +"&id="+id_select
            +"&noms="+noms
            +"&prenoms="+prenoms;

        $.get(url, function(data, status){
            $data = $("<div>"+data+"</div>");
            _.map($data.children(".alert").toArray(), alert_add);
            $("#fusion-form input[name='noms']").val("");
            $("#fusion-form input[name='prenoms']").val("");
            fusion_rm_personne(id_A);
            fusion_rm_personne(id_B);
            get_list_personne($("#fusion_personne_list"));
        });
    });


    /* DISSOCIER */
    $("#dissocier-submit").click(function(){
        $("#dissocier-form").submit();
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
    $(".import-form .import-submit").click(function(){
        alert_add("<div class='alert alert-info fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>L'importation peut prendre du temps, veuillez patienter</div>");
        $(".import-form .import-submit").text("Importation en cours ...");
        $(".import-form .import-submit").attr("disabled", "");
        $(this).parent().parent().submit();
    });
})
