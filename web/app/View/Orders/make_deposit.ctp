<?php

 include('loading_params.php');

?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<div class="ui active inverted dimmer" id="depositLoader" style="display: none;">
	<div class="ui text loader">Loading</div>
</div>
<div class="ui message">
Укажите сумму, на которую вы бы хотели пополнить баланс Личного счёта
</div>
<?php echo $this->Form->create('Order', ['class' => 'ui form', 'id' => 'depositForm']); ?>
<div class="two fields">
    <div class="field">
    	<div class="ui right labeled input">
		<?php

			echo $this->Form->input('amount', [ 'div'   => false,
										   		'label' => false,
										   		'id'    => 'depositAmount',
										   		'placeholder' => 100]);
		?>
			<div class="label"><i class="ruble icon"></i></div>
		</div>
	</div>
	<div class="field">
		<div class="ui fluid primary button disabled" id="depositButton">Пополнить</div>
	</div>
</div>
<div>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">

	$(function() {

			$('#depositButton').click(function(){

				$('#depositLoader').show();

				$.post( '/orders/makeDeposit', $('#depositForm').serialize() )
	             .done(
	                    function(data){
	                    	$('#topMenuModal .header').html('Выбрать способ оплаты');
	                        $('#topMenuModal').removeClass('small large fullscreen').addClass('large');
	                        $('#topMenuModal .content .description').html(data);


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
	             })
	             .always( function(){
	             	$('#depositLoader').hide();
	             });

			});

			function depositCheckAmount()
			{
				var amount = Number($('#depositAmount').val());

				if (amount > 0)
				{
					$('#depositButton').removeClass('disabled');
				}
				else
				{
					$('#depositButton').addClass('disabled');
				}

			}

			$("#depositAmount").keyup(function() {
							depositCheckAmount();
						});

			depositCheckAmount();

			});
</script>
