<?php
/*
 * Created on 31.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('loading_params.php');
 //pr($this->data);
?>
<cake:nocache>
<div id="voice_params" style="margin-left: 30px;">

    <div id="flash"><?php echo $this->Session->flash();?></div>
    <div id="ui error message">
    <?php
    if (@$errors) {
        foreach ( @$errors as $field => $error ) {
           echo "Ошибка в поле ".$field.": ".$error."<br/>";
        }
    }
    ?>
    </div>

    <?php echo $this->Form->create('Server', ['url' => ['action' => 'editParamsVoiceMumble',
                                                         $this->data['Server']['id'],
                                                         2],
                                              'class' => 'ui form',
                                              'id'    => 'voiceMumbleForm']); ?>




        <div class="field">
            <label class="desc">defaultchannel <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Канал по умолчанию, на который будут перебрасываться вошедшие при заходе на сервер. Указывайте номер канала, а не имя!
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.defaultchannel',
                                        array(
                                            'div' => false,
                                            'label' => false,
                                            'error' => true,
                                            'class' => ''));?>
        </div>
        <div class="field">
            <label class="desc">autobanAttempts <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Количество попыток подключения с одного IP - как удачных, так и неудачных. Напишите 0, чтобы отключить функцию.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.autobanAttempts',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">autobanTimeframe <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Промежуток времени в секундах, во время которого считается количество попыток подключения. Напишите 0, чтобы отключить функцию.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.autobanTimeframe',
                                    array('div' => false,
                                          'label' => false,
                                          'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">autobanTime <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Время в секундах, на которое создаётся бан.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.autobanTime',
                                    array(  'div' => false,
                                            'label' => false,
                                            'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">Welcometext <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Сообщение, которое показывается всем вошедшим. Можно использовать HTML-теги.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.welcometext',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">ServerPassword <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Пароль сервера. <br/>Должен быть пустым, если сервер публичный.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.serverpassword',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">textmessagelength <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Максимальная длина текстового сообщения в символах. Введите 0, чтобы отключить ограничение.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.textmessagelength',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">imagemessagelength <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Максимальная длина текстового сообщения, содержащего изображения, в символах. Введите 0, чтобы отключить ограничение.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.imagemessagelength',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">allowhtml <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Позволять ли использовать HTML в сообщениях, комментариях и описании канала.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.allowhtml',
                                    array(
                                        'options'=>array(
                                                        'true' => 'Да',
                                                        'false'=> 'Нет'
                                                        ),
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>

        <div class="field">
            <label class="desc">registerName <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Публичное имя сервера.<br/>Чтобы позволить публичную регистрацию, необходимо заполнить все поля register, а пароль сервера должен быть
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.registerName',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>

        <div class="field">
            <label class="desc">registerPassword <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Пароль для публичной регистрации сервера.<br/> Чтобы позволить публичную регистрацию, необходимо заполнить это поле, а пароль сервера должен быть пустым.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.registerPassword',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">registerUrl <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                URL web-сервера при публичной регистрации сервера.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.registerUrl',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">registerHostname <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Если не заполнено поле выше, то впишите сюда IP-адрес web-сервера для публичной регистрации.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.registerHostname',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true
                                                ));?>
        </div>
        <div class="field">
            <label class="desc">bandwidth <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Максимальная полоса пропускания, с которой клиентам разрешено отправлять данные на сервер. <br/><br/>Перемещайте ползунок для выбора значения.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.bandwidth',
                                    array(
                                        'type' => 'hidden',
                                        'div' => false,
                                        'id' => 'bandwidthHidden',
                                        'label' => false,
                                        'error' => true

                                                ));

                echo $this->Html->tag('div','', array ('id' => 'bandwidthDisabled' ));

                echo $this->Html->tag('div','', array ('id' => 'bandwidth'));
            ?>
        </div>
        <div class="field">
            <label class="desc">sslCert <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Если у вас уже есть SSL-сертификат, загрузите его в директорию сервера через FTP и укажите здесь имя файла.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.sslCert',
                                    array(
                                        'size' => '30',
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">sslKey <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Если у вас уже есть ключ SSL-сертификата, загрузите его в директорию сервера через FTP и укажите здесь имя файла.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.sslKey',
                                    array(
                                        'div' => false,
                                        'label' => false,
                                        'error' => true));?>
        </div>
        <div class="field">
            <label class="desc">certrequired <i class="info circle icon"></i></label>
            <div class="ui fluid popup">
                <h5>Подсказка</h5>
                Требовать ли сертификат у клиента.
            </div>
            <?php echo $this->Form->input('VoiceMumbleParam.certrequired',
                                    array(
                                        'options'=>array(
                                                        'true' => 'Да',
                                                        'false'=> 'Нет'
                                                        ),
                                        'div' => false,
                                        'label' => false,
                                        'error' => true
                                                ));?>
        </div>
        <?php
            echo $this->Form->input('id', array('type'=>'hidden'));

            echo $this->Js->submit('Сохранить',
                                        array(
                                            'url'=> array(
                                                            'controller'=>'Servers',
                                                            'action'=>'editParamsVoiceMumble',
                                                            $this->data['Server']['id'],
                                                            2
                                             ),
                                            'update' => '#indexModal .content .description',
                                            'class' => 'ui fluid green button',
                                            'before' => '$("#voiceMumbleForm").addClass("loading");',
                                            'complete'=>'$("#voiceMumbleForm").removeClass("loading");',
                                            'buffer' => false));
        ?>
    <?php echo $this->Form->end();?>
<script type="text/javascript">
    $(function() {

        $('.desc').popup({
            inline  : true
        });

        $("#bandwidth").slider({
                                range: "max",
                                value: <?php echo $this->data['VoiceMumbleParam']['bandwidth']; ?>,
                                min: 8192,
                                max: 131072,
                                step: 2048,
                                slide: function(event, ui) {
                                    $("#bandwidthHidden").val(ui.value);
                                    countKbits();

                                }
                            });

        function countKbits() {
$('#indexModal').modal('refresh');
            var bits = $("#bandwidthHidden").val();

            kbits = Math.round(eval(bits/1024)) + 'Кбит';
            $("#bandwidthDisabled").text(kbits);

        }

        countKbits();

    });
</script>
</div>
<?php //pr($this->data['VoiceMumbleParam'][0]);
?>
</cake:nocache>
