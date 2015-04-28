<?php
/*
 * Created on 28.04.2015
 *
 * Made fot project GH Mananger
 * by Nikita Bulaev
 */

$statusClass = array('ok'    => '',
                     'warn'  => 'warning',
                     'error' => 'negative');

$statusNice = array( 'ok'    => 'OK',
                     'warn'  => __('Warning'),
                     'error' => __('Error'));

$userNice = array(  'user'    => __('Client'),
                    'userOut' => __('By token'),
                    'admin'   => __('Administrator'),
                    'script'  => __('Script'));

?>
<div class="ui page grid" style="margin-top: 10px;">
    <div class="ui white row">
        <div class="column">
            <div class="ui header"><?php echo __('Action log');?></div>
            <table class="ui basic table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo __('Description');?></th>
                        <th><?php echo __('Initiator');?></th>
                        <th>IP</th>
                        <th><?php echo __('Date and Time');?></th>
                        <th><?php echo __('Status');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($log as $message) {
                    ?>
                        <tr class="<?php echo $statusClass[$message['Action']['status']]?>">
                            <td>
                                <?php

                                    $this->Format= '#%1$07d';
                                    printf($this->Format, $message['Action']['id']);

                                ?>
                            </td>
                            <td style="text-align: left;"><?php echo $message['Action']['action']; ?></td>
                            <td>
                                <?php
                                    echo $this->Html->tag('small', @$userNice[$message['Action']['creator']]);
                                ?>
                            </td>
                            <td>
                                <?php

                                    if ($message['Action']['creator'] == 'user'){
                                        echo $message['Action']['ip'];
                                    }
                                ?>
                            </td>
                            <td><?php echo $this->Common->niceDate($message['Action']['created']);?></td>
                            <td><?php echo @$statusNice[$message['Action']['status']]; ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
