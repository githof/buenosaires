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


function setup_button_delete_acte(){
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
}


function add_personne_auto_complete_personne(id, html, $div_form, max){
    var pers = "A";
    if($("input[name='personne-A']").length > 0){
        if(max > 1)
            pers = "B";
        $(".btn-add-personne").attr("disabled", "");
    }else{
        if(max == 1)
            $(".btn-add-personne").attr("disabled", "");
    }

    var $input = $("<input type='hidden' name='personne-"+pers+"' value='"+id+"'>");
    var $div_html = $("<div>"+html+"</div>");
    var $button_remove = $("<button>");
    $button_remove.append("<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>");
    $button_remove.click(function(){
        var rmv = "A", opposite = "B";
        if($(this).parent().hasClass("personne-B")){
            rmv = "B";
            opposite = "A";
        }
        $(this).parent().remove();
        $("input[name='personne-"+rmv+"']").remove();

        if(rmv == "A"){
            $("input[name='personne-B']").attr("name", "personne-A");
            var tmp = $(".personne-B");
            tmp.removeClass("personne-B");
            tmp.addClass("personne-A");
        }

        $(".btn-add-personne").removeAttr("disabled");
    });
    $div_html.append($button_remove);

    $div_form.append(
        $input,
        $div_html
    );
}

function send_auto_complete_personne_query(findme, $div_results, $div_form, max){
    $(".autocomplete-search").show();
    $.get("get?s=auto_complete_personne&str="+findme, function(data, status){
        console.log(data);
        $div_results.html("");
        var $data = $("<div>"+data+"</div>");
        $.each($data.children().toArray(), function(index, value){
            var $value = $(value);
            var html = $value.html();
            var $button = $("<button class='btn-add-personne'>");
            $button.append("<span class='glyphicon glyphicon-plus' aria-hidden='true'></span>");
            if($("input[name='personne-B']").length > 0)
                $button.attr("disabled", "");
            $value.prepend($button);
            if($("input[name='personne-B']").length > 0)
                $button.disable();
            $button.click(function(){
                add_personne_auto_complete_personne(
                    $value.find(".personne-id").html(),
                    html,
                    $div_form,
                    max
                );
            });
            $div_results.append($value);
        });
        $(".autocomplete-search").hide();
    });
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


    /* TOOLTIPS */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });


    /* ALERTS */
    _.map($(".alert").toArray(), alert_show);


    /* ACTE SUPPR BUTTONS */
    setup_button_delete_acte();


    /* IMPORT FORM */
    $(".import-form .import-submit").click(function(){
        alert_add("<div class='alert alert-info fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>L'importation peut prendre du temps, veuillez patienter</div>");
        $(".import-form .import-submit").text("Importation en cours ...");
        $(".import-form .import-submit").attr("disabled", "");
        $(this).parent().parent().submit();
    });


    /* AUTO COMPLETE PERSONNE FUSION */
    $(".autocomplete-search").hide();
    $("input[name='autocomplete']").bind('input keyup', function(){
        var $this = $(this);
        var delay = 1000;
        var val = $this.val();

        var max = 1;
        if($this.parent().parent().hasClass("max-2"))
            max = 2;

        clearTimeout($this.data('timer'));
        $this.data('timer', setTimeout(function(){
            $this.removeData('timer');
            send_auto_complete_personne_query(
                val,
                $this.parent().parent().children("#auto-complete-results"),
                $this.parent().parent().children("form").children("div"),
                max
            );
        }, delay));
    });
})
