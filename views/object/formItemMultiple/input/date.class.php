<?php

class DateClass extends FormItemMultiple {

    public function generate($compound,$property,$item_id,$index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
       //verifico se tem valor default
        $hasDefaultValue = (isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!='') ? $property['metas']['socialdb_property_default_value'] : false;
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        //se nao possuir nem valor default verifico se ja existe
        $values = (!$values && $hasDefaultValue) ? [$hasDefaultValue] : $values;
        $autoValidate = ($values && isset($values[0]) && !empty($values[0])) ? true : false;
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
        $isView = $this->viewValue($property,$values,'term');
        if($isView){
            return true;
        }
        ?>
        <?php if ($this->isRequired): ?> 
        <div class="form-group" 
             id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
             style="border-bottom:none;padding: 0px;">
                <?php if(has_action('alter_input_date')): ?>
                    <?php do_action('alter_input_date',
                            ['value'=> $values,'item_id'=>$item_id,'compound'=>$compound,'property_id'=>$property_id,'property'=>$property,'index'=>$index_id,'autoValidate'=>$autoValidate]) ?>
                <?php else: ?> 
                 <input 
                    style="margin-right: 5px;" 
                    size="13"
                    value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                    id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                    class="input_date input auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                    aria-describedby="input2Status"
                    type="text" value="">
                <?php endif; ?> 
                <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="input2Status" class="sr-only">(status)</span>
                <input type="hidden" 
                       <?php if($property_id !== 0): ?>
                       compound="<?php echo $compound['id'] ?>"
                       <?php endif; ?>
                       property="<?php echo $property['id'] ?>"
                       class="validate-class validate-compound-<?php echo $compound['id'] ?>"
                       value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
         </div>
        <?php else: ?> 
            <?php if($property_id !== 0): ?> 
                    <input  type="hidden" 
                            compound="<?php echo $compound['id'] ?>"
                            property="<?php echo $property['id'] ?>"
                            id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                            class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                            value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
            <?php endif;  ?>
            <?php if(has_action('alter_input_date')): ?>
                <?php do_action('alter_input_date',
                            ['value'=> $values,'item_id'=>$item_id,'compound'=>$compound,'property_id'=>$property_id,'property'=>$property,'index'=>$index_id,'autoValidate'=>$autoValidate]) ?>
            <?php else: ?>         
                <input 
                    style="margin-right: 5px;" 
                    size="13"
                    value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                    id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                    class="input_date auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                    type="text" value="">
            <?php endif; ?> 
        <?php
        endif;
                $this->initScriptsDate($property_id, $item_id, $compound_id, $index_id);
        if($hasDefaultValue): ?>
            <script>
                $('#date-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').trigger('blur');
            </script>
        <?php endif;         
        }

        public function initScriptsDate($property_id, $item_id, $compound_id, $index_id) { ?>
        <script>
            init_metadata_date("#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>");

            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').blur(function () {
                let field_value = $(this).val().split("/");
                let day = field_value[0], month = field_value[1], year = field_value[2];
                if(day_exist_multiple(day, month, year))
                {
                    <?php if($this->isRequired):  ?>
                        validateFieldsMetadataText($(this).val().trim(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                    <?php endif; ?>
                    $.ajax({
                        url: $('#src').val() + '/controllers/object/form_item_controller.php',
                        type: 'POST',
                        data: {
                            operation: 'saveValue',
                            type: 'data',
                            value: $(this).val().trim(),
                            item_id: $('#item-multiple-selected').val().trim(),
                            compound_id: '<?php echo $compound_id ?>',
                            property_children_id: '<?php echo $property_id ?>',
                            index: <?php echo $index_id ?>,
                            indexCoumpound: 0,
                            isKey: <?php echo ($this->isKey) ? 'true':'false' ?>
                        }
                    }).done(function (result) {
                        <?php if($this->isKey): ?>
                         var json =JSON.parse(result);
                         if(json.value){
                            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                                toastr.error(json.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                         }
                        <?php endif; ?>
                    });
                }else{
                    $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                    toastr.error('<?php _e('Invalid date!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                }
            });
            
            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').change(function () {
                let field_value = $(this).val().split("/");
                let day = field_value[0], month = field_value[1], year = field_value[2];
                if(day_exist_multiple(day, month, year))
                {
                    <?php if($this->isRequired):  ?>
                        validateFieldsMetadataText($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                    <?php endif; ?>
                    $.ajax({
                        url: $('#src').val() + '/controllers/object/form_item_controller.php',
                        type: 'POST',
                        data: {
                            operation: 'saveValue',
                            type: 'data',
                            value: $(this).val(),
                            item_id: '<?php echo $item_id ?>',
                            compound_id: '<?php echo $compound_id ?>',
                            property_children_id: '<?php echo $property_id ?>',
                            index: <?php echo $index_id ?>,
                            indexCoumpound: 0,
                            isKey: <?php echo ($this->isKey) ? 'true':'false' ?>
                        }
                    }).done(function (result) {
                        <?php if($this->isKey): ?>
                         var json =JSON.parse(result);
                         if(json.value){
                            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                                toastr.error(json.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                         }
                        <?php endif; ?>
                    });
                }else{
                    $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                    toastr.error('<?php _e('Invalid date!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                }    
            });

            function init_metadata_date(seletor) {
                $(seletor).datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    showOn: "button",
                    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                    buttonImageOnly: true,
                    changeMonth: true,
                    changeYear: true
                });
            }
            
            Hook.register(
            'get_single_item_value',
            function ( args ) {
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'getDataValue',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        item_id:args[0]
                    }
                }).done(function (result) {
                    var json = JSON.parse(result);
                    $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').attr("placeholder", "<?php _e('Alter ', 'tainacan') ?>1<?php _e(' item', 'tainacan') ?>");
                    if(json.value){
                         $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val(json.value.join(','));
                    }
                });
            });
            
            Hook.register(
            'get_multiple_item_value',
            function ( args ) {
                $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').attr("placeholder", "<?php _e('Alter ', 'tainacan') ?>" + args.length + " <?php _e(' items', 'tainacan') ?>");
            });
            
            function day_exist_multiple(day, month_number, year)
            {
                month_number--;
                let days_in_month = [/*January*/31,/*Fabruary*/ 28,/*March*/ 31,/*April*/ 30,/*May*/ 31,/*June*/ 30, /*July*/31,
                                   /*August*/31, /*September*/30, /*October*/31,/*November*/ 30, /*December*/ 31];
                if(is_leap_multiple(year))
                {
                    /*February*/
                    days_in_month[1] = 29;
                }

                if(day > days_in_month[month_number] || day < 1 || month_number > 11 || month_number < 0)
                {
                    return false;
                }
                else return true;
            }

            function is_leap_multiple(year)
            {
                if(year % 400 === 0 || (year % 4 === 0 && year % 100 !== 0))//Is a leap year
                {
                    return true;
                }else return false;
            }
        </script> 
        <?php
    }
}
