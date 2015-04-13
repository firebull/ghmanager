<?php
/*
 * Created on 13.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
?>
<div id="orderPay">

<div class="ui ordered three steps" style="margin-bottom: 10px;">
    <div class="disabled step">
        <div class="content">
            <div class="title">Заказ</div>
            <div class="description">Выбор параметров сервера</div>
        </div>
    </div>
    <div class="active step">
        <div class="content">
            <div class="title">Оплата</div>
            <div class="description">Выбор способа оплаты</div>
        </div>
    </div>
    <div class="disabled step">
        <div class="content">
            <div class="title">Завершение</div>
        </div>
    </div>
</div>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<div class="ui small icon warning message">
    <i class="warning icon"></i>
    <div class="content">
        При оплате заказа вы соглашаетесь с <?php

            echo $this->Html->link('Договором оферты',
                             Configure::read('Links.oferta'),
                             array(
                                    'target' => '_blank',
                                    'style' => 'text-decoration: underline; color: #f00;'
                                  )
                             );

        ?>.
    </div>
</div>

<div class="ui small icon info message">
    <i class="info icon"></i>
    <div class="content">
        <p>Щелкните на выбранном способе оплаты, чтобы перейти на сайт платёжной системы для завершения процедуры.</p>
    </div>
</div>
<div class="ui grid">
    <div class="row">
        <div class="five wide column">
            <div class="ui vertical pointing menu">
                <a class="item" data-bind="event: {click: showGroup.bind(false, 1)}, css: {'red active': payGroup() == 1}">
                    Банковской картой
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 2)}, css: {'red active': payGroup() == 2}">
                    Электронными деньгами
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 3)}, css: {'red active': payGroup() == 3}">
                    Терминалы оплаты
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 4)}, css: {'red active': payGroup() == 4}">
                    Банковским платежом
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 5)}, css: {'red active': payGroup() == 5}">
                    Денежным переводом
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 6)}, css: {'red active': payGroup() == 6}">
                    Пункты приёма платежей
                </a>
                <a class="item" data-bind="event: {click: showGroup.bind(false, 7)}, css: {'red active': payGroup() == 7}">
                    Другие способы оплаты
                </a>
            </div>
        </div>
        <div class="eleven wide column">
            <div class="ui grid" data-bind="visible: payGroup() == 1">
                <?php  // ?>

                <div class="row">
                    <div class="three wide column">
                        <div class="platron_mid"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'platron',
                                                    'using' => 'TESTCARD',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>

                <?php // ?>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-h-logo-float-right"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'bankCard',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui small icon warning message" data-bind="visible: payGroup() == 2">
                <i class="warning icon"></i>
                <div class="content">
                    Внимание! После оплаты Яндекс.Деньгами вам нужно будет вручную вернуться в панель и обновить страницу с серверами или заказами! Иначе вы не увидите изменений сразу!
                </div>
            </div>
            <div class="ui grid" data-bind="visible: payGroup() == 2">
                <?php /* Из-за смены правил работы яндекса с магазинами, отключаю типовую форму ?>
                <div class="row">
                    <div class="three wide column">
                        <div class="yandex_money_small"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'yandex',
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>

                <?php */ ?>
                <div class="row">
                    <div class="three wide column">
                        <div class="yandex_money_small"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'yamoney', // Перевод напрямую на кошелёк
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-h-logo-float-left"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="webmoney_small"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'webmoney',
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="qiwi_mid"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">

                        <?php
                            echo $this->Html->tag('button',
                                                  'Оплатить',
                                                  ['onClick' => "$('#qiwiFormContainer_".$order['Order']['id']."').show(); $('#qiwiFormWallet_".$order['Order']['id']."').show('blind');",
                                                   'class' => 'ui primary fluid button']);
                        ?>
                    </div>
                </div>
                <div class="row" id="qiwiFormContainer_<?php echo $order['Order']['id'];?>" style="display: none;">
                    <div class="column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'formId' => 'Wallet',
                                                    'system' => 'qiwi',
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                ); ?>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-h-logo-float-left"></div>
                    </div>
                    <div class="nine wide column">
                        Множество других электронных платёжных систем, комиссия по тарифам обменного пункта, мгновенное зачисление.
                        <?php
                            echo $this->Html->link('Подробности...',
                                             'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=preferredexchangers',
                                             array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'exchangers',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui grid" data-bind="visible: payGroup() == 3">
                <div class="row">
                    <div class="three wide column">
                        <div class="qiwi_mid"></div>
                    </div>
                    <div class="nine wide column">
                        Комиссия 0%, мгновенное зачисление
                    </div>
                    <div class="four wide column">

                        <?php
                            echo $this->Html->tag('button',
                                                  'Оплатить',
                                                  ['onClick' => "$('#qiwiFormContainer2_".$order['Order']['id']."').show(); $('#qiwiFormTerm_".$order['Order']['id']."').show('blind');",
                                                   'class' => 'ui primary fluid button']);
                        ?>
                    </div>
                </div>
                <div class="row" id="qiwiFormContainer2_<?php echo $order['Order']['id'];?>" style="display: none;">
                    <div class="column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'formId' => 'Term',
                                                    'system' => 'qiwi',
                                                    'using' => 'inner',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                ); ?>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div style="float: left;" class="rw-icon rw-icon-5"></div>
                    </div>
                    <div class="nine wide column">
                        Через терминалы оплаты, комиссия <nobr>0-10%,</nobr> мгновенное зачисление
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=terminals&RN=',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'terminals',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-atm"></div>
                    </div>
                    <div class="nine wide column">
                        Через банкоматы банков-партнёров, комиссия <nobr>1-4%,</nobr> поступление средств от немедленного до 2-х часов.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=atm&RN=',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'terminals',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui grid"  data-bind="visible: payGroup() == 4">
                <div class="row">
                    <div class="three wide column">
                        <div class="sberbank_money_small"></div>
                    </div>
                    <div class="nine wide column">
                    Через Сбербанк, комиссия 3%, поступление средств через несколько дней.
                    </div>
                    <div class="four wide column">
                    <?php echo $this->Html->link('Оплатить', '#',
                                     array ('id'=>'sberbank_kvit_fill_link_'.$order['Order']['id'],
                                            'class' => 'ui fluid primary button',
                                            'title' => 'Кликните, чтобы распечатать квитанцию',
                                            'onClick'=>"$('#sberbank_kvit_fill_".$order['Order']['id']."').show();"));

                    $this->Js->get('#sberbank_kvit_fill_'.$order['Order']['id'])->effect('slideIn');?>
                    </div>
                </div>
                <!-- Заполнение полей квитанции начало -->
                <div class="row" id="sberbank_kvit_fill_<?php echo $order['Order']['id']; ?>" style="display: none;">
                    <div class="column">
                    <?php
                        echo $this->Form->create('Order', ['action' => 'payBySberbank',
                                                           'target' => '_blank',
                                                           'class' => 'ui form segment']);
                    ?>
                        <div class="ui small message">Пожалуйста, введите ФИО и адрес плательщика.
                               Эти данные на сервере не хранятся и будут уничтожены сразу после создания квитанции.
                               Вы можете не вводить эти данные здесь, а вписать их позже вручную.
                        </div>
                        <div class="field">
                            <label>ФИО: </label>
                        <?php
                        echo $this->Form->input('fio', [ 'id'    => 'fio',
                                                         'div'   => false,
                                                         'label' => false]);
                        ?>
                        </div>
                        <div class="field">
                            <label>Адрес: </label>
                        <?php
                        echo $this->Form->input('address', [ 'id'    => 'address',
                                                             'div'   => false,
                                                             'label' => false]);
                        ?>
                        </div>
                        <?php
                        echo $this->Form->input('id', array('type'=>'hidden','value'=>$order['Order']['id']));
                        echo $this->Form->submit('Печать квитанции',
                                    array('class' => 'ui fluid orange button'));

                        ?>
                    <?php
                        echo $this->Form->end();
                    ?>
                    </div>
                </div>
                <!-- Заполнение полей квитанции конец -->
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-bank"></div>
                    </div>
                    <div class="nine wide column">
                        Квитанция на оплату в любом банке, комиссия 2-5%, поступление средств через несколько дней.
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'bank',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-ibank"></div>
                    </div>
                    <div class="nine wide column">
                        Через интернет-банкинги банков-партнёров, комиссия <nobr>0-3%,</nobr> поступление средств от немедленного до нескольких дней.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=ibank&RN=',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'ibank',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui grid" data-bind="visible: payGroup() == 5">
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-3"></div>
                    </div>
                    <div class="nine wide column">
                        Денежным переводом, комиссия 0%, поступление средств занимает до нескольких часов.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=moneytransfer&RN=',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'transfers',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-4"></div>
                    </div>
                    <div class="nine wide column">
                        Почтовым переводом, комиссия 1,7%, поступление средств через несколько дней.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/client/InputpaymentPostRus.aspx',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'postRus',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui grid" data-bind="visible: payGroup() == 6">
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-mts"></div>
                    </div>
                    <div class="nine wide column">
                        В салонах связи МТС, комиссия 0%, мгновенное зачисление.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=mts',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'mts',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-euroset"></div>
                    </div>
                    <div class="nine wide column">
                        В салонах связи "Евросеть", комиссия 0%, мгновенное зачисление.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'http://www.rbkmoney.ru/popolnenie-elektronnogo-koshelka-v-salonakh-svyazi',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'euroset',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-icon rw-icon-svyaznoy"></div>
                    </div>
                    <div class="nine wide column">
                        В салонах связи "Связной", комиссия 0%, поступление средств в течение 2-х часов.
                        <?php
                        echo $this->Html->link('Подробности...',
                                         'http://www.rbkmoney.ru/popolnenie-elektronnogo-koshelka-v-salonakh-svyazi',
                                         array('target' => '_blank'));
                        ?>
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'svyaznoy',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="ui grid" data-bind="visible: payGroup() == 7">
                <div class="row">
                    <div class="three wide column">
                        <div class="rw-h-logo-float-left"></div>
                    </div>
                    <div class="nine wide column">
                        Выбрать способ оплаты самостоятельно на сайте RBK Money.
                    </div>
                    <div class="four wide column">
                        <?php echo $this->element('purchase_form',
                                              array(
                                                    'system' => 'rbk',
                                                    'using' => 'common',
                                                    'order' => $order,
                                                    'serverTemplate' => @$serverTemplate,
                                                    'paymentParams' => $paymentParams
                                                    )
                                                );?>
                        <button class="ui primary fluid button">Оплатить</button>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br/>

<?php
    echo $this->Form->create(['action' => 'index', 'class' => 'ui from']);
    echo $this->Form->submit('Оплатить позже',
                            array('class' => 'ui fluid orange button'));

    echo $this->Form->end();

    echo $this->Js->writeBuffer(); // Write cached scripts
?>
</div>
<script type="text/javascript">
    var orderPayViewModel = function(){

        var self = this;

        this.payGroup = ko.observable(1);

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();


        this.showGroup = function(group){
            this.payGroup(group);
        }.bind(this);
    };

    ko.cleanNode(document.getElementById("orderPay"));
    ko.applyBindings(new orderPayViewModel(), document.getElementById("orderPay"));
</script>
