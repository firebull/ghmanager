<?php
    if (!empty($ip))
    {
?>
    <h2>Последние 25 действий на IP <?php echo $ip; ?></h2>

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
