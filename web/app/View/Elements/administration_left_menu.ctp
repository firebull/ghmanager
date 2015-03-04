<?php
/*
 * Created on 12.04.2012
 */
 $class = array(
                'servers' => 'nav',
                'stats' => 'nav',
                'locations' => 'nav',
                'users' => 'nav',
                'supporttickets' => 'nav',
                'orders'=>'nav',
                'gametemplates' => 'nav',
                'services' => 'nav',
                'messages' => 'nav',
                'promos' => 'nav',
                'maintenances' => 'nav');

 $class[strtolower($this->params['controller'])]='active';

?>

    <ul class="admin_left_menu">
        <li class="<?php echo $class['servers']; ?>">
        <?php echo $this->Html->link( 'Клиентские серверы',
                                        array (
                                        'controller'=>'servers',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['supporttickets']; ?>">
        <?php echo $this->Html->link( 'Техподдержка ('.intval(@$openTickets).')',
                                        array (
                                        'controller'=>'supportTickets',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['stats']; ?>">
        <?php echo $this->Html->link( 'Журнал действий',
                                        array (
                                        'controller'=>'stats',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['orders']; ?>">
        <?php echo $this->Html->link( 'Заказы',
                                        array (
                                        'controller'=>'orders',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['locations']; ?>">
        <?php echo $this->Html->link( 'Локации и серверы',
                                        array (
                                        'controller'=>'locations',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>

        <li class="<?php echo $class['users']; ?>">
        <?php echo $this->Html->link( 'Клиенты',
                                        array (
                                        'controller'=>'users',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['gametemplates']; ?>">
        <?php echo $this->Html->link( 'Шаблоны',
                                        array (
                                        'controller'=>'gameTemplates',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['messages']; ?>">
        <?php echo $this->Html->link( 'Новости',
                                        array (
                                        'controller'=>'messages',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['services']; ?>">
        <?php echo $this->Html->link( 'Услуги',
                                        array (
                                        'controller'=>'services',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['promos']; ?>">
        <?php echo $this->Html->link( 'Промо',
                                        array (
                                        'controller'=>'promos',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
        <li class="<?php echo $class['maintenances']; ?>">
        <?php echo $this->Html->link( 'Обслуживание',
                                        array (
                                        'controller'=>'maintenances',
                                        'action'=>'control'
                                        )

                                );
        ?>
        </li>
    </ul>
