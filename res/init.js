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

            console.log(values[0]);
            $.get("get/fusion_personne_infos?id="+values[0], function(data){
                $("#result").html(data);
                console.log("SUCCESS");
                console.log(data);
            });
        },
        afterDeselect: function(){
            var that = this;
            that.qs1.cache();
            if(that.$selectionUl.children(".ms-selected").length < 2)
                that.$selectableUl.children().removeClass("disabled");
        }
    });


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });


    $(".alert").fadeOut(5000, function(){
        $(this).hide();
        $(this).remove();
    });
})
