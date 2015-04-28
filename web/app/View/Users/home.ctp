<?php
/*
 * Created on 28.04.2015
 *
 * Made fot project GH Mananger
 * by Nikita Bulaev
 */
?>
<div id="userHomepage" style="margin-top: 20px;">
    <div class="ui active inverted dimmer" data-bind="visible: loading">
        <div class="ui text loader"><?php echo __('Loading data');?></div>
    </div>
    <div class="ui vertically divided stackable page grid">

        <div id="flash"><?php echo $this->Session->flash(); ?></div>
        <div class="two white column equal height divided row">
            <div class=" column">
                <div class="ui left floated header">
                    <?php echo __('Your servers');?>
                </div>
                <a class="ui right floated icon button" href="/servers">
                    <?php echo __('Proceed');?>
                    <i class="level up icon"></i>
                </a>
                <div class="clear"></div>
                <table class="ui very basic table" data-bind="visible: servers().length > 0">
                    <tbody data-bind="foreach: {data: servers, as: 'item'}">
                        <tr data-bind="css: $root.serverStatusClass($data)">
                            <td data-bind="text: '#' + Server.id"></td>
                            <td>
                                <img data-bind="attr: {'src': '/img/icons/servers/' + GameTemplate.name + '.png'}"/>
                            </td>
                            <td data-bind="text: $root.serverName($data)"></td>
                            <td data-bind="text: $root.serverStatus($data)"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="ui segment" data-bind="visible: servers().length <= 0">
                    <?php echo __('You do not have servers yet. Click "Order new server" in top panel.');?>
                </div>
            </div>
            <div class=" column">
                <div class="ui left floated header">
                    <?php echo __('Your opened tickets');?>
                </div>
                <a class="ui right floated icon button" href="/supportTickets">
                    <?php echo __('Proceed');?>
                    <i class="level up icon"></i>
                </a>
                <div class="clear"></div>
                <div class="ui divided selection list" data-bind="visible: tickets().length > 0, foreach: tickets">
                    <div class="item" data-bind="">

                        <div class="ui right floated label" data-bind="css: {'red': Number(unread_user_count > 0}">
                            <i class="mail icon"></i>
                            <span data-bind="text: unread_user_count"></span>
                        </div>
                        <div class="ui right floated label">
                            <i class="mail outline icon"></i>
                            <span data-bind="text: supports_count"></span>
                        </div>
                        <div class="content">
                            <div class="header">
                                #<span data-bind="text: id"></span>:
                                <span data-bind="text: title"></span>
                            </div>
                            <div class="description">
                                <span data-bind="text: moment(modified).fromNow()"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ui segment" data-bind="visible: tickets().length <= 0">
                    <?php echo __('You do not have opened tickets.');?>
                </div>
            </div>
        </div>
        <div class="two white column equal height divided row">
            <div class=" column">
                <div class="ui left floated header">
                    <?php echo __('Your last orders');?>
                </div>
                <a class="ui right floated icon button" href="/orders">
                    <?php echo __('Proceed');?>
                    <i class="level up icon"></i>
                </a>
                <div class="clear"></div>
                <table class="ui very basic table" data-bind="visible: orders().length > 0">
                    <tbody data-bind="foreach: {data: orders, as: 'order'}">
                        <tr data-bind="css: {'positive': payed == 1, 'warning': payed == 0}">
                            <td data-bind="text: '#' + id"></td>
                            <td data-bind="text: moment(created).format('DD.MM.YYYY')"></td>
                            <td data-bind="text: $root.orderTarget($data)"></td>
                            <td data-bind="text: $root.orderStatus($data)"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="ui segment" data-bind="visible: orders().length <= 0">
                    <?php echo __('You do not have created orders yet.');?>
                </div>
            </div>
            <div class="column">
                <div class="ui left floated header">
                    <?php echo __('Last news');?>
                </div>
                <?php
                    echo $this->Html->link(__('All news'). ' <i class="level up icon"></i>',
                                           Configure::read('Links.blog'),
                                           ['escape' => false,
                                            'class' => 'ui right floated icon button',
                                            'target' => '_blank']);
                ?>
                <div class="clear"></div>
                <div class="ui feed" data-bind="visible: news().length > 0, foreach: news">
                    <div class="event">
                        <div class="content">
                            <div class="date" data-bind="text: moment(date).format('DD.MM.YYYY hh:mm')"></div>
                            <a class="summary" data-bind="text: title, attr: {'href': link}" target="_blank"></a>
                        </div>
                    </div>
                </div>
                <div class="ui segment" data-bind="visible: news().length <= 0">
                    <?php echo __('No news yet.');?>
                </div>
            </div>
        </div>
        <div class="two white column equal height divided row">
            <div class=" column">
                <div class="ui left floated header">
                    <?php echo __('Last actions log');?>
                </div>
                <a class="ui right floated icon button" href="/actionLog">
                    <?php echo __('Show all');?>
                    <i class="level up icon"></i>
                </a>
                <div class="clear"></div>
                <table class="ui very basic table" data-bind="visible: actions().length > 0">
                    <tbody data-bind="foreach: {data: actions, as: 'action'}">
                        <tr data-bind="css: {'negative': status == 'error'}">
                            <td data-bind="text: '#' + ('000000000' + id).substr(-7);"></td>
                            <td data-bind="text: action"></td>
                            <td data-bind="text: moment(created).format('DD.MM.YYYY hh:mm:ss')"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="ui segment" data-bind="visible: actions().length <= 0">
                    <?php echo __('Action log is empty.');?>
                </div>
            </div>
            <div class=" column">
                <div class="ui header">
                    <?php echo __('Last attacks log');?>
                </div>

                <div class="ui segment" data-bind="visible: attacks().length <= 0">
                    <?php echo __('Attacks log is empty.');?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    moment.locale('ru'); // TODO: Set global locale

    var userHomepageViewModel = function(){
        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.servers = ko.observableArray();
        this.orders  = ko.observableArray();
        this.tickets = ko.observableArray();
        this.actions = ko.observableArray();
        this.attacks = ko.observableArray();
        this.news    = ko.observableArray();

        this.serverName = function(item){

            if (item.Server.name == null || item.Server.name == undefined){
                return item.GameTemplate.longname;
            } else {
                return item.Server.name;
            }

        }.bind(this);

        this.serverStatus = function(item){

            var Server = item['Server'];

            if (Server.status == 'stoped'
                    || Server.status == 'stopped'
                    || Server.status == null) {
                return '<?php echo __("Stopped");?>';
            } else if (Server.status == 'update_started') {
                return '<?php echo __("Updating");?>';
            } else if (Server.status == 'update_error' || Server.status == 'exec_error') {
                return '<?php echo __("Error");?>';
            } else if (Server.status == 'exec_success') {
                if (item.state !== undefined && item.state.error !== undefined) {
                    if (moment().diff(Server.statusTime, 'minutes', true) < 5){
                        return '<?php echo __("Starting");?>';
                    } else {
                        return '<?php echo __("Error");?>';
                    }
                } else {
                    return '<?php echo __("Working");?>';
                }
            } else {
                return '<?php echo __("Unknown");?>'
            }

        };

        this.serverStatusClass = function(item){

            var Server = item['Server'];

            if (Server.status == 'stoped' || Server.status == 'stopped') {
                return '';
            } else if (Server.status == 'update_started') {
                return 'warning';
            } else if (Server.status == 'update_error' || Server.status == 'exec_error') {
                return 'negative';
            } else if (Server.status == 'exec_success') {
                if (item.state !== undefined && item.state.error !== undefined) {
                    if (moment().diff(Server.statusTime, 'minutes', true) < 5){
                        return 'warning';
                    } else {
                        return 'negative';
                    }
                } else {
                    return 'positive';
                }
            } else {
                return ''
            }

        };

        this.orderTarget = function(Order){
            if (Order.month === null || Number(Order.month) == 0){
                return '<?php echo __("Personal balance refill");?>';
            } else {
                return '<?php echo __("Server rent");?>';
            }
        }.bind(this);

        this.orderStatus = function(Order){
            if (Order.payed == 1){
                return '<?php echo __("Payed");?>';
            } else {
                return '<?php echo __("Unpayed");?>';
            }
        }.bind(this);

        this.loadData = function(){
            var self = this;
            self.loading(true);

            $.getJSON('/Users/homeData.json')
             .done( function(data){
                        answer = data['data'];

                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            if (answer.Servers !== undefined && $.isArray(answer.Servers)){
                                self.servers(answer.Servers);
                            }

                            if (answer.Orders !== undefined && $.isArray(answer.Orders)){
                                self.orders(answer.Orders);
                            }

                            if (answer.Tickets !== undefined && $.isArray(answer.Tickets)){
                                self.tickets(answer.Tickets);
                            }

                            if (answer.Actions !== undefined && $.isArray(answer.Actions)){
                                self.actions(answer.Actions);
                            }

                            if (answer.News !== undefined && $.isArray(answer.News)){
                                self.news(answer.News);
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

             });
        }.bind(this);

        this.loadData();

    };

    ko.applyBindings(new userHomepageViewModel(), document.getElementById("userHomepage"));
</script>
