		<div class="ui grid">
			<div class="row">
				<div class="one wide column"></div>
				<div class="fourteen wide column">
					<div class="ui success message" id="successMessage">
						<i class="close icon"></i>
						<div class="header">
							<?php echo h($message); ?>
						</div>
					</div>
				</div>
				<div class="one wide column"></div>
			</div>
		</div>
		<br/>
<script type="text/javascript">
	$('#successMessage').on('click', function() {
	  $(this).closest('.message').fadeOut();
	});
</script>
