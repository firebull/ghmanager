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


    <div class="ui fixed main menu menu-shadow" style="margin-bottom: 40px; !important;">
        <div class="container">
            <a class="item menu-home-image" href="/">
                <img src="/img/icons/personage01_32px.png">
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

                echo $this->Html->link('<i class="red plus icon"></i> Заказать сервер',
                                       ['controller' => 'orders',
                                        'action'     => 'add'],
                                       ['class'      => 'item',
                                        'escape'     => false]);

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
                <a href="" class="ui item"><i class="user icon"></i> <?php echo $userinfo['User']['fullName'];?></a>

                <a href="/logout" class="ui item">Выйти</a>
            </div>
        </div>
    </div>
