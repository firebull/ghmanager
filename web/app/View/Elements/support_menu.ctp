<div class="ui fluid vertical menu">
	  <?php
	  	$action = $this->request->params['action'];

	  	$menu = array('index' => 'item',
	  				  'help' => 'item',
	  				  'faq' => 'item',
	  				  'contacts' => 'item',
	  				  'terms' => 'item'
	  				  );

	  	@$menu[$action] = 'blue item active';

        echo $this->Html->link('<i class="laptop icon"></i> '.__('Tickets'),
                               '/supportTickets',
                               array('escape' => false,
                                     'class' => $menu['index']));

        echo $this->Html->link('<i class="book icon"></i> '.__('Help'),
                               '/supports/help',
                               array('escape' => false,
                                     'class' => $menu['help']));

        echo $this->Html->link('<i class="lightbulb icon"></i> FAQ',
                               '/supports/faq',
                               array('escape' => false,
                                     'class' => $menu['faq']));

        echo $this->Html->link('<i class="chat outline icon"></i> '.__('Contacts'),
                               '/contacts',
                               array('escape' => false,
                                     'class' => $menu['contacts']));
  ?>

    <?php
    /*
    echo $this->Html->link('<i class="text file icon"></i> '.__('Terms and conditions'),
                               '/terms',
                               array('escape' => false,
                                     'class' => $menu['terms']));
	*/
    ?>
</div>
