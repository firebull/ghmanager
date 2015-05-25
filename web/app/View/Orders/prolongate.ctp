<?php
/*
 * Created on 09.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
//pr($this->data);
$privateTypeId = $this->data['ServerTemplateUser']['privateType'];
?>
<div id="prolongate">
<div class="ui ordered three steps" style="margin-bottom: 10px;">
    <div class="active step">
        <div class="content">
            <div class="title">Заказ</div>
        </div>
    </div>
    <div class="disabled step">
        <div class="content">
            <div class="title">Оплата</div>
        </div>
    </div>
    <div class="disabled step">
        <div class="content">
            <div class="title">Завершение</div>
        </div>
    </div>
</div>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php
    echo $this->Form->create('Order', array('class' => 'ui form'));
?>
    <div class="ui list">
        <div class="item">
        <?php
            echo $this->Html->image('icons/servers/'.$this->data['GameTemplate'][0]['name'].'.png',
                                    ['class' => 'ui image']);
        ?>
            <div class="content">
                <div class="header">
                    <?php
                        echo $this->data['GameTemplate'][0]['longname'];
                    ?>
                </div>
                <div class="description">
                    <?php echo $typeDiscount[$privateTypeId];?>
                </div>
            </div>
        </div>
        <div class="item">
            <i class="caret right icon" style="min-width: 28px;"></i>
            <div class="content">
                <div class="header">
                    Слотов:
                    <span data-bind="text: slots"></span>x<span data-bind="text: slotCost"></span><i class="ruble icon"></i>за слот
                </div>
            </div>
        </div>
        <div class="item">
            <i class="caret right icon" style="min-width: 28px;"></i>
            <div class="content">
                <div class="header">
                    Текущая аренда до
                    <?php echo $this->Common->niceDate($this->data['ServerTemplateUser']['payedTill']);?>
                </div>
            </div>
        </div>
        <div class="item" data-bind="visible: services">
            <i class="caret down icon" style="min-width: 28px;"></i>
            <div class="content">
                <div class="header">
                    Подключенные услуги
                </div>
                <div class="description">
                    (Для изменения списка услуг обратитесь в техподдержку)
                </div>
                <div class="list" data-bind="foreach: services">
                    <div class="item">
                        <i class="checkmark box icon"></i>
                        <div class="content">
                            <div class="header">
                                <span data-bind="text: longname"></span>
                                за <span data-bind="text: price"></span><i class="ruble icon"></i>в месяц
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="field">
        <label>Продлить аренду на:</label>
        <select data-bind="options: monthList,
                           optionsText: 'name',
                           optionsValue: 'number',
                           value: selectedMonths"
                name="data[Order][month]"></select>
    </div>
    <div class="two fields">
        <div class="field">
            <label>Промо-код (если есть):</label>
            <input name="data[PromoCode][code]" id="promoCode" type="text">
            <label id="promoCodeText"></label>
        </div>
        <div class="field">
            <label>&nbsp;</label>
            <button class="ui button" data-bind="event: {click: checkPromo}">Проверить</button>
        </div>
    </div>
    <div class="ui horizontal divider">Итог заказа</div>
    <table class="ui definition very basic table">
        <tbody>
            <tr class="positive">
                <td>Итого с учётом скидки:</td>
                <td>
                    <b data-bind="text: totalCost"></b><i class="ruble icon"></i>
                </td>
            </tr>
            <tr>
                <td style="width: 35%;">Итоговая скидка:</td>
                <td>
                    <span data-bind="text: totalDiscount"></span>% или
                    <span data-bind="text: discountSum"></span><i class="ruble icon"></i>
                </td>
            </tr>
            <tr>
                <td>Стоимость сервера:</td>
                <td>
                    <span data-bind="text: serverCost"></span><i class="ruble icon"></i>
                    <span data-bind="visible: servicesCost() > 0">
                        <br/>
                        <span>+ дополнительные услуги на</span>
                        <span data-bind="text: servicesCost"></span><i class="ruble icon"></i>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="ui horizontal divider">Оплата с лицевого счёта: доступно <span data-bind="text: balance"></span><i class="ruble icon"></i></div>
    <div class="ui tertiary segment">
        <div class="grouped inline fields">
            <div class="field">
                <div class="ui radio checkbox">
                  <input type="radio" name="data[Order][payFrom]" value="out" id="personalAccNo" checked="checked" data-bind="checked: personalAcc">
                  <label for="personalAccNo">Нет</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                  <input type="radio" name="data[Order][payFrom]" value="full" id="personalAccFull" data-bind="checked: personalAcc, attr: {'disabled': balance() < totalCost()}">
                  <label for="personalAccFull">Полностью</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                  <input type="radio" name="data[Order][payFrom]" value="part" id="personalAccPart" data-bind="checked: personalAcc,  attr: {'disabled': balance() <= 0}">
                  <label for="personalAccPart">Частично</label>
                </div>
            </div>
            <div class="one wide field">
                <input name="data[Order][partPayAmount]" id="personalAccPartAmount" placeholder="0.00" data-bind="attr: {'disabled': personalAcc() != 'part'}">
            </div>
        </div>
    </div>
    <button class="ui fluid primary button">Продлить</button>
    <?php echo $this->Form->end(); ?>

<script type="text/javascript">
    var prolongateViewModel = function(){

        var self = this;

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.server   = <?php echo @json_encode($this->data['ServerTemplateUser']);?>;
        this.template = <?php echo @json_encode($this->data['GameTemplate'][0]);?>;
        this.user     = <?php echo @json_encode($this->data['User'][0]);?>;
        this.services = ko.observableArray(<?php echo @json_encode($this->data['Service']);?>)

        this.discounts = <?php echo @json_encode($discount);?>;
        this.promoDiscount  = ko.observable(0);
        this.clientDiscount = ko.observable(<?php echo $userDiscount; ?>);
        this.discountSum    = ko.observable(0);
        this.balance        = ko.observable(Number(<?php echo $this->data['User'][0]['money']; ?>));
        this.personalAcc    = ko.observable('out');

        this.slots       = ko.observable(Number(this.server.slots));
        this.privateType = ko.observable(Number(this.server.privateType));
        this.selectedMonths    = ko.observable();

        this.slotCost = ko.pureComputed(function() {
            if (this.privateType() == 0){
                return this.template.price;
            } else if (this.privateType() == 1){
                return this.template.pricePrivatePassword;
            } else if (this.privateType() == 2){
                return this.template.pricePrivatePower;
            }

        }, this);

        this.serverCost = ko.pureComputed(function() {
            return Number(this.slots()) *
                   Number(this.slotCost()) *
                   Number(this.selectedMonths());
        }, this);

        this.servicesCost = ko.pureComputed(function() {
            var self = this;
            var serviceTotal = 0;

            $.each(self.services(), function(id, item){
                serviceTotal += Number(item.price);
            });

            return serviceTotal * Number(this.selectedMonths());
        }, this);

        this.monthList = ko.pureComputed(function() {
            var monthList = [];

            for (var i = 1; i <= 9; i++) {
                if (i < 3){
                    discount = '';
                }
                else if (i >= 3 && i < 6){
                    discount = ' (скидка ' + this.discounts['3'] + '%)';
                }
                else if (i >= 6 && i < 9){
                    discount = ' (скидка ' + this.discounts['6'] + '%)';
                }
                else if (i >= 9){
                    discount = ' (скидка ' + this.discounts['9'] + '%)';
                }

                monthList.push({'number': i, 'name': i + ' ' + getNumEnding(i, ['месяц', 'месяца', 'месяцев']) + discount});
            };

            return monthList;

        }, this);

        this.totalDiscount  = ko.pureComputed(function() {
            var month = this.selectedMonths();

            if (month < 3){
                monthDiscount = 0;
            }
            else if (month >= 3 && month < 6){
                monthDiscount = Number(this.discounts['3']);
            }
            else if (month >= 6 && month < 9){
                monthDiscount = Number(this.discounts['6']);
            }
            else if (month >= 9){
                monthDiscount = Number(this.discounts['9']);
            }

            return this.clientDiscount() + monthDiscount + self.promoDiscount();

        }, this);

        this.totalCost = ko.pureComputed(function() {
            var total = (this.serverCost() + this.servicesCost());
            var totalWithDiscount = (this.serverCost() + this.servicesCost()) * ((100 - this.totalDiscount())/100);

            this.discountSum((total - totalWithDiscount).toFixed(2));

            return totalWithDiscount.toFixed(2);

        }, this);

        this.checkPromo = function() {
            var self = this;
            var code = $('#promoCode').val();

            $.getJSON('/promos/checkCode/' + code + '.json', {},
                function(data) {
                    answer = data.result;
                    if(answer.discount !== undefined) {
                        self.promoDiscount(Number(answer.discount));
                        $('#promoCodeText').text('По коду ' + code + ' вам дана cкидка ' + answer.discount + '%');
                    } else {
                        $('#promoCodeText').text('Код недействителен');
                    }
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
             .always(function(){

            });
        }

        this.typeIcon = ko.pureComputed(function() {
            var typeIcons = {"1": "/img/icons/steam.png",
                             "2": "/img/icons/headphones.png",
                             "5": "/img/icons/steam.png",
                             "6": "/img/icons/punkbuster.png"};
            return typeIcons[this.selectedType()];
        }, this);

        this.templateIcon = ko.pureComputed(function() {
            var template = this.gameTemplatesById[this.selectedTemplate()];

            if (template !== undefined){
                return '/img/icons/servers/' + template.name + '.png';
            }
        }, this);

    };

    ko.cleanNode(document.getElementById("prolongate"));
    ko.applyBindings(new prolongateViewModel(), document.getElementById("prolongate"));

    /**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * @param  iNumber Integer Число на основе которого нужно сформировать окончание
     * @param  aEndings Array Массив слов или окончаний для чисел (1, 4, 5),
     *         например ['яблоко', 'яблока', 'яблок']
     * @return String
     */
    function getNumEnding(iNumber, aEndings)
    {
        var sEnding, i;
        iNumber = iNumber % 100;
        if (iNumber>=11 && iNumber<=19) {
            sEnding=aEndings[2];
        }
        else {
            i = iNumber % 10;
            switch (i)
            {
                case (1): sEnding = aEndings[0]; break;
                case (2):
                case (3):
                case (4): sEnding = aEndings[1]; break;
                default: sEnding = aEndings[2];
            }
        }
        return sEnding;
    }

</script>
