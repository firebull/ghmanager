<?php
/*
 * Created on 28.07.2010
 *
 */
 include('../loading_params.php');
 //pr(@$orderResult);
 //pr($script);
?>
<script type="text/javascript">                 
    $(function() {
        $('#prolongate_server').empty();
    });
</script>
<div id="flash"><?php echo $session->flash(); ?></div>
<?php
 echo $form->create('Order', array('class' => 'form-horizontal'));
?>
<table class="new_order">
                
                <tr>
                    <td class="param_value" colspan="2" style="padding-left: 23px;">
                        <label class="control-label highlight6" for="eac">Сервер:</label>
                        <div class="controls">
                        <?php   echo $form->input('GameTemplate.id', array('options' => $gameTemplatesList,
                                        'selected' => @$gameTemplateId,
                                        'div' => false, 
                                        'id'  => 'eac',
                                        'class' => 'span3',
                                        'label' => false)); ?>
                        </div>
                        </label>

                
                        
                </td>
                </tr>

                <tr>
                    <td class="param_value" colspan="2" style="padding-left: 23px; padding-top: 10px;">
                    <div class="controls">                                        
                        <?php 
                        echo $form->input('month', array('div' => false, 
                                                            'label' => false,
                                                            'type'=>'hidden'));?>
                        <div id="OrderMonthDisabled" class="accent"></div>
                        <?php 
                        echo $form->input('price', array('div' => false, 
                                                            'label' => false, 
                                                            'title'=>'Для EAC скидки нет',
                                                            'type'=>'hidden',
                                                            'size'=>'2',
                                                            'id'=> 'price', 
                                                            'style'=>'border:0; font-weight:bold;'));?>
                        
                        <div id="priceDisabled" style="display: inline;" class="accent"></div>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td class="param_value" colspan="2" style="padding-left: 23px;">
                    <div style="float: left;">
                        <label class="control-label accent" for="sliderMonth" style="margin-right: 25px; padding-top: 0px;">Срок оплаты:</label>
                    </div>
                    <div id="sliderMonth" title="Перемещайте ползунок для выбора количества месяцев" style="width: 220px;float: left; margin-top: 5px;"></div>
                    </td>
                </tr>

                <tr>
                    <td class="param_value" colspan="2">
                        <div class="well">
                        <label class="control-label accent" for="personalAccPartAmount" style="padding-top: 10px; ">Оплата с Лицевого счёта:
                        <?php

                            if ($balance > 0)
                            {
                                echo "<br/><small>(Доступно ".$balance." руб.)</small>";
                            }
                            else
                            {
                                echo "<br/><small>(Нет средств)</small>";
                            }
                        ?></label>
                        <?php 
                            
                            echo $this->element('pay_from_account', array('userBalance' => $balance));

                        ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="separator">
                    </td>
                </tr>   
                <tr>
                    <td class="param_name" style="width: 175px;"></td>
                    <td class="param_value">

                        <div id="descText" class="accent"></div>
                    
                    </td>
                </tr>
                <tr>
                    <td class="param_name" style="padding-bottom: 6px;">Итого:</td>
                    <td class="param_value">
                    
                        <?php 
                        echo $form->input('sum', array('div' => false, 
                                                            'label' => false,
                                                            'type'=>'hidden'));?>
                                                        
                        <div id="OrderSumDisabled" class="accent_more"></div>
                        <div class="accent"> рублей за сервер</div>
                    
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                    <?php   echo $form->input('Server.id', array(
                                    
                                    'div' => false,
                                    'label' => false)); ?>
                    <?php
                    
                        echo $js->submit('Отправить',
                                                    array(
                                                        'url'=> array(
                                                                        'controller'=>'Orders',
                                                                        'action'=>'addEac'
                                                         ),
                                                        'update' => '#add_server',
                                                        'class' => 'btn btn-primary',
                                                        'before' =>$loadingShow,
                                                        'complete'=>$loadingHide,
                                                        'buffer' => false));
                                                
                        echo $form->end();
                    ?>
                    </td>
                </tr>
</table>
<?php 
            
            echo $js->writeBuffer(); // Write cached scripts 
?>
<script type="text/javascript"> 
                
                    $(function() {                                                                                          
                        var balance = <?php echo $balance; ?>;
                            
                        function populateTemplatesList(tmps) {
                              var options = '';
                              <?php
                              if (@$gameTemplateId){
                                  echo "var selectedGame = '".$gameTemplateId."'";
                              }
                              else
                              {
                                  echo "var selectedGame = 0";
                              }
                              ?>
                              
                            
                              $.each(tmps, function(index, tmp) {
                                if (selectedGame == index){
                                    options += '<option value="' + index + '" selected="selected">' + tmp + '</option>';
                                }
                                else
                                {
                                    options += '<option value="' + index + '">' + tmp + '</option>';
                                }
                              
                              });
                              $('#eac').html(options);
                              $('#types').show();
                            
                            }
                                                       
                                                                                                               
                        function MonthsSlider () {
                                                        
                            $("#sliderMonth").slider({
                                range: "max",
                                value: 1,
                                min: 1,
                                max: 9,
                                step: 1,
                                slide: function(event, ui) {
                                    $("#OrderMonth").val(ui.value);
                                    $("#OrderMonthDisabled").text(monthText(ui.value));
                                    Sum();
                                }
                            });
                            
                                                                                
                            $("#OrderMonth").val($("#sliderMonth").slider("value"));
                            $("#OrderMonthDisabled").text(monthText($("#sliderMonth").slider("value")));
                            
                        };
                        
                        function monthText(monthValue){
                            if (monthValue == 1){
                                var monthText = '1 месяц';
                            }
                            else
                            if (monthValue > 1 && monthValue < 5)
                            {
                                var monthText = monthValue + ' месяца';
                            }
                            else
                            if (monthValue >= 5 && monthValue < 21)
                            {
                                var monthText = monthValue + ' месяцев';
                            }
                            return monthText;
                        }
                        
                        function Sum () {
                            
                            var selectedEac = $("#eac").val();

                            <?php echo $script;?>

                            var month = $("#OrderMonth").val();

                            //var price = $('#price').val();  

                            var payPart = $("#personalAccPartAmount").val();
                            
                            var sum = month*(eval(price));
                            
                            if (payPart > balance)
                            {
                                payPart = balance;              
                            }

                            if ($("#personalAccPart").attr('checked') || $("#personalAccFull").attr('checked')){
                                if (balance > 0 && balance < sum){
                                    $("#personalAccFull").removeAttr('checked').attr('disabled','disabled');
                                    $("#personalAccPart").removeAttr('disabled').attr('checked','checked');
                                    $('#personalAccPartPay').show();
                                    $('#zonePart').addClass('tbl_hover_green');
                                    $('#zoneFull').removeClass('tbl_hover_green');
                                }
                                else
                                if (balance >= sum)
                                {
                                    $("#personalAccFull").removeAttr('disabled');

                                }

                                if ($("#personalAccPart").attr('checked'))
                                {

                                    if (payPart >= sum)
                                    {
                                        payPart = 0;
                                        $('#personalAccPartPay').hide();
                                        $("#personalAccPartAmount").val('');
                                        $("#personalAccFull").attr('checked','checked');
                                        $('#zonePart').removeClass('tbl_hover_green');
                                        $('#zoneFull').addClass('tbl_hover_green');
                                    }
                                    else
                                    if (payPart > 0 && payPart < sum)
                                    {
                                        sum = eval(sum) - eval(payPart);
                                        $("#personalAccPartAmount").val(payPart);
                                        $('#zonePart').addClass('tbl_hover_green');
                                        $('#zoneFull').removeClass('tbl_hover_green');
                                    }
                                }
                            }


                            $("#OrderSum").val(sum);
                            $("#OrderSumDisabled").text(sum);
                            Price();
                            
                        }
                        
                        
                        function Price (){
                            $("#price").val(price);
                            $("#priceDisabled").text(' по ' + price + ' рублей в месяц');
                        }
                        
                        function eacDesc(){
                            var eac = $("#eac").val();

                            if (eac == 37)
                            {
                                descText = '<strong>Только</strong> сервер с нашего хостинга может быть подключен на этот EAC.'
                            }
                            else
                            if (eac == 38)
                            {
                                descText = 'Вы сможете подключить <strong>любой</strong> сервер с любого хостинга.'
                            }

                            $('#descText').html(descText);
                        }
                        
                        $("#eac").change(function() {

                                Price(); 
                                Sum();
                                eacDesc();
                                return false;
                        });

                        
                        $("#personalAccPartAmount").keyup(function() {
                            Sum();
                        });

                        $("#personalAccPart").click(function() {
                                                            Sum();      
                                                            $('#personalAccPartPay').show('highlight');
                                                            $('#zoneFull').removeClass('tbl_hover_green');                          
                                                        });
                        
                        $("#personalAccFull").click(function() {        
                                                            $('#personalAccPartPay').hide();
                                                            $('#zonePart').removeClass('tbl_hover_green');
                                                            $('#zoneFull').addClass('tbl_hover_green'); 
                                                            Sum();                      
                                                        });
                        $("#personalAccNo").click(function() {      
                                                            $('#personalAccPartPay').hide();
                                                            $('#zonePart').removeClass('tbl_hover_green');
                                                            $('#zoneFull').removeClass('tbl_hover_green');  
                                                            Sum();                      
                                                        });                             

                        MonthsSlider();
                        Price();                   
                        Sum();
                        eacDesc();
                    });
    </script>

                