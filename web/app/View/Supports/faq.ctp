<div class="ui padded grid">
    <div class="three wide column">
        <?php
            echo $this->element('support_menu', []);
        ?>
    </div>
    <div class="thirteen wide column">
        <div class="ui segment">
            <div class="ui dividing header">Финансовые вопросы</div>
            <div class="ui fluid items">
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Как оплатить?
                        </div>
                        <div class="description">
                            На
                            <?php
                                echo $this->Html->link('сайте', Configure::read('Links.site'));
                            ?>
                            есть подробные инструкции для всех платежных систем
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="content">
                    <div class="header">
                        Как продлить сервер?
                    </div>
                    <div class="description">
                        Нажмите кнопку продления <i class="add to cart icon"></i>, выберите срок продления и далее произведите оплату, как описано выше.
                    </div>
                </div>
                </div>
            </div>

            <div class="ui dividing header">Общие вопросы</div>
            <div class="ui items">
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Как сделать трассировку?
                        </div>
                        <div class="description">
                            Подробное описание есть на нашем <?php

                            echo $this->Html->link('wiki', 'https://github.com/firebull/ghmanager/wiki/%D0%A2%D1%80%D0%B0%D1%81%D1%81%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0',
                                            array('target' => '_blank'));

                            ?>.
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Как настроить NoSteam?
                        </div>
                        <div class="description">
                            Мы не оказываем поддержку по любым вопросам, связанным с NoSteam, но вы можете
                            настроить его самостоятельно - у вас есть полный доступ к серверу.
                            Вопросы можете задавать на <?php

                            echo $this->Html->link('форуме', 'http://forum.teamserver.ru/',
                                            array('target' => '_blank'));

                            ?> или в отдельной <?php

                            echo $this->Html->link('теме', 'http://forum.teamserver.ru/index.php?topic=124.0',
                                            array('target' => '_blank'));

                            ?>.
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Где мои демки?
                        </div>
                        <div class="description">
                            <p>Все демо серверов CS:S, TF2, DOD:S и HL2: MP автоматически архивируются и перемещаются в отдельную директорию, где они сортируются по дате записи. Оттуда их можно легко и быстро скачать по HTTP.</p>

                            Узнать адрес с вашими демками можно, кликнув по иконке <i class="icon folder open"></i> в строке сервера </p>

                            <p>Иногда демо не переносятся сразу, т.к. сервер часто не закрывает демо на запись, в результате файл для автоматического скрипта всё еще открыт. Вторая проблема в том, что часто игроки сами не останавливают запись и бывали случаи, когда остановка сервера сразу после матча приводила к полной потери демо.</p>

                            Отсюда два главных совета:

                            <ul>
                                <li> Всегда останавливайте запись. В Warmod для этого достаточно прекратить матч. В других случаях просто смените карту.</li>
                                <li> Если вы не обнаружили после матча запись по вашей ссылке для быстрой загрузки, просто перезапустите сервер. Все потерянные демо будут найдены и перенесены.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui dividing header">CS:S, L4D1/2, DOD:S, TF2</div>
            <div class="ui items">
                <div class="item">
                    <div class="content">
                        <div class="header">
                            При каждом заходе на сервер скачиваются звуки (модели, текстуры и др). Как избавится?
                        </div>
                        <div class="description">
                            <p>Откройте редактор конфигов <i class="icon pencil"></i> и в <i>server.cfg</i> измените параметр:<br/>
                            <blockquote>sv_pure 0</blockquote></p>

                            <p>Причина в том, что изначально в настройках сервера прописано проверять соответствие всех файлов сервера и игры. Но дополнительные звуки/модели/текстуры и т.д. не попадают  в это правило и блокируются после загрузки их с сервера. </p>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Как установить плагины, которых нет в панели?
                        </div>
                        <div class="description">
                            На нашем Wiki есть подробная <?php

                            echo $this->Html->link('инструкция для Sourcemod', 'https://github.com/firebull/ghmanager/wiki/%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%B8-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B8%D0%BB%D1%8F%D1%86%D0%B8%D1%8F-%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%BE%D0%B2-%D0%BF%D0%BE%D0%B4-SourceMod',
                                            ['target' => '_blank']);

                            ?>.
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui dividing header">Counter-strike 1.6</div>
            <div class="ui items">
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Не могу зайти на сервер, ошибка "This server is using a newer protocol (48) than your client (47)."
                        </div>
                        <div class="description">
                            <p>Просто установите DProto: Нажмите <i class="icon-briefcase"></i> "Установка модов и плагинов" и кликните на DProto. Также можете установить и другие плагины.</p>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="header">
                            Как установить плагины, которых нет в панели?
                        </div>
                        <div class="description">
                            На нашем Wiki есть подробная <?php

                            echo $this->Html->link('инструкция для AmxModx', 'https://github.com/firebull/ghmanager/wiki/%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%B8-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B8%D0%BB%D1%8F%D1%86%D0%B8%D1%8F-%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%BE%D0%B2-%D0%BF%D0%BE%D0%B4-AMX-Mod-X',
                                            ['target' => '_blank']);

                            ?>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
