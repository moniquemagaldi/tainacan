<script type="text/javascript">
    /* Executed by script's start */
    $(function () {
    });
    //inicializa as abas
    function initiate_tabs(){
        var xhr =  $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                operation: 'get_tabs'}
        });
        // quando o ajax for finalizado        
        xhr.done(function (result) {
            hide_modal_main();
            var json = jQuery.parseJSON(result);
            if(json.array.length>0){
                var li = $('#tabs_item');
                var content = $('#tab-content-metadata');
                $.each(json.array,function(index,value){
                   if($('#metadata-container-'+value.meta_id).length==0){
                       li.append(get_li_model(value.meta_id,value.meta_value));
                      content.append(get_tab_content(value.meta_id));
                   } 
                   $('.tabs').tab();
                });
            }
        });
        //retorno apenas a promess
        return xhr;
    }
    //funcao que gera um li modelo para ser incluido
    function get_li_model(id,name){
        if(!name){
            name = '<?php _e('New tab', 'tainacan') ?>';
        }
        return '<li role="presentation">'+
                '<a id="click-tab-'+id+'" href="#tab-'+id+'" aria-controls="tab-'+id+'" role="tab" data-toggle="tab">'+
                    '<span class="tab-title" id="'+id+'-tab-title">'+name+'</span>'+
                '</a>'+
            '</li>';
    }
    // funcao que gera o conteudo da aba criada
    function get_tab_content(id){
        var html = get_expand_all_tab(id);
        return '<div style="background:white;margin-bottom: 15px;margin-top: 15px;" id="tab-'+id+'" class="tab-pane fade">'+
                html +
                '<div id="accordeon-'+id+'" class="multiple-items-accordion"></div>'+
                '</div>';
    }
    //funcao que gerar po expandir todos
    function get_expand_all_tab(id){
        return '<div style="margin-bottom:0%"  onclick="open_accordeon('+id+')" class="expand-all-item btn white tainacan-default-tags">'+
                    '<div class="action-text" style="display: inline-block;">'+
                             '<?php _e('Expand all', 'tainacan') ?></div>'+
                    '&nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>'+
                '</div>';
    }
    //funcao responsavel em listar as abas nos selects
    function list_tabs(){
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                operation: 'get_tabs'}
        }).done(function (result) {
            hide_modal_main();
            initiate_accordeon('default');
            var json = jQuery.parseJSON(result);
            if(json.array.length>0){
                $.each(json.array,function(index,value){
                    initiate_accordeon(value.meta_id);
                });
            }
        });
    }
    //inicia o accordeon de cada aba criada
    function initiate_accordeon(id){
        $("#accordeon-"+id).accordion({
            active: false,
            collapsible: true,
            header: "h2",
            heightStyle: "content"
        });
    }
    // funcao que retorna o id da aba ou entao retorna false se caso nao existir
    function get_tab_property_id(current_id){
        var tab_property_id = false;
        var json = jQuery.parseJSON($('#tabs_properties').val());
        if(json&&json.length>0){
            $.each(json,function(index,object){
                if(object[current_id]){
                    tab_property_id = object[current_id];
                    return true;
                } else{
                    tab_property_id = 'default';
                }
            });
        }
        return tab_property_id;
    }
    //insere as propriedades na abacorreta
    function append_property_in_tabs(){
        $items = $("#text_accordion").children();
        //propriedades
        if($("#show_form_properties").children().length>0){
            $properties = $("#show_form_properties").children();
        }else if($("#show_form_properties_edit").children().length>0){
            $properties = $("#show_form_properties_edit").children();
        }
        //rankings
        var id = '<?php echo (isset($object->ID)) ? $object->ID : $object_id  ?>'
        if($("#create_list_ranking_"+id).children().length>0){
            $rankings = $("#create_list_ranking_"+id).children();
        }else if($("#update_list_ranking_"+id).children().length>0){
            $rankings = $("#update_list_ranking_"+id).children();
        }
        // index is zero-based to you have to remove one from the values in your array
        for(var j = 0; j<$items.length;j++){
             var id = ($($items.get(j)).attr('id')) ? $($items.get(j)).attr('id').replace('meta-item-','') : '';
             if(id&&get_tab_property_id(id)){
                var $ul =  $("#accordeon-"+get_tab_property_id(id));
                $( $items.get(j) ).appendTo( $ul);
             }
        }
        for(var j = 0; j<$properties.length;j++){
            var id = ($($properties.get(j)).attr('id')) ? $($properties.get(j)).attr('id').replace('meta-item-','') : '';
            if(id&&get_tab_property_id(id)){
                 var $ul =  $("#accordeon-"+get_tab_property_id(id));
                 $( $properties.get(j) ).appendTo( $ul);
            }
        }
        for(var j = 0; j<$items.length;j++){
            var id = ($($rankings.get(j)).attr('id')) ? $($rankings.get(j)).attr('id').replace('meta-item-','') : '';
            if(id&&get_tab_property_id(id)){
                 var $ul =  $("#accordeon-"+get_tab_property_id(id)   );
                $( $rankings.get(j) ).appendTo( $ul);
            }
        }
        $('[data-toggle="tooltip"]').tooltip();
    }
    //click toggle
    function open_accordeon(id){
        if( $('#tab-'+id+' .ui-accordion-content').is(':visible')){
            $('#tab-'+id).find("div.action-text").text('<?php _e('Expand all', 'tainacan') ?>');
            $('#tab-'+id+' .ui-accordion-content').fadeOut();
            $('.cloud_label').click();
        }else{
            $('#tab-'+id+' .ui-accordion-content').fadeIn();
            $('.cloud_label').click();
            $('#tab-'+id).find("div.action-text").text('<?php _e('Collapse all', 'tainacan') ?>');
        }
    }
</script>