<?php
/*
 * Created on 13.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
?>
<div class="ui padded grid" style="height: 100%;" id="ordersList">
    <div class="ui row"  style="height: 100%;">
        <div class="two wide column">

        </div>
        <div class="twelve wide white column" style="height: 100%; display: inline-table;">
<?php
if (@$orders){
?>
    <div id="flash"><?php echo $this->Session->flash(); ?></div>

    <div class="ui dividing header">Неоплаченные заказы:</div>
    <div class="ui warning message">
        Внимание! После оплаты Яндекс.Деньгами вам нужно будет вручную вернуться в панель и обновить страницу с заказами! Иначе вы не увидите изменения статуса заказа!
    </div>
    <table class="ui table">
        <thead>
            <th>№ заказа</th>
            <th style="width: 40px;"></th>
            <th style="width: 200px;">Дата формирования</th>
            <th>Назначение</th>
            <th>Сумма</th>
            <th>Действие</th>
        </thead>

        <!-- Here is where we loop through our array, printing out info -->

        <?php
        $i = 1;
        foreach ($orders as $order):
            if ($order['payed'] == '1' && @$pastStatus == '0'
                or
                $order['payed'] == '1' && !@$pastStatus){

                $pastStatus = '1';
                $i = 1;
                /*
                 * Закроем предыдущую таблицу и откроем новую,
                 * с закрытыми тикетами.
                 */

        ?>
    </table>

    <div class="ui dividing header">Оплаченные заказы:</div>
    <table class="ui table">
        <thead>
            <th>№ заказа</th>
            <th style="width: 40px;"></th>
            <th style="width: 200px;">Дата формирования</th>
            <th>Назначение</th>
            <th>Сумма</th>
            <th>Статус</th>
            <th></th>
        </thead>
            <?php

                } //if

            ?>

            <tr id="opener_<?php echo $order['id']; ?>">
                <td><?php echo $order['id']; ?></td>
                <td>
                    <a title="Подробности заказа" class="ui icon button" data-bind="event: {click: showModal.bind(false, 'small', 'Подробности заказа', '/orders/detail/<?php echo $order['id'];?>')}">
                        <i class="icon book"></i>
                    </a>


                </td>
                <td><?php echo $this->Common->niceDate($order['created']);?></td>
                <td>
                    <?php

                        if (!empty($order['month'])){
                            if (!empty($order['Server'][0]['id'])){
                                echo 'Аренда сервера #'.$order['Server'][0]['id'];
                            }
                            else
                            {
                                echo 'Сервер';
                            }

                        }
                        else
                        {
                            echo 'Пополнение счёта';
                        }

                     ?>
                </td>
                <td>
                    <?php

                        echo $order['sum']." рублей";
                        if ($order['sumToPay'] > 0 and $order['sum'] > $order['sumToPay'])
                        {
                            echo "<br/>К оплате: ".$order['sumToPay'].' руб.';
                        }

                    ?>
                </td>
                <td>
                    <?php
                        if ($order['payed'] == 0){
                    ?>
                            <a data-bind="event: {click: showModal.bind(false, '', 'Пожалуйста, выберите способ оплаты', '/orders/pay/<?php echo $order['id'];?>')}">Оплатить</a>
                            <br/>
                            <a data-bind="event: {click: orderDelete.bind(false, <?php echo $order['id'];?>)}">Отменить</a>
                    <?php

                        }
                        else
                        {
                            echo "Оплачен<br/>";
                            if (!empty($order['payedDate'])){
                                echo $this->Html->tag('small', $this->Common->niceDate($order['payedDate']));
                            }
                        }
                    ?>
                </td>
                <?php
                    if ($order['payed'] == 1){
                ?>
                <td>
                    <?php

                        if (!empty($order['payedBy']) && ($order['payedBy'] != 'manual' or $order['payedBy'] != 'unknown')){
                            $paymentImgs['yandex']   = 'yamoney_logo88x31.gif';
                            $paymentImgs['rbk']      = 'rbk_58.png';
                            $paymentImgs['webmoney'] = 'webmoney_blue_on_white_88.png';
                            $paymentImgs['qiwi']     = 'qiwi_goriz_88.png';
                            $paymentImgs['internal'] = 'personage01_x41x40.png';

                            if (!empty($paymentImgs[$order['payedBy']])){
                                echo $this->Html->image('icons/'.$paymentImgs[$order['payedBy']]);
                            }

                        }

                    ?>
                </td>
                <?php
                    }
                ?>
            </tr>

        <?php

            $pastStatus = $order['payed'];
        endforeach; ?>



    </table>

<?php
    }
    else
    {
?>
    <div class="ui message">
        У вас пока нет заказов.
    </div>
<?php
    }
?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ordersListViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.showModal = function(size, title, bodyUrl, data){
            var self = this;

            $('#topMenuModal').removeClass('small large fullscreen').addClass(size);
            $('#topMenuModal .header').html(title);


            self.loading(true);

            $.get( bodyUrl )
             .done(
                    function(data){
                        $('#topMenuModal .content .description').empty();
                        $('#topMenuModal .content .description').html(data);
                        $('#topMenuModal').modal(
                            {onHide: function(){
                                    $('#topMenuModal .content .description').empty();
                                    window.location.href = "/orders";
                                    }
                            })
                        .modal('show').modal('refresh');

                        self.loading(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                }
             });

        }.bind(this);

        this.orderDelete = function(orderId){
            var self = this;

            swal({
              title: "Подтвердите отмену",
              text: "Вы уверены, что хотите отменить заказ #" + orderId + "?",
              type: "warning",
              showCancelButton: true,
              closeOnConfirm: false,
              confirmButtonText: "Да, отменить заказ",
              cancelButtonText: "Нет",
              confirmButtonColor: "#ec6c62"
            }, function() {
              $.ajax({
                url: "/orders/cancel/" + orderId,
                type: "PUT"
              })
              .done(function(data) {
                swal("Отменён!", "Заказ успешно отменён.", "success");
                window.location.href = "/orders";
              })
              .error(function(data) {
                swal("Ошибка", "Не удалось подключиться к серверу", "error");
              });
            });
        }.bind(this);

    };

    ko.applyBindings(new ordersListViewModel(), document.getElementById("ordersList"));
</script>


