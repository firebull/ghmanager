<?php

?>
<div id="orderCreate">
<?php
	echo $this->Form->create('Order', array('class' => 'ui form'));
?>
	<div class="field">
		<select data-bind="options: locations,
		                   optionsText: function(item) {
		                       return item.name + ' (' + item.collocation + ')'
		                   },
		                   optionsValue: 'id',
		                   value: selectedLocation"></select>
	</div>
	<div class="field">
		<select data-bind="options: types,
		                   optionsText: 'longname',
		                   optionsValue: 'id',
		                   value: selectedType"></select>
	</div>
	<div class="field">
		<select data-bind="options: gameTemplatesList,
		                   optionsText: 'longname',
		                   optionsValue: 'id',
		                   value: selectedTemplate"></select>
	</div>


<?php echo $this->Form->end(); ?>
</div>

<?php
	$typeDiscount = [ ['id' => 0, 'name' => 'Публичный сервер'],
					  ['id' => 1, 'name' => 'Приватный с паролем'],
					  ['id' => 2, 'name' => 'Приватный с автоотключением']];

?>
<script type="text/javascript">
	var orderCreateViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.locations = ko.observableArray(<?php echo @json_encode($locationsList);?>);
        this.selectedLocation = ko.observable();

        this.types        = ko.observableArray(<?php echo @json_encode($typesList);?>);
        this.selectedType = ko.observable();

        this.gameTemplates = <?php echo @json_encode($gameTemplates);?>;
        this.selectedTemplate = ko.observable();

        this.typesDiscount = ko.observableArray(<?php echo @json_encode($typeDiscount);?>);

        this.gameTemplatesList = ko.pureComputed(function() {


		    return this.gameTemplates[this.selectedType()];
		}, this);

    };

    ko.cleanNode(document.getElementById("orderCreate"));
    ko.applyBindings(new orderCreateViewModel(), document.getElementById("orderCreate"));
</script>
