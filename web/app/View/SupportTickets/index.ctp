<?php
/*
 * Created on 15.04.2015
 *
 * Made fot project GH Mananger
 * by Nikita Bulaev
 */
?>
<div class="ui horizontally padded grid" id="tickets">
    <div class="row">
        <div class="three wide column">
            <?php
                echo $this->element('support_menu', []);
            ?>
            <button data-bind="event: {click: showModal.bind(false, 'fullscreen', '<?php echo __("Create new ticket");?>', '/SupportTickets/add')}" class="ui fluid orange button">
                <i class="help circle icon"></i>
                <?php echo __("Create new ticket");?>
            </button>
        </div>
        <div class="thirteen wide column">
            <div class="ui segment">
                <?php echo $this->Session->flash(); ?>
                <div class="ui message" data-bind="visible: openedTicketsList().length == 0 && closedTicketsList().length == 0"><?php echo __('No ticket yet');?></div>
                <div class="ui error message" data-bind="visible: errors().length > 0">
                    <div class="ui small header">Во время запроса произошли ошибки:</div>
                    <ul data-bind="foreach: {data: errors, as: 'error'}">
                        <li data-bind="text: error"></li>
                    </ul>
                </div>
                <div class="ui info message" data-bind="visible: infos().length > 0">
                    <ul data-bind="foreach: {data: infos, as: 'info'}">
                        <li data-bind="text: info"></li>
                    </ul>
                </div>
                <div class="ui divided equal height grid">
                    <div class="six wide column">
                        <div class="ui active inverted dimmer" data-bind="visible: loading">
                            <div class="ui text loader"><?php echo __("Loading tickets"); ?></div>
                        </div>
                        <div class="ui small dividing header" data-bind="visible: openedTicketsList().length > 0" style="margin-top: 0.165em;"><?php echo __('Opened tickets');?></div>
                        <div class="ui divided selection list" data-bind="visible: openedTicketsList().length > 0">
                            <!-- ko template: { name: 'tickets-list-item-template', foreach: openedTicketsList }-->

                            <!-- /ko -->
                        </div>

                        <div class="ui small dividing header" data-bind="visible: closedTicketsList().length > 0"><?php echo __('Closed tickets');?></div>
                        <div class="ui divided selection list" data-bind="visible: closedTicketsList().length > 0">
                            <!-- ko template: { name: 'tickets-list-item-template', foreach: closedTicketsList }-->

                            <!-- /ko -->
                        </div>
                    </div>
                    <div class="ten wide column">
                        <div class="ui active inverted dimmer" data-bind="visible: loadingTicket">
                            <div class="ui text loader"><?php echo __("Loading thread"); ?></div>
                        </div>
                    <!-- ko if: ticket().SupportTicket !== undefined -->
                        <div class="ui left floated header">
                            #<span  data-bind="text: ticket().SupportTicket.id"></span>,
                            <span  data-bind="text: ticket().SupportTicket.title"></span>
                        </div>
                        <div class="ui right floated tiny header">
                            <span  data-bind="text: moment(ticket().SupportTicket.created).format('HH:mm DD.MM.YY')"></span>
                        </div>
                        <div class="clear"></div>
                        <div class="ui small labels">
                            <div class="ui label" data-bind="css: {green: ticket().SupportTicket.status() == 'open', red: ticket().SupportTicket.status() == 'closed'}">
                                <i class="unlock alternate icon"></i>
                                <span data-bind="text: ticketStatus()"></span>
                            </div>
                            <div class="ui label">
                                <i class="mail outline icon"></i>
                                <span data-bind="text: ticket().SupportTicket.supports_count()"></span>
                                <?php echo __('messages');?>
                            </div>
                            <div class="ui red label" data-bind="visible: ticket().SupportTicket.unread_user_count() > 0">
                                <i class="mail icon"></i>
                                <span data-bind="text: ticket().SupportTicket.unread_user_count()"></span>
                                <?php echo __('unread');?>
                            </div>
                            <div class="ui label" data-bind="visible: ticket().SupportTicket.unread_user_count() == 0">
                                <i class="mail outline icon"></i>
                                <span>0</span>
                                <?php echo __('unread');?>
                            </div>
                        </div>
                        <!-- ko if: ticket().Server.length > 0 -->
                        <div class="ui clearing divider"></div>
                        <div class="ui list" data-bind="foreach: ticket().Server">
                            <div class="item">
                                <img class="ui image" data-bind="attr: {'src': '/img/icons/servers/' + $root.serverIcon(id)}"/>
                                <div class="content">
                                    ID <span data-bind="text: id"></span>:
                                    <span data-bind="text: $root.serverTemplate(id)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- /ko -->
                        <!-- ko if: $(ticket().Thread()).size() > 0 -->
                        <div class="ui comments">
                            <h3 class="ui dividing header"><?php echo __('Thread');?></h3>
                            <!-- ko foreach: ticket().Thread() -->
                            <div class="comment">
                                <div class="avatar">
                                    <img data-bind="attr: {'src': $root.messageUserpic($data)}"/>
                                </div>
                                <div class="content">
                                    <a class="author" data-bind="text: $root.messageAuthor($data)"></a>
                                    <div class="metadata">
                                        <span class="date" data-bind="text: moment(created).fromNow()"></span>
                                    </div>
                                    <div class="text" data-bind="text: text">
                                    </div>
                                </div>
                            </div>
                            <!-- /ko -->
                            <form class="ui reply form" id="replyForm">
                                <div class="field">
                                    <input type="hidden" name="data[SupportTicket][id]" id="SupportTicketId" data-bind="attr: {'value': ticket().SupportTicket.id}">
                                    <textarea name="data[Support][text]" id="replyText" placeholder="<?php echo __('Type your message here');?>"></textarea>
                                </div>
                                <div class="ui blue labeled submit icon button" data-bind="event: {click: sendReply.bind(false)}">
                                  <i class="icon edit"></i> <?php echo __('Add Reply');?>
                                </div>
                            </form>
                        </div>
                        <!-- /ko -->
                    <!-- /ko -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ui small modal" id="ticketsModal">
    <i class="close icon"></i>
    <div class="header"></div>
    <div class="content"><div class="description"></div></div>
    <div class="actions">
        <div class="ui button"><?php echo __('Cancel');?></div>
    </div>
</div>
<script type="text/html" id="tickets-list-item-template">
    <div class="item" data-bind="event: {click: $root.showTicket.bind($data)}, css: {'active': SupportTicket.id == $root.ticketId()}">
        <div class="ui right floated label" data-bind="css: {'red': Number(SupportTicket.unread_user_count()) > 0}">
            <i class="mail icon"></i>
            <span data-bind="text: SupportTicket.unread_user_count"></span>
        </div>
        <div class="content">
            <div class="header">
                #<span data-bind="text: SupportTicket.id"></span>:
                <span data-bind="text: SupportTicket.title"></span>
            </div>
            <div class="description">
                <span data-bind="text: moment(SupportTicket.modified).fromNow()"></span>
            </div>
        </div>
    </div>
</script>
<script type="text/javascript" language="javascript">
        moment.locale('ru');

        var ticketsViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.loadingTicket = ko.observable(false);
        this.errors  = ko.observableArray();
        this.infos = ko.observableArray();

        this.ticketId = ko.observable(false);
        this.ticket   = ko.observableArray();
        this.openedTicketsList = ko.observableArray();
        this.closedTicketsList = ko.observableArray();
        this.serversTemplates = [];

        this.showTicket = function(data){
            var self = this;
            var newData = data;

            newData['Thread'] = ko.observableArray();

            self.ticketId(data.SupportTicket.id);
            self.ticket(newData);
            self.getThread();

        }.bind(this);

        this.getThread = function(){
            var self = this;
            var url = '/SupportTickets/view/' + self.ticket().SupportTicket.id + '/.json';

            self.loadingTicket(true);

            $.get( url )
             .done(
                    function(answer){
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            if (answer.ticketStatus !== undefined
                                    && answer.ticketStatus == 'closed'
                                    && self.ticket().SupportTicket.status() == 'open'){
                                self.openedTicketsList.remove(data);
                                self.ticket().SupportTicket.status('closed');
                                self.closedTicketsList.unshift(self.ticket());
                            }

                            $.each(answer.thread, function(id, item){
                                self.ticket().Thread.push(item);
                            });


                        }

                        self.loadingTicket(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loadingTicket(false);
                }
             });

        }.bind(this);

        this.sendReply = function(){
            var self = this;

            self.loadingTicket(true);

            $.post('/supports/add.json', $('#replyForm').serialize() )
             .done(
                    function(data){
                        answer = data.result;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            if (answer.info !== undefined) {
                                if ($.isArray(answer.info)){
                                    self.infos(answer.info);
                                } else {
                                    self.infos.push(answer.info);
                                }
                            }

                            if (answer.message !== undefined){
                                self.ticket().Thread.push(answer.message);
                            }

                            $('#replyText').val('');
                        }

                        self.loadingTicket(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loadingTicket(false);
                }
             });

        }.bind(this);

        this.showModal = function(size, title, bodyUrl, data){
            var self = this;

            $('#ticketsModal').removeClass('small large fullscreen').addClass(size);
            $('#ticketsModal .header').html(title);


            self.loading(true);

            $.get( bodyUrl )
             .done(
                    function(data){
                        $('#ticketsModal .content .description').empty();
                        $('#ticketsModal .content .description').html(data);
                        $('#ticketsModal').modal({onHidden: function(){
                            $('#ticketsModal .header').empty();
                            $('#ticketsModal .content .description').empty();
                        }}).modal('show').modal('refresh');

                        self.loading(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                }
             });

        }.bind(this);

        this.ticketStatus = function(){
            return this.ticket().SupportTicket.status() == 'open' ? '<?php echo __("Open");?>' : '<?php echo __("Closed");?>';
        }

        this.serverIcon = function(id){
            return this.serversTemplates[id]['name'] + '.png';
        }

        this.serverTemplate = function(id){
            return this.serversTemplates[id]['longname'];
        }

        this.messageUserpic = function(item){
            if (item.answerBy == 'support'){
                 return '/img/avatars/personage_184px.png';
            } else {
                return '/img/avatars/user-common.svg';
            }
        }

        this.messageAuthor = function(item){
            if (item.answerBy == 'support'){
                 return '<?php echo __("Tech support");?>';
            } else {
                return '<?php echo __("You");?>';
            }
        }

        this.loadData = function(){
            var self = this;

            $.getJSON('/SupportTickets/loadTickets.json')
             .done( function(answer){
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            if (answer.tickets !== undefined && $.isArray(answer.tickets)){
                                var opened = [];
                                var closed = [];
                                $.each(answer.tickets, function(id, ticket){

                                    ticket.SupportTicket.status = ko.observable(ticket.SupportTicket.status);
                                    ticket.SupportTicket.supports_count = ko.observable(Number(ticket.SupportTicket.supports_count));
                                    ticket.SupportTicket.unread_user_count = ko.observable(Number(ticket.SupportTicket.unread_user_count));

                                    if (ticket.SupportTicket.status() == 'open'){
                                        self.openedTicketsList.push(ticket);
                                    } else {
                                        self.closedTicketsList.push(ticket);
                                    }
                                });
                            }

                            if (answer.serversTemplates !== undefined){
                                self.serversTemplates = answer.serversTemplates;
                            }
                        }
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                }
             })
             .always(function(){
                self.loading(false);
                $('#maps').attr('style', '')
                $('#indexModal').modal('show');
                //$('.popup-titles').popup({inline: true, position: 'bottom left'});
             });
        }.bind(this);

        this.loadData();

    };

    ko.applyBindings(new ticketsViewModel(), document.getElementById("tickets"));

</script>
<?php
            echo $this->Js->writeBuffer(); // Write cached scripts
?>
