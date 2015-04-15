<?php
    if (isset($result) and $result == 'ok'){
?>
    <script type="text/javascript">
        //window.location.href = "/SupportTickets";
    </script>
<?php
    }
?>
<div class="ui padded grid" id="new_support_ticket_form">
    <div class="two column row">
        <div class="column">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->Form->create('SupportTicket', ['class' => 'ui form segment',
                                                             'data-bind' => 'css: {"error": errors.length() > 0']);?>
                <div class="ui error message" data-bind="template: {name: 'errors'}"></div>
                <div class="field">
                    <label for="ticketTitle">Тема тикета:</label>
                    <?php echo $this->Form->input('title', [ 'div'   => false,
                                                             'label' => false,
                                                             'error' =>
                                                                ['class' => 'error',
                                                                    'attributes' => ['wrap' => 'div',
                                                                                     'class' => 'ui red pointing above ui label']],
                                                             'id'    => 'ticketTitle',
                                                             'required' => false]); ?>
                    <small>Пожалуйста, напишите тут суть проблемы в несколько слов.</small>
                </div>
                <div class="field">
                    <label for="troubleServer">Сервер:</label>
                    <?php echo $this->Form->input('Server', [ 'div'   => false,
                                                              'label' => false,
                                                              'class' => 'ui checkbox fluid',
                                                              'multiple' => 'checkbox',
                                                              'error' =>
                                                                ['class' => 'error',
                                                                    'attributes' => ['wrap' => 'div',
                                                                                     'class' => 'ui red pointing above ui label']],
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
                                                                  'error' =>
                                                                    ['class' => 'error',
                                                                        'attributes' => ['wrap' => 'div',
                                                                                         'class' => 'ui red pointing above ui label']],
                                                                  'id' => 'ticketText',
                                                                  'required' => false]); ?>
                    <small>Пожалуйста, сообщайте больше подробностей.</small>
                </div>
                <div class="ui primary button" id="createTicketButton" data-bind="event: {click: sendTicket.bind(false)}"><?php echo __('Send');?></div>
                <button id="newTicketCloser" class="ui button"><?php echo __('Cancel');?></button>

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
<script type="text/html" id="errors">
    <ul data-bind="foreach: {data: errors, as: 'error'}">
        <li data-bind="text: error"></li>
    </ul>
</script>
<script type="text/javascript">
    var newTicketViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.sendTicket = function(){

            var self = this;

            var valid = $('#SupportTicketAddForm')
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
                ticketText: {
                  identifier  : 'ticketText',
                  rules: [
                    {
                      type   : 'empty',
                      prompt : '<?php echo __("Please, enter problem description");?>'
                    }
                  ]
                }
                }).form('validate form');


            if (valid) {
                $.post('/SupportTickets/add.json', $('#SupportTicketAddForm').serialize() )
                 .done(
                        function(data){
                            if (data.result !== undefined && data.result == 'ok'){
                                window.location.href = "/SupportTickets";
                           } else {
                            console.log(data.error);
                                if (data.error !== undefined){
                                    $('#SupportTicketAddForm').removeClass('success').addClass('error');
                                    $('#SupportTicketAddForm').form('add errors', data.error);
                                }
                                return false;
                           }
                        })
                 .fail( function(data, status, statusText) {
                    if (data.status == 401){
                        window.location.href = "/users/login";
                    } else {
                        $('#SupportTicketAddForm').removeClass('success').addClass('error');
                        answer = "HTTP Error: " + statusText;
                        self.errors.push(answer);
                        self.loading(false);
                    }
                 });
            }
        }.bind(this);

    };

    ko.cleanNode(document.getElementById("new_support_ticket_form"));
    ko.applyBindings(new newTicketViewModel(), document.getElementById("new_support_ticket_form"));


    $('#newTicketCloser').click(function(){
        $('#ticketsModal').modal('hide');
        return false;
    });

</script>

