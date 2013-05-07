<div id="faq" class="accordion" style="float: left; height: auto; max-width: 800px; backgroung-color: white;">
    <h4 class="highlight3">Финансовые вопросы</h4>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse1_1">
              Как оплатить?
            </a>
            </div>
            <div id="collapse1_1" class="accordion-body collapse">
            <div class="accordion-inner">
                На сайте есть подробные инструкции для всех платежных систем:
                <ul>
                    <li><?php echo $html->link('Webmoney', 'http://www.teamserver.ru/aboutus/howtopay/79-wmhowto',
                                array('target' => '_blank')); ?></li>
                    <li><?php echo $html->link('Яндекс.Деньги', 'http://www.teamserver.ru/aboutus/howtopay/95-yahowto',
                                array('target' => '_blank')); ?></li>
                    <li><?php echo $html->link('Qiwi', 'http://www.teamserver.ru/aboutus/howtopay/135-qiwihowto',
                                array('target' => '_blank')); ?></li>
                    <li><?php echo $html->link('RBK Money, Кредитной картой, Салоны связи и др.', 'http://www.teamserver.ru/aboutus/howtopay/84-rbkhowto',
                                array('target' => '_blank')); ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse1_2">
              Как продлить сервер?
            </a>
            </div>
            <div id="collapse1_2" class="accordion-body collapse">
            <div class="accordion-inner">
                <p>Нажмите кнопку продления <i class="icon-shopping-cart"></i>, выберите срок продления и далее произведите оплату, как описано выше.</p>
            </div>
        </div>
    </div>
    <br/>
    <h4 class="highlight3">Общие вопросы</h4>

    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse2_1">
              Как сделать трассировку?
            </a>
            </div>
            <div id="collapse2_1" class="accordion-body collapse">
            <div class="accordion-inner">
                Подробное описание есть на нашем <?php

                echo $html->link('wiki', 'http://wiki.ghmanager.com/problem-solving:%D1%82%D1%80%D0%B0%D1%81%D1%81%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0',
                                array('target' => '_blank'));

                ?>.
            </div>
        </div>
    </div>

    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse2_2">
              Как настроить NoSteam?
            </a>
        </div>
        <div id="collapse2_2" class="accordion-body collapse">
            <div class="accordion-inner">
                Мы не оказываем поддержку по любым вопросам, связанным с NoSteam, но вы можете
                настроить его самостоятельно - у вас есть полный доступ к серверу.
                Вопросы можете задавать на <?php

                echo $html->link('форуме', 'http://forum.teamserver.ru/',
                                array('target' => '_blank'));

                ?> или в отдельной <?php

                echo $html->link('теме', 'http://forum.teamserver.ru/index.php?topic=124.0',
                                array('target' => '_blank'));

                ?>.
            </div>
        </div>
    </div>

    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse2_4">
              Где мои демки?
            </a>
        </div>
        <div id="collapse2_4" class="accordion-body collapse">
            <div class="accordion-inner">
                <p>Все демо серверов CS:S, TF2, DOD:S и HL2: MP автоматически архивируются и перемещаются в отдельную директорию, где они сортируются по дате записи. Оттуда их можно легко и быстро скачать по HTTP.</p>


                Узнать адрес с вашими демками можно, кликнув по иконке <i class="icon-folder-open"></i> в строке сервера </p>


                <p>Иногда демо не переносятся сразу, т.к. сервер часто не закрывает демо на запись, в результате файл для автоматического скрипта всё еще открыт. Вторая проблема в том, что часто игроки сами не останавливают запись и бывали случаи, когда остановка сервера сразу после матча приводила к полной потери демо.</p>

                Отсюда два главных совета:

                <ul>
                <li> Всегда останавливайте запись. В Warmod для этого достаточно прекратить матч. В других случаях просто смените карту.</li>
                <li> Если вы не обнаружили после матча запись по вашей ссылке для быстрой загрузки, просто перезапустите сервер. Все потерянные демо будут найдены и перенесены.</li>
                </ul>
            </div>
        </div>
    </div>

    <br/>
    <h4 class="highlight3">CS:S, L4D1/2, DOD:S, TF2</h4>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse3_1">
              При каждом заходе на сервер скачиваются звуки (модели, текстуры и др.). Как избавится?
            </a>
        </div>
        <div id="collapse3_1" class="accordion-body collapse">
            <div class="accordion-inner">
                <p>Откройте редактор конфигов <i class="icon-pencil"></i> и в <i>server.cfg</i> измените параметр:<br/>
                <strong>sv_pure 0</strong></p>


                <p>Причина в том, что изначально в настройках сервера прописано проверять соответствие всех файлов сервера и игры. Но дополнительные звуки/модели/текстуры и т.д. не попадают  в это правило и блокируются после загрузки их с сервера. </p>

                </ul>
            </div>
        </div>
    </div>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse3_2">
              Как установить плагины, которых нет в панели?
            </a>
        </div>
        <div id="collapse3_2" class="accordion-body collapse">
            <div class="accordion-inner">
                На нашем Wiki есть подробная <?php

                echo $html->link('инструкция для Sourcemod', 'http://wiki.teamserver.ru/game-mods-tuning:css:installsmplug',
                                array('target' => '_blank'));

                ?>.
            </div>
        </div>
    </div>

    <br/>
    <h4 class="highlight3">Counter-strike 1.6</h4>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse4_1">
              Не могу зайти на сервер, ошибка "This server is using a newer protocol (48) than your client (47)."
            </a>
        </div>
        <div id="collapse4_1" class="accordion-body collapse">
            <div class="accordion-inner">
                <p>Просто установите DProto: Нажмите <i class="icon-briefcase"></i> "Установка модов и плагинов" и кликните на DProto. Также можете установить и другие плагины.</p>
                </ul>
            </div>
        </div>
    </div>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq" href="#collapse4_2">
              Как установить плагины, которых нет в панели?
            </a>
        </div>
        <div id="collapse4_2" class="accordion-body collapse">
            <div class="accordion-inner">
                На нашем Wiki есть подробная <?php

                echo $html->link('инструкция для AmxModx', 'http://wiki.teamserver.ru/server-admin:amxmodx:plugins-install',
                                array('target' => '_blank'));

                ?>.
            </div>
        </div>
    </div>
</div>
<div id="clear"></div>
<?php
echo $html->tag('button', '<i class="icon-book"></i> Скрыть FAQ', array(  'id' => 'faq_closer',
                                                    'class' => 'btn'));
?>

<script>
    $(function() {
        $( ".accordion" ).collapse();

        $('#faq_closer').click(

                function() {

                    $('#faq_area').hide('highlight');
                    $('#faq_opener').removeAttr('disabled');

                }

            );

    });
</script>

<br/>