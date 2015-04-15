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
            <div class="ui grid">
    		    <div class="six wide column">
                    <div class="ui small dividing header" data-bind="visible: openedTicketsList().length > 0"><?php echo __('Opened tickets');?></div>
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
    <div class=" item">
        <div class="ui right floated label" data-bind="css: {'red': Number(SupportTicket.unread_user_count) > 0}">
            <i class="mail icon"></i>
            <span data-bind="text: SupportTicket.unread_user_count"></span>
        </div>
        <div class="content">
            <div class="header">
                #<span data-bind="text: SupportTicket.id"></span>:
                <span data-bind="text: SupportTicket.title"></span>
            </div>
            <div class="description">
                <span  data-bind="text: moment(SupportTicket.modified).fromNow()"></span>
            </div>
        </div>
    </div>
</script>
<script type="text/javascript" language="javascript">
        moment.locale('ru');

		var ticketsViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.openedTicketsList = ko.observableArray();
        this.closedTicketsList = ko.observableArray();

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
                                    if (ticket.SupportTicket.status() == 'open'){
                                    	self.openedTicketsList.push(ticket);
                                    } else {
                                    	self.closedTicketsList.push(ticket);
                                    }
                                });
                            }
                        }

                        console.log(self.openedTicketsList());
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
