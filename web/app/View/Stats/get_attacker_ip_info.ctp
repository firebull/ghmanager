<?php
    if (!empty($ip))
    {
?>
    <h2>Описание IP <?php echo $ip; ?></h2>

    <h3>Расположение:</h3>

    <div style="padding-left: 10px; padding-bottom: 15px;">
        <strong>Страна: </strong><?php echo @$geo['country_name']; ?><br/>
        <strong>Город: </strong><?php echo @$geo['city']; ?><br/>
        <strong>Имя хоста: </strong><?php echo gethostbyaddr($ip);?>
    </div>

    <h3>Последние 25 действий с этого IP:</h3>

    <table class="intext" style="background-color: white;">
        <tr>
            <th>Время</th>
            <th>Источник</th>
            <th>Назначение</th>
            <th>Тип атаки</th>
        </tr>
    <?php
        arsort($iptablesLog);

        foreach ($iptablesLog as $log) {
            if (!empty($log))
            {
                echo '<tr>';
                echo $this->Html->tag('td', $this->Common->niceDate($log[0], 'full', 'unix'));
                echo $this->Html->tag('td', $log[1]);
                echo $this->Html->tag('td', $log[2]);
                echo $this->Html->tag('td', $log[3]);
                echo '</tr>';
            }
        }
    ?>
    </table>

<?php

    }
?>
