<?php
    include('loading_params.php');
?>

<div class="list_border">
    <h2>Журнал 50 последних предположительных атак</h2>

    <table class="intext" style="background-color: white;">
        <tr>
            <th>Время</th>
            <th>Источник</th>
            <th>Назначение</th>
            <th>Тип атаки</th>
        </tr>
    <?php

        arsort($iptablesLog);
        $i = 0;
        foreach ($iptablesLog as $log) {
            if (!empty($log))
            {
                echo '<tr>';
                echo $this->Html->tag('td', $this->Common->niceDate($log[0], 'full', 'unix'));

                echo '<td>';
                //Ссылка  для просмотра журнала атакуещего IP
                echo $this->Html->link($log[1], '#',
                                    array ('id'=>'view_attacker_'.$i, 'escape' => false
                                    ,'onClick'=>"$('#view_more').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"

                                    ));
                $effect = $this->Js->get('#view_more')->effect('slideIn');
                $event  = $this->Js->request(array('controller'=>'stats',
                                             'action'=>'getAttackerIpInfo', $log[1]),
                                       array('update' => '#view_more',
                                             'before'=>'$("#view_more").empty();'.$effect.$loadingShow,
                                             'complete'=>$loadingHide));

                $this->Js->get('#view_attacker_'.$i)->event('click', $event);

                echo '</td>';
                echo '<td>';
                //Ссылка  для просмотра журнала атакуемого IP
                $dst = preg_split('/\:/', $log[2]);

                echo $this->Html->link($log[2], '#',
                                    array ('id'=>'view_dst_'.$i, 'escape' => false
                                    ,'onClick'=>"$('#view_more').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"

                                    ));
                $effect = $this->Js->get('#view_more')->effect('slideIn');
                $event  = $this->Js->request(array('controller'=>'stats',
                                             'action'=>'getDestinationIpInfo', $dst[0], $dst[1]),
                                       array('update' => '#view_more',
                                             'before'=>'$("#view_more").empty();'.$effect.$loadingShow,
                                             'complete'=>$loadingHide));

                $this->Js->get('#view_dst_'.$i)->event('click', $event);

                echo '</td>';

                echo $this->Html->tag('td', $log[3]);
                echo '</tr>';
            }

            $i++;
        }
    ?>
    </table>
</div>
<div id="view_more" title="Подробная информация об IP" style="display: none;"></div>

<?php
// Журнал событий
if (!empty($journal)){

$statusNice = array( 'ok'    => 'OK',
                     'warn'  => 'Внимание',
                     'error' => 'Ошибка');

$userNice = array(  'user'    => 'Клиент',
                    'userOut' => 'По токену',
                    'admin'   => 'Администратор',
                    'script'  => 'Скрипт');

?>

<div class="list_border">
    <h2>Журнал событий:</h2>

    <table class="intext"  border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th>ID</th>
            <th style='min-width: 400px;'>Описание</th>
            <th>Инициатор</th>
            <th>IP</th>
            <th>Дата и время</th>
            <th>Статус</th>
        </tr>
<?php

    foreach ($journal as $journalAction) {
?>
        <tr  <?php

            if ($journalAction['Action']['status'] == 'ok')
            {
                echo 'style="background-color: #fff;"';
            }
            else
            if ($journalAction['Action']['status'] == 'warn')
            {
                echo 'style="background-color: #ffc;"';
            }
            else
            {
                echo 'style="background-color: #fcc;"';
            }

        ?>>
            <td>
                <?php

                    $this->Format= '#%1$08d';
                    printf($this->Format, $journalAction['Action']['id']);

                ?>
            </td>
            <td style="text-align: left;"><?php echo $journalAction['Action']['action']; ?></td>
            <td>
                <?php

                    if($journalAction['Action']['creator'] != 'user'){
                        echo $this->Html->tag('small', @$userNice[$journalAction['Action']['creator']]);
                    }
                    else
                    {
                        echo $this->Html->tag('small', $journalAction['User']['username']);
                    }
                ?>
            </td>
            <td>
                <?php

                    if ($journalAction['Action']['creator'] == 'user'){
                        echo $journalAction['Action']['ip'];
                    }
                ?>
            </td>
            <td><?php echo $this->Common->niceDate($journalAction['Action']['created']);?></td>
            <td><?php echo @$statusNice[$journalAction['Action']['status']]; ?></td>
        </tr>
<?php
    }

?>
    </table>
</div>

<?php
}
?>
