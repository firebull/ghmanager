<div class="ui orange dividing header"><?php echo __('Create new ticket');?></div>
<div class="ui padded grid">
    <div class="two column row">
        <div class="column" id="new_support_ticket_form">
            <?php echo $this->Form->create('SupportTicket', ['class' => 'ui form segment']);?>
                <div class="ui error message"></div>
                <div class="field">
                    <label for="ticketTitle">Тема тикета:</label>
                    <?php echo $this->Form->input('title', [ 'div'   => false,
                                                             'label' => false,
                                                             'id'    => 'ticketTitle']); ?>
                    <small>Пожалуйста, напишите тут суть проблемы в несколько слов.</small>
                </div>
                <div class="field">
                    <label for="troubleServer">Сервер:</label>
                    <?php echo $this->Form->input('Server', [ 'div'   => false,
                                                              'label' => false,
                                                              'id'    => 'troubleServer',
                                                              'required' => true]); ?>
                    <small>Укажите сервер, испытывающий проблемы. Если проблема не с сервером, выберите соответсвующий пункт. Можно выбрать НЕСКОЛЬКО серверов!</small>
                </div>
                <div class="field">
                    <label for="text">Описание проблемы:</label>
                    <?php echo $this->Form->input('Support.text',['type'=>'textarea',
                                                                  'escape'=>false,
                                                                  'div' => false,
                                                                  'label' => false,
                                                                  'id' => 'ticketText',
                                                                  'required' => false]); ?>
                    <small>Пожалуйста, сообщайте больше подробностей.</small>
                </div>
                <button class="ui primary button">Отправить</button>
                <button id="newTicketCloser" class="ui button">Отмена</button>

        <?php echo $this->Form->end(); ?>
        </div>
        <div class="column">
            <div class="ui small warning message">
                <div class="content">
                    <div class="header">Пожалуйста, опишите проблему чётко и понятно!</div>
                    <div class="description">В теме укажите суть проблемы в нескольких словах.
                    Выберите сервер, испытывающий проблемы. И адекватно, используя знаки препинания и как можно меньше
                    сокращений, опишите проблему. Это значительно ускорит нами понимание сути проблемы и поиск её решения!
                    </div>
                </div>
            </div>
            <div class="ui small info message">
                <div class="content">
                    <div class="header">Советы:</div>
                    <div class="description">
                        <div class="ui bulleted list">
                            <div class="item">Если вам нужно передать лог, пожалуйста, вставьте его в <a href="http://pastebin.com" target="_blank">pastebin.com</a> и впишите ссылку в тикет</div>
                            <div class="item">Если вам нужно передать скриншот, загрузите его на <a href="http://rghost.net" target="_blank">rghost.net</a> и вставьте ссылку в тикет</div>
                            <div class="item">Длинные ссылки можно сокращать на <a href="http://goo.gl" target="_blank">goo.gl</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui basic segment">
                <div class="ui tiny header">Время работы техподдержки</div>
                <ul>
                    <li>Финансовые вопросы: 10:00 до 19:00 по будням</li>
                    <li>Веб-хостинг: 10:00 до 22:00 каждый день</li>
                    <li>Операции на физических серверах: 10:00 до 22:00 каждый день</li>
                    <li>Другие вопросы: круглосуточно</li>
                </ul>
                <small>Задать вопрос можно в любое время, но ответ получите в указанные временные промежутки.</small>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#newTicketCloser').click(function(){
        $('#new_ticket').hide('highlight');
        $('.formError').remove();
        $('#new_ticket').empty();
        $('#new_ticket_opener').removeAttr('disabled');
        return false;
    });

    $('#SupportTicketAddForm')
      .form({
        ticketTitle: {
          identifier  : 'ticketTitle',
          rules: [
            {
              type   : 'empty',
              prompt : '<?php echo __("Please, enter Ticket title");?>'
            }
          ]
        },
        troubleServer: {
          identifier  : 'troubleServer',
          rules: [
            {
              type   : 'empty',
              prompt : '<?php echo __("Please, choose server");?>'
            }
          ]
        },
        ticketText: {
          identifier  : 'ticketText',
          rules: [
            {
              type   : 'empty',
              prompt : '<?php echo __("Please, enter problem description");?>'
            }
          ]
        },

        });

</script>

