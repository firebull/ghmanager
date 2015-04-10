<?php
    /*
        Created by Nikita Bulaev at 10.03.2015
     */

$class = ['servers'        => 'item',
          'supportTickets' => 'item',
          'orders'         => 'item',
          'administration' => 'item'];


if ($this->params['action'] == 'control')
{
    $class['administration'] = 'ui item active';
}
else
{
    $class[$this->params['controller']] = 'ui red item active';
}

?>


    <div class="ui fixed main menu menu-shadow" style="margin-bottom: 40px; !important;" id="topMenu">
        <div class="container">
            <a class="item menu-home-image" href="/">
                <?php
                echo $this->Html->image(Configure::read('Panel.vendor.logo'));
                ?>
            </a>
            <?php
                echo $this->Html->link('<i class="game icon"></i> Серверы',
                                       ['controller' => 'servers',
                                        'action'     => 'index'],
                                       ['class'      => $class['servers'],
                                        'escape'     => false]);

                echo $this->Html->link('<i class="doctor icon"></i> Помощь',
                                       ['controller' => 'supportTickets',
                                        'action'     => 'index'],
                                       ['class'      => $class['supportTickets'],
                                        'escape'     => false]);

                echo $this->Html->link('<i class="in cart icon"></i> Заказы',
                                       ['controller' => 'orders',
                                        'action'     => 'index'],
                                       ['class'      => $class['orders'],
                                        'escape'     => false]);

                echo $this->Html->tag( 'div',
                                       '<i class="red plus icon"></i> Заказать сервер',
                                       ['class'      => 'item',
                                        'escape'     => false,
                                        'style'      => 'cursor: pointer;',
                                        'id'         => 'orderButton']);

            ?>
            <div class="right menu">
            <?php
            /*
             * Выводить административную часть
             */
            if (strtolower($userinfo['Group'][0]['name']) == 'admin'
                 or strtolower($userinfo['Group'][0]['name']) == 'gameadmin')
            {
                echo $this->Html->link('<i class="red setting icon"></i> Администрирование',
                                       ['controller' => 'administration'],
                                       ['class'      => $class['administration'],
                                        'escape'     => false]);
            }
            ?>
                <div class="ui topmenu dropdown item">
                    <i class="user icon"></i>
                    <?php echo $userinfo['User']['fullName'];?>
                    <i class="dropdown icon"></i>
                    <div class="ui menu">
                        <div class="header text item">
                            Профиль
                        </div>
                        <div class="text item">
                            <div class="ui list">
                                <div class="item" style="color: #222 !important;">
                                    <i class='user icon'></i>
                                    <div class="content">
                                        <div class="header">
                                            <?php echo $userinfo['User']['fullName'];?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($userinfo['User']['steam_id'])){ ?>
                                <div class="item" style="color: #222 !important;">
                                    <i class='steam square icon'></i>
                                    <div class="content">
                                        <div class="desription">
                                            <?php echo $userinfo['User']['steam_id'];?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="item" style="color: #222 !important;">
                                    <i class='envelope icon'></i>
                                    <div class="content">
                                        <div class="desription">
                                            <?php echo $userinfo['User']['email'];?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (@$userinfo['User']['discount'] > 0){ ?>
                                <div class="item" style="color: #222 !important;">
                                    <i class='money icon'></i>
                                    <div class="content">
                                        <div class="desription">
                                            <?php echo 'Ваша скидка: '.$userinfo['User']['discount'];?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php
                                    $headerPrinted = false;
                                    $userIsTester = false;
                                    foreach ( $userinfo['Group'] as $group ) {
                                        if (strtolower($group['name']) != 'member'){
                                            if ($headerPrinted === false){
                                                echo "<br/>Ваши права доступа:";
                                                echo "<ul style='padding-left: 15px; margin-left: 10px; margin-bottom: 0px; margin-top: 0px;'>";
                                                $headerPrinted = true;
                                            }

                                            echo "<li>".$group['desc'].'</li>';

                                            // Определить ключ привязки к БетаТестерам
                                            if (strtolower($group['name']) == 'betatesters'){
                                                $userIsTester = true;
                                            }
                                        }
                                    }
                                    if ($headerPrinted === true){
                                        echo "</ul>";
                                    }

                                ?>
                            </div>
                        </div>
                        <div class="header item">
                            Действия
                        </div>
                        <a data-bind="event: {click: showModal.bind(true, '', 'Изменить профиль', '/users/edit/ver:2')}" class="item">
                            <i class="blue user icon"></i>Изменить профиль
                        </a>
                        <a data-bind="event: {click: showModal.bind(true, 'small', 'Изменить пароль FTP', '/users/changeFtpPass/ver:2')}" class="item">
                            <i class="blue lock icon"></i>Пароль FTP
                        </a>
                        <a href="/servers/webHosting/ver:2" class="item">
                            <i class="blue cloud icon"></i>Бесплатный Web-хостинг
                        </a>
                    </div>
                </div>

                <a href="/logout" class="ui item">Выйти</a>
            </div>
        </div>
        <div class="ui flowing popup" id="orderMenu">
            <div class="ui three column divided equal height center aligned grid" style="min-width: 500px;">
                <div class="column">
                    <div style="cursor: pointer;" data-bind="event: {click: showModal.bind(false, '', 'Заказать игровой сервер', '/orders/add/1')}">
                        <img class="ui centered image" src="/img/bigicons/ico-game.png"/>
                        <br/>
                        <div class="ui blue label">Игровой сервер</div>
                    </div>
                </div>
                <div class="column">
                    <div style="cursor: pointer;" data-bind="event: {click: showModal.bind(false, 'small', 'Заказать голосовой сервер', '/orders/add/2')}">
                        <img class="ui centered image" src="/img/bigicons/ico-voice.png"/>
                        <br/>
                        <div class="ui blue label"><nobr>Голосовой сервер</nobr></div>
                    </div>
                </div>
                <div class="column">
                    <div style="cursor: pointer;" data-bind="event: {click: showModal.bind(false, 'small', 'Заказать сервер EAC', '/orders/addEac/8')}">
                        <img class="ui centered image" src="/img/bigicons/ico-eac.png"/>
                        <br/>
                        <div class="ui blue label"><nobr>Сервер EAC</nobr></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ui small modal" id="topMenuModal">
        <i class="close icon"></i>
        <div class="header"></div>
        <div class="content"><div class="description"></div></div>
        <div class="actions">
            <div class="ui button">Отмена</div>
        </div>
    </div>
<script type="text/javascript">
    $('.topmenu.dropdown').dropdown({action: 'hide'});

    $('#orderButton')
          .popup({
            popup : $('#orderMenu'),
            on    : 'click'
          })
        ;

    var topMenuViewModel = function(){

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
                        $('#topMenuModal').modal({allowMultiple: true}).modal('show').modal('refresh');

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

    };

    ko.applyBindings(new topMenuViewModel(), document.getElementById("topMenu"));
</script>
