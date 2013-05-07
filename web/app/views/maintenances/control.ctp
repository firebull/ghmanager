<pre>
<?php
//Иконка для отмены заказа
$confiremDeleteMessage = 'Вы уверены, что хотите удалить ВСЕ неоплаченные заказы и серверы, к ним привязанные, старше двух недель?'.
                         "\n<br/><br/>Это необратимая операция!" ;
echo $html->link('- Удаление неоплаченных заказов старше 2-х недель.', '#',
                        array ('id'=>'order_cansel_all', 'escape' => false
                        ,'onClick'=>"$('#clean_confirm').dialog({
                                                                    resizable: false,
                                                                    height:250,
                                                                    width: 400,
                                                                    modal: true,
                                                                    buttons: {

                                                                            'Удалить заказы': function() {
                                                                            window.location.href='/orders/clearExpired';
                                                                            $(this).dialog('close');
                                                                        },
                                                                        Закрыть: function() {
                                                                            $(this).dialog('close');
                                                                        }
                                                                    }
                                                                });"
                        
                        ));
?>
<div id="clean_confirm" title="Подтвердите операцию!" style="display: none;">
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
            <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
            <?php echo $confiremDeleteMessage; ?>                               
        </div>
</div>



 - Удаление неоплаченных серверов старше 1 месяца

 - Очистка журнала от записей старше 2-х месяцев
</pre>