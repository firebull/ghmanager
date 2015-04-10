<?php

?>
<div id="orderCreate">
<div class="ui ordered three steps" style="margin-bottom: 10px;">
    <div class="active step">
        <div class="content">
            <div class="title">Заказ</div>
            <div class="description">Выбрать параметры сервера</div>
        </div>
    </div>
    <div class="disabled step">
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
<?php
    echo $this->Form->create('Order', array('class' => 'ui form'));
?>
    <div class="two fields">
        <div class="field">
            <label>Локация</label>
            <div class="ui labeled input">
                <div class="ui label">
                    <img src="/img/icons/personage01_21px.png"/>
                </div>
                <select data-bind="options: locations,
                                   optionsText: function(item) {
                                       return item.name + ' (' + item.collocation + ')'
                                   },
                                   optionsValue: 'id',
                                   value: selectedLocation"
                        name="data[Location][id]"></select>
            </div>
        </div>
        <div class="field">
            <label>Вид</label>
            <div class="ui labeled input">
                <div class="ui label">
                    <img data-bind="attr: {'src': typeIcon}" src="/img/icons/personage01_21px.png"/>
                </div>
                <select data-bind="options: types,
                                   optionsText: 'longname',
                                   optionsValue: 'id',
                                   value: selectedType"
                        name="data[Type][id]"></select>
            </div>
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label>Сервер</label>
            <div class="ui labeled input">
                <div class="ui label">
                    <img data-bind="attr: {'src': templateIcon}" src="/img/icons/personage01_21px.png"/>
                </div>
                <select data-bind="options: gameTemplatesList,
                                   optionsText: 'longname',
                                   optionsValue: 'id',
                                   value: selectedTemplate"
                        name="data[GameTemplate][id]"></select>
            </div>
        </div>
        <div class="field">
            <label>Тип</label>
            <div class="ui labeled input">
                <div class="ui label">
                    <img src="/img/icons/personage01_21px.png"/>
                </div>
                <select data-bind="options: publicTypeList,
                                   optionsText: 'name',
                                   optionsValue: 'id',
                                   value: selectedPublicType"
                        name="data[Server][privateType]"></select>
            </div>
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label>Слотов:</label>
            <div class="ui right labeled input">
                <select data-bind="options: slotsSelectList,
                                   optionsText: 'name',
                                   optionsValue: 'number',
                                   value: selectedSlots"
                        name="data[Order][slots]"></select>
                <div class="ui label" style="min-width: 50%;">
                    x<span data-bind="text: slotPrice"></span> <i class="ruble icon"></i>за слот
                </div>
            </div>
        </div>
        <div class="field">
            <label>Срок оплаты:</label>
            <select data-bind="options: monthList,
                               optionsText: 'name',
                               optionsValue: 'number',
                               value: selectedMonths"
                    name="data[Order][month]"></select>
        </div>
    </div>
    <div class="ui tertiary segment">
        <div class="ui tiny header">Дополнительные услуги</div>
        <!-- ko foreach: servicesList -->
        <div class="field">
            <div class="ui toggle checkbox">
                <input type="checkbox" data-bind="attr: {'name': 'data[Service][' + $index() + '][id]', 'id': 'service_' + $index(), 'value': $index()}, checked: $root.selectedServices, checkedValue: id">
                <label data-bind="attr: {'for': 'service_' + $index()}">
                    <span data-bind='text: longname'></span> за <span data-bind="text: price"></span><i class="ruble icon"></i>
                </label>
            </div>
            <a style="cursor: pointer;" data-bind="visible: $root.showServiceDesc() != id, event: {click: $root.serviceDescAction.bind($data)}">Подробнее <i class="caret right icon"></i></a>
            <a style="cursor: pointer;" data-bind="visible: $root.showServiceDesc() == id, event: {click: $root.serviceDescAction.bind($data)}">Скрыть <i class="caret down icon"></i></a>
            <div data-bind="visible: $root.showServiceDesc() == id">
                <div class="ui clearing divider"></div>
                <small data-bind="html: desc"></small>
            </div>
        </div>
    <!-- /ko -->
    </div>
    <div class="three fields">
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
                <td>Стоимость сервера в месяц:</td>
                <td>
                    <span data-bind="text: serverCost"></span><i class="ruble icon"></i>
                    <span data-bind="visible: servicesCost() > 0">
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
                  <input type="radio" name="data[Order][payFrom]" value="full" id="personalAccFull" data-bind="checked: personalAcc">
                  <label for="personalAccFull">Полностью</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                  <input type="radio" name="data[Order][payFrom]" value="part" id="personalAccPart" data-bind="checked: personalAcc">
                  <label for="personalAccPart">Частично</label>
                </div>
            </div>
            <div class="one wide field">
                <input name="data[Order][partPayAmount]" id="personalAccPartAmount" placeholder="0.00" data-bind="attr: {'disabled': personalAcc() != 'part'}">
            </div>
        </div>
    </div>
    <button class="ui fluid primary button">Заказать</button>
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
        this.showServiceDesc = ko.observable(false);

        this.locations = ko.observableArray(<?php echo @json_encode($locationsList);?>);
        this.selectedLocation = ko.observable();

        this.types        = ko.observableArray(<?php echo @json_encode($typesList);?>);
        this.selectedType = ko.observable(<?php echo $typeId; ?>);

        this.gameTemplates = <?php echo @json_encode($gameTemplates);?>;
        this.gameTemplatesById = <?php echo @json_encode($gameTemplatesList);?>;
        this.selectedTemplate  = ko.observable();
        this.selectedSlots     = ko.observable();
        this.selectedMonths    = ko.observable();
        this.selectedServices  = ko.observableArray();

        this.typesDiscount = ko.observableArray(<?php echo @json_encode($typeDiscount);?>);
        this.selectedPublicType   = ko.observable();
        this.discounts = <?php echo @json_encode($discount);?>;
        this.promoDiscount  = ko.observable(0);
        this.clientDiscount = ko.observable(<?php echo $userDiscount; ?>);
        this.discountSum    = ko.observable(0);
        this.balance        = ko.observable(Number(<?php echo $balance; ?>));
        this.personalAcc    = ko.observable('out');

        this.servicesList = ko.observableArray();

        this.gameTemplatesList = ko.pureComputed(function() {
            return this.gameTemplates[this.selectedType()];
        }, this);

        this.publicTypeList = ko.pureComputed(function() {
            if (this.selectedType() >= 2 && this.selectedType() < 5){
                return this.typesDiscount()[0];
            } else {
                if (this.selectedTemplate() == 7 || this.selectedTemplate() == 8){
                    return [this.typesDiscount()[0], this.typesDiscount()[2]];
                } else {
                    return this.typesDiscount();
                }
            }

        }, this);

        this.slotsSelectList = ko.pureComputed(function() {
            var template = this.gameTemplatesById[this.selectedTemplate()];
            var slotList = [];

            if (template !== undefined){
                this.selectedSlots(template.slots_value);

                for (var i = Number(template.slots_min); i <= Number(template.slots_max); i++) {
                    slotList.push({'number': i, 'name' : i + ' ' + getNumEnding(i, ['слот', 'слота', 'слотов'])});
                };

                return slotList;
            }

        }, this);

        this.slotPrice = ko.pureComputed(function() {
            var template = this.gameTemplatesById[this.selectedTemplate()];

            if (template !== undefined && this.selectedPublicType() !== undefined){
                if (this.selectedPublicType() == 0){
                    return template['price'];
                } else if (this.selectedPublicType() == 1){
                    return template['pricePrivatePassword'];
                } else if (this.selectedPublicType() == 2){
                    return template['pricePrivatePower'];
                }
            }

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

        this.servicesListGet  = ko.computed(function() {
            var self = this;
            $.getJSON('/services/getServices/' + this.selectedTemplate() + '/all/' + this.selectedLocation(),
                      {}, function(answer) {
                            if(answer.result !== undefined) {
                                self.servicesList(answer.result);
                              return true;
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

        this.serverCost = ko.pureComputed(function() {
            console.log(this.selectedServices());
            return Number(this.selectedSlots()) *
                   Number(this.slotPrice()) *
                   Number(this.selectedMonths());
        }, this);

        this.servicesCost = ko.pureComputed(function() {
            var self = this;
            var serviceTotal = 0;

            $.each(self.selectedServices(), function(id){
                serviceTotal += Number(self.servicesList()[id].price);
            });

            return serviceTotal;
        }, this);

        this.totalCost = ko.pureComputed(function() {
            var total = (this.serverCost() + this.servicesCost());
            var totalWithDiscount = (this.serverCost() + this.servicesCost()) * ((100 - this.totalDiscount())/100);

            this.discountSum((total - totalWithDiscount).toFixed(2));

            return totalWithDiscount.toFixed(2);

        }, this);

        this.serviceDescAction = function(data){
            var self = this;

            if (data.id == self.showServiceDesc()){
                self.showServiceDesc(false);
            } else {
                self.showServiceDesc(data.id);
            }
        }.bind(this);

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

    ko.cleanNode(document.getElementById("orderCreate"));
    ko.applyBindings(new orderCreateViewModel(), document.getElementById("orderCreate"));

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
