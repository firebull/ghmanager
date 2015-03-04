<?php

/*

Common class for controllers
Copyright (C) 2013 Nikita Bulaev

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

*/

class TeamServerComponent extends Component {

        public $components = array('DarkAuth', 'Email');

        function initialize(Controller $controller, $settings = array()) {
                $this->controller = $controller;
        }

        function logAction( $text = null, $status = null, $ownerUserId = null, $creator = null) {
                $this->controller->loadModel('Action');
                $this->controller->loadModel('UserGroup');

                $adminGroupIds = array('1', '2');
                $userIsAdmin = false;

                $userId = $this->DarkAuth->getUserId();

                $this->controller->UserGroup->id = $userId;
                $user = $this->controller->UserGroup->read();

                if ($creator === null ) {
                        foreach ($user['Group'] as $group) {
                                if (in_array($group['id'], $adminGroupIds)) {
                                        $creator = 'admin';
                                        $userIsAdmin = true;
                                        break;
                                }
                        }

                        if ($userIsAdmin === false) {
                                $creator = 'user';
                        }
                }

                if ($ownerUserId === null) {
                        $ownerUserId = $userId;
                }

        $log['Action'] = array( 'user_id' => $ownerUserId,
                                'action'  => $text,
                                'creator' => $creator,
                                'ip'      => env('REMOTE_ADDR'),
                                'status'  => $status
                              );

        $this->controller->Action->save($log);
        }

        // Генерирование нломера личного счёта по ID клиента
        function getUserBill($userId = null) {
                if ($userId === null) {
                        $userId = $this->DarkAuth->getUserId();
                }

                if ($userId) {
                        return sprintf('47%1$07d', $userId);
                } else {
                        return false;
                }
        }

        function generatePass( $length = 8) {

                $consonantes = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz123456789';
                $r = '';
                for ($i = 0; $i < $length; $i++) {

                                $r .= $consonantes{rand(0, strlen($consonantes) - 1)};
                }
                return $r;
        }

        function redisConnect($db = 0) {
                $redis = new Redis();
                $redis->connect('192.168.0.48', 6379, 2);
                $redis->auth('WeeghieGiech2aic7aig6fuwu8OoW6aevaaj3ideideeke9Yie2aabieng8ooTha');
                $redis->select($db);

                return $redis;
        }

        /*
         * Функция запроса по HTTP, используя CURL
         */
        function webGet( $host = null, $port = null, $path = null, $method = 'GET', $timeout = 15) {

            if ($method === 'GET') {
                if ( $port > 0 ) {
                    $url = $host.":".$port."/".$path;
                } else {
                    $url = $host."/".$path;
                }

                $handle = curl_init($url);

                if ($handle != false) {
                    curl_setopt ($handle, CURLOPT_HEADER, 0);
                    curl_setopt ($handle, CURLOPT_USERAGENT, "User-Agent: GH Manager (GET, Mozilla Compatible)");

                    curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt ($handle, CURLOPT_TIMEOUT, $timeout);
                    ob_start();
                    curl_exec($handle);
                    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                    curl_close($handle);
                    $contents = ob_get_contents();
                    ob_end_clean();

                    if ( $httpCode != 200 ) {
                        $this->request->data['Webget']['error']['Http'] = $httpCode;
                        $this->controller->Session->setFlash('При попытке совершить операцию произошла ошибка: '.$httpCode.
                                '<br/>Попробуйте повторить операцию позднее.', 'flash_error');
                        return  false;
                    } else {
                        return $contents;
                    }

                } else {
                    $this->request->data['Webget']['error']['Curl'] = curl_error($handle);
                    $this->controller->Session->setFlash('Ошибка подключения: '.curl_error($handle), 'flash_error');
                    curl_close($handle);
                    return false;
                }
            } elseif ($method === 'POST') {

                if ( $port > 0 ) {
                    $host = $host.":".$port;
                }

                $handle = curl_init($host);
                if ($handle != false) {
                    /* Временно!!! Отключить проверку сертификатов. */
                    curl_setopt ($handle, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt ($handle, CURLOPT_SSL_VERIFYHOST, 0);

                    curl_setopt ($handle, CURLOPT_POST, true);
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $path);

                    curl_setopt ($handle, CURLOPT_HEADER, 0);
                    curl_setopt ($handle, CURLOPT_USERAGENT, "User-Agent: GH Manager (POST, Mozilla Compatible)");

                    curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt ($handle, CURLOPT_TIMEOUT, $timeout);
                    ob_start();
                    curl_exec($handle);
                    curl_close($handle);
                    $contents = ob_get_contents();
                    ob_end_clean();
                    return $contents;
                } else {
                    $this->controller->Session->setFlash('Ошибка подключения: '.curl_error($handle), 'flash_error');
                    return false;
                }
            } else {
                $this->controller->Session->setFlash('Указан неправильный метод: '.$method, 'flash_error');
                curl_close($handle);
                return false;
            }

        }

        function saveConfirm($type = null, $userId = null, $serverId = null, $array = null) {

            if (!($type === null and $userId === null and $array === null)) {
                $this->controller->loadModel('Confirm');
                $confirm = false;
                $data['Confirm'] = array();

                if ($serverId !== null) {
                    $confirm = $this->controller->Confirm->find('first',
                                                                 array( 'conditions' => array( 'user_id' => $userId,
                                                                                               'type' => $type,
                                                                                               'server_id' => $serverId
                                                                                                )));
                } else {
                    $confirm = $this->controller->Confirm->find('first',
                                                                 array( 'conditions' => array( 'user_id' => $userId,
                                                                                               'type' => $type
                                                                                                )));
                }

                if ($confirm !== false) {
                    $this->controller->Confirm->id = $confirm['Confirm']['id'];
                }

                $data['Confirm']['code'] = strtoupper($this->generatePass(6));

                if ($type === 'phone') {
                    $data['Confirm']['code2'] = strtoupper($this->generatePass(6));
                }

                $data['Confirm']['user_id'] = $userId;
                $data['Confirm']['type'] = $type;
                $data['Confirm']['array'] = json_encode($array);

                if ($serverId !== null) {
                    $data['Confirm']['server_id'] = $serverId;
                }

                if ($this->controller->Confirm->save($data)) {
                    if ($type === 'phone') {
                        return array($data['Confirm']['code'], $data['Confirm']['code2']);
                    } else {
                        return $data['Confirm']['code'];
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }

        function sendSms($phone = null, $text = null) {

            if (!($phone === null and $text === null)) {

                $sms = @parse_ini_file("../config/external.ini.php", true);

                if (empty($sms)) {
                    $this->Session->setFlash('Не могу получить данные. Попробуйте позднее. ', 'flash_error');
                    return false;
                }

                $smslogin = $sms['smss']['login'];
                $smspass  = $sms['smss']['pass'];

                // Сначала проверить баланс
                $balance = $this->webGet('https://smsc.ru/sys/balance.php', 0, 'fmt=3&login='.$smslogin.'&psw='.$smspass, 'POST');

                if ($balance !== false) {
                    $balance = json_decode($balance, true);
                } else {
                    return false;
                }

                // Стоимость одного смс 50коп. Проверить, чтоб баланс был больше.
                if (!empty($balance['balance']) and floatval($balance['balance']) >= 0.5) {
                    //pr($balance['balance']);
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }

        // Рассчёт остатка средств
        function countServerMoneyLeft($server = null) {
            if (empty($server)) {
                return array(0, 0, 0);
            }

            $currentRent = strtotime($server['Server']['payedTill']);
            $giftDays = intval($server['Server']['giftDays']) * 86400;
            $monthLeft = 0;
            $daysLeft = 0;
            $moneyPerDay = 0;

            if (($currentRent - $giftDays) > time() && !empty($server['Order'])) {
                $monthLeft = round(($currentRent - time())/2592000, 1);
                $daysLeft = round(($currentRent -time())/86400, 2);
                $orderMonth = 0;
                $payedMoney = 0;

                foreach ($server['Order'] as $order) {
                    if ($order['payedBy'] !== 'manual' and $order['payed'] == 1) {
                        $orderMonth += intval($order['month']);

                        if ($order['sumGot'] == 0) {
                            $payedMoney += round($order['sum']*0.96, 2);
                        } else {
                            $payedMoney += floatval($order['sumGot']);
                        }

                        if ($orderMonth >= $monthLeft ) {
                            break;
                        }
                    }
                }
                if ($payedMoney > 0) {
                    $moneyPerDay = round($payedMoney/($orderMonth*30), 3);
                    $moneyLeft = round($daysLeft*$moneyPerDay, 2);
                } else {
                    $moneyLeft = 0;
                }

            } else {
                $moneyLeft = 0;
            }

            return array($moneyLeft, $moneyPerDay, $daysLeft);
        }
}

?>
