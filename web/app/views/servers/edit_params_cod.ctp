<?php
/*
 * Created on 12.03.2012
 *
 * File created for project TeamServer
 * by nikita
 */
 include('../loading_params.php');
 $id = $this->data['Server']['id'];
?>
<div id="server_params">
	<div id="flash"><?php echo $session->flash(); ?></div>

<div style="height: 100%; background-color: #ddd; border-radius: 3px;">

<div style="position: relative;">
<?php

if ($configsOwner == 'server')
{
    $headerClass = "a-menu-header a-menu-btn-active";
}
else
{
    $headerClass = "a-menu-header";
}

echo $html -> link( "<span class=a-menu-btn-text><strong>Серверные конфиги: </strong>".
								 	"Основные настройки сервера</span>".
                                    "<span class=a-menu-header-icon-left id='icon_server'><span></span></span>".
								 	"<span class=a-menu-header-loading id='loading_configs_server'><span></span></span>", '#',
									array ('id'=>'configs_server',
									'escape' => false,
									'class' => $headerClass

									));
$effect = $js->get('#server_params_container')->effect('slideIn');      
$event  = $js->request(array('controller'=>'servers',
                             'action'=>'editParamsSrcds',
                             $id,
                             'server'), 
                       array('update' => '#server_params_container',      
                             'before' => "$('#icon_server').hide();".
                                         "$('#loading_configs_server').show();".
                                         "$('.accordionBody').hide('blind');",
                             //'complete'=>$loadingHide
                             ));

$js->get('#configs_server')->event('click', $event);

?>
</div>
<?php
if ($configsOwner == 'server'){ ?>
			<div id="server" class="accordionBody" style="display: block; margin-left: 5px; margin-right: 5px; margin-top: 0px; min-height: 200px; height: 100%;  position: relative;">
				<?php echo $this->element('config_menu', array( 'id' => $id, 'configs' => $configs, 'owner' => 'server')); ?>
				<div id="configEditor">
					<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
						<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
						Выберите конфиг для редактирования слева или другой мод/плагин из общего списка.
						</p>
					</div>
				</div>
			</div>
<?php		}
?>

<!-- Список модов -->
<?php if (!empty($server['Mod'])){
			foreach ( $server['Mod'] as $mod ) {

                // Сначала надо подготовить список пользовательских модов,
                // которых нет у нас. Для этого из списка найденых модов
                // в директории сервера, удаляем моды, установленные
                // из панели.
                
                if (!empty($mods)){
                    $userModId = array_search($mod['name'], $mods);
                    if ($userModId !== false){
                        unset($mods[$userModId]);   
                    }
                }

				if (!empty($mod['Config'])){
				
                    if ($configsOwner == $mod['name'])
                    {
                        $headerClass = "a-menu-header a-menu-btn-active";
                    }
                    else
                    {
                        $headerClass = "a-menu-header";
                    }
                    ?>
                    <div style="position: relative;">
                    <?php
                    echo $html -> link( "<span class=a-menu-btn-text><strong>".$mod['longname'].": </strong>".
                                    "".@$mod['shortDescription']."&nbsp</span>".
                                    "<span class=a-menu-header-icon-left id='icon_mod_".$mod['id']."'><span></span></span>".
                                    "<span class=a-menu-header-loading id='loading_mod_".$mod['id']."'><span></span></span>", '#',
                                    array ('id'=>'configs_mod_'.$mod['id'],
                                    'escape' => false,
                                    'class' => $headerClass

                                    ));

                    $effect = $js->get('#server_params_container')->effect('slideIn');      
                    $event  = $js->request(array('controller'=>'servers',
                                                 'action'=>'editParamsSrcds',
                                                 $id,
                                                 'mod',
                                                 $mod['id']), 
                                           array('update' => '#server_params_container',      
                                                 'before' => "$('#icon_mod_".$mod['id']."').hide();".
                                                             "$('#loading_mod_".$mod['id']."').show();".
                                                             "$('.accordionBody').hide('blind');",
                                                 //'complete'=>$loadingHide
                                                 ));
            
                    $js->get('#configs_mod_'.$mod['id'])->event('click', $event);

                ?>
                    </div>
				<?php if ($configsOwner == $mod['name']) { ?>
				<div id="mod_<?php echo $mod['id']; ?>" class="accordionBody" style="display: block; margin-left: 5px; margin-right: 5px; margin-top: 0px; min-height: 200px; height: 100%; position: relative;">
					<?php echo $this->element('config_menu', array( 'id' => $id, 'configs' => $configs, 'owner' => 'mod_'.$mod['id'])); ?>
					<div id="configEditor">
						<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
							<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
							Выберите конфиг для редактирования слева или другой мод из общего списка.
							</p>
						</div>
				</div>
				</div>

				<?php } ?>

<?php 			}
			}
		}
?>

<!-- Список клиентских модов -->
<?php if (!empty($mods)){
            foreach ( $mods as $userMod ) {
                if (!empty($userMod)){
                
                    if ($configsOwner == $userMod)
                    {
                        $headerClass = "a-menu-header a-menu-btn-active";
                    }
                    else
                    {
                        $headerClass = "a-menu-header";
                    }
                    ?>
                    <div style="position: relative;">
                    <?php
                    echo $html -> link( "<span class=a-menu-btn-text><strong>".ucfirst($userMod)."</strong></span>".
                                    "<span class=a-menu-header-icon-left id='icon_mod_".$userMod."'><span></span></span>".
                                    "<span class=a-menu-header-loading id='loading_mod_".$userMod."'><span></span></span>", '#',
                                    array ('id'=>'configs_mod_'.$userMod,
                                    'escape' => false,
                                    'class' => $headerClass

                                    ));

                    $effect = $js->get('#server_params_container')->effect('slideIn');      
                    $event  = $js->request(array('controller'=>'servers',
                                                 'action'=>'editParamsSrcds',
                                                 $id,
                                                 'userMod',
                                                  $userMod), 
                                           array('update' => '#server_params_container',      
                                                 'before' => "$('#icon_mod_".$userMod."').hide();".
                                                             "$('#loading_mod_".$userMod."').show();".
                                                             "$('.accordionBody').hide('blind');",
                                                 //'complete'=>$loadingHide
                                                 ));
            
                    $js->get('#configs_mod_'.$userMod)->event('click', $event);

                ?>
                    </div>
                <?php if ($configsOwner == $userMod) { ?>
                <div id="mod_<?php echo $userMod; ?>" class="accordionBody" style="display: block; margin-left: 5px; margin-right: 5px; margin-top: 0px; min-height: 200px; height: 100%; position: relative;">
                    <?php echo $this->element('config_menu', array( 'id' => $id, 'configs' => $configs, 'owner' => 'mod_'.$userMod)); ?>
                    <div id="configEditor">
                        <div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
                            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                            Выберите конфиг для редактирования слева или другой мод из общего списка.
                            </p>
                        </div>
                </div>
                </div>

                <?php } ?>

<?php           }
            }
        }
?>

</div>
<?php
	echo $js->writeBuffer(); // Write cached scripts
?>

</div>
