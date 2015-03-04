
<div class="controls">
	<label class="radio" id="zoneOut" style="padding-left: 5px; width: 331px;">
		<input type="radio" <?php 
										if ($userBalance <= 0) {
										 	echo "checked=checked";
										 } 
									
								?> name="data[Order][payFrom]" value="out" id="personalAccNo"/>Нет

	</label>

	<label class="radio" id="zonePart" style="padding: 5px; width: 331px;">
	<input type="radio" <?php 
								if ($userBalance > 0){
									echo "checked=checked";
								}
								else
								{
								 	echo "disabled";
								 } 
							
						?> name="data[Order][payFrom]" value="part" id="personalAccPart"/>Частично списать со счёта
			
			<div id="personalAccPartPay" class="input-append"<?php
												if ($userBalance <= 0)
													{
													 	echo " style='display: none;' ";
													}
											?>>
			<input name="data[Order][partPayAmount]" id="personalAccPartAmount" value="" class="span1" size="6" style="text-align: center;" placeholder="0.00" /><span class="add-on">руб.</span> 
			</div>
	</label>

    <label class="radio" id="zoneFull" style="padding-left: 5px; width: 331px;">
		<input type="radio" <?php 
									if ($userBalance <= 0) {
									 	echo "disabled";
									 } 
								
							?> name="data[Order][payFrom]" value="full" id="personalAccFull"/>Полностью
	</label>

</div>

