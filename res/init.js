
function get_personne_infos(id){
    $.get("get?s=fusion_personne_infos&id="+id, function(data, status){
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

function rm_personne_infos(id){
    var $input = $("#pers-"+id);
    if($input == null)
        return;

    var pers = ($input.parent().hasClass("personne-A"))? "personne-A" : "personne-B";
    $("."+pers).remove();
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
            if(that.$selectionUl.children(".ms-selected").length >= 2)
                that.$selectableUl.children().addClass("disabled");
            get_personne_infos(values[0]);
        },
        afterDeselect: function(values){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length < 2)
                that.$selectableUl.children().removeClass("disabled");
            rm_personne_infos(values[0]);
        }
    });


    /* TOOLTIPS */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });


    /* ALERTS */
    $(".alert").fadeOut(5000, function(){
        $(this).hide();
        $(this).remove();
    });



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
})
