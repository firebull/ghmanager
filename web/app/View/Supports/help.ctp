<div class="ui padded grid">
    <div class="three wide column">
        <?php
            echo $this->element('support_menu', []);
        ?>
    </div>
    <div class="thirteen wide column">
        <div class="ui segment">
			<div class="ui dividing header"><?php echo __('Help');?></div>
		<?php
			if (!empty($helps)){
		?>
			<div class="ui items">
		<?php
				foreach ($helps as $help) {
		?>
				<div class="item">
					<div class="content">
						<div class="header">
							<?php echo $help['Help']['title'];?>
						</div>
						<div class="description" style="max-width: 100% !important;">
							<?php echo @$this->Markdown->parse($help['Help']['text']);?>
						</div>
					</div>
				</div>
		<?php
				}
		?>
			</div>
		<?php
			}
		?>
        </div>
    </div>
</div>
