<?php
/**
 * DarkAuth CakeComponent for Authentication.
 *
 * An Authentication component that works on the idea that you
 * don't need a specific "login" page, you should just be presented
 * with it if you don't have access, or told you are denied if you
 * are authenticated but not authorised, but you are not redirected
 * in either case.
 *
 * Tested with CakePHP Version: 1.2.0.7692 RC3
 *
 * @link            http://thechriswalker.net/
 * @version         1.5 Final
 * @lastmodified    2009/01/05 15:03:36
 */
class DarkAuthComponent extends Component {
    /**
     * user model name
     */
    protected $user_model_name = 'User';

    /**
     * user model fields for user and password.
     */
    protected $user_name_field = 'username';
    protected $user_pass_field = 'passwd';

    /**
     * do you want to case fold the username before verifying? either 'lower','upper','none', to change case to lower/upper/leave it alone before matching.
     */
    protected $user_name_case_folding = 'none';

    /**
     * surely you have a field in you users table to show whether the user is active or not? set to null if not.
     */
    protected $user_live_field = 'live';
    /**
     * What value means the use IS live?
     */
    protected $user_live_value = 1;

    /**
     * Group for access control if used
     */
    protected $group_model_name = 'Group';
    /**
     * The field for group access control
     */
    protected $group_name_field = 'name';

    /**
     * set to false if you don use a HABTM group relationship.
     */
    protected $HABTM = true;

    /**
     * if you want a single group to have automatically granted access to any restriction.
     */
    protected $superuser_group = 'admin';

    /**
     * this is the login view (I usually suggest keeping this in the root of your views folder)
     */
    protected $login_view = '/login';
    /**
     * this is the deny view (I usually suggest keeping this in the root of your views folder)
     */
    protected $deny_view = '/deny';

    /**
     *  NB this is were to **redirect** AFTER logout by default
     */
    protected $logout_page = '/';

    /**
     * This message is setFlash()'d on failed login.
     */
    protected $login_failed_message = '<p>Сожалеем, вы неправильно ввели логин или пароль</p>';
    /**
     * Should we set the flash on failure?
     */
    protected $set_flash_for_failure = true;

    /**
     * This message setFlash'd on successful login.
     */
    protected $login_success_message = '<p>Вы успешно вошли в административную панель.</p>';
    /**
     * Should we set the flash on success?
     */
    protected $set_flash_for_success = true;

    /**
     * Message to setFlash after logout.
     */
    protected $logout_message = '<p>Вы успешно покинули административную панель.</p>';
    /**
     * Should we set the flash on logout?
     */
    protected $set_flash_for_logout = true;

    /**
     *  The Key to use in setFlash (default is 'flash')
     */
    protected $flash_key = 'flash';

    /**
     * Allow use of cookies to remember authenticated sessions.
     */
    protected $allow_cookie = true;

    /**
     * how long until cookies expire (by default). format is "strtotime()" based (http://php.net/strtotime).
     */
    protected $cookie_expiry = '+6 Months';

    /**
     * some random stuff that someone is unlikey to guess.
     */
    protected $session_secure_key = 'WUmtVjfnFFARluRG69NjsY';

    /**
     * Log the logins as they happen?
     */
    protected $log_the_login = true;

    /**
     * Field to store login time (in the user model)
     */
    protected $log_the_login_time_field = 'last_login';
    /**
     * Field to store login ip address (in the user model)
     */
    protected $log_the_login_ip_field = 'last_ip';

    /**
     * Heashes Passwords for secure storage and comparison.
     *
     * You can edit this function to explain how you want to hash your passwords.
     * Also you can use it as a static function in your controller to hash passwords beforeSave
     */
    function hasher($plain_text) {
        $hashed = md5('gh' . $plain_text . 'manager' . '321654987321jhasc23c');
        return $hashed;
    }

    ##########################################################################
    /*
     * DON'T EDIT THESE OR ANYTHING BELOW HERE UNLESS YOU KNOW WHAT YOU'RE DOING
     */

    /**
     * The Controller
     */
    function initialize(Controller $controller, $settings = array()) {
        $this->controller = $controller;
    }
    /**
     * Where we are
     */
    public $here;
    /**
     * Components needed for this Component
     */
    public $components = array('Session');
    /**
     * The Current logged in User detail
     */
    public $current_user;
    /**
     * Did we login from Session data?
     */
    public $from_session;
    /**
     * Did we login from Post data?
     */
    public $from_post;
    /**
     * Did we login from Cookie data?
     */
    public $from_cookie;

    /**
     * the startup method called on creation.
     */
    function startup(Controller $controller) {
        //Let's check they have changed the secure key from the default.
        if ($this->session_secure_key === 'WUmtVjfnFFARluRG69Njsy') {
            die('<p>Please change the DarkAuth::session_secure_key value from the default.</p>');
        }
        $this->controller = $controller;
        $this->here = substr($this->controller->here, strlen($this->controller->base));
        $this->_login();
        //now check session/cookie info.
        $this->getUserInfoFromSessionOrCookie();
        //now see if the calling controller wants auth
        if (array_key_exists('_DarkAuth', $this->controller)) {
            // We want Auth for any action here
            if (!empty($this->controller->_DarkAuth['onDeny'])) {
                $deny = $this->controller->_DarkAuth['onDeny'];
            } else {
                $deny = null;
            }

            if (!empty($this->controller->_DarkAuth['required'])) {

                $this->requiresAuth($this->controller->_DarkAuth['required'], $deny);
            } else {
                $this->requiresAuth(null, $deny);
            }
        }
        //finally give access to the data through Configure
        $DA = array(
            'User' => $this->getUserInfo(),
            'Access' => $this->getAccessList(),
            'Authorized' => $this->isAllowed(),
        );
        Configure::write('DarkAuth', $DA);
    }
    /**
     * Attempts to login from POST data
     */
    function _login() {
        if (is_array($this->controller->data) && array_key_exists('DarkAuth', $this->controller->data)) {
            $this->authenticate_from_post($this->controller->data['DarkAuth']);
            $this->controller->request->data['DarkAuth']['password'] = '';
        }
    }
    /**
     * generates a security salt from Cake and this Components random strings.
     */
    function secure_key() {
        static $key;
        if (!$key) {
            $key = md5(Configure::read('Security.salt') . '!DarkAuth!' . $this->session_secure_key);
        }
        return $key;
    }
    /**
     *  forces the auth check and displays the login screen, or auth denied page or allows the script to continue as appropriate.
     */
    function requiresAuth($groups = array(), $deny_redirect = null, $flash = null) {
        if (empty($this->current_user)) {
            // Still no info! render login page!
            if ($this->from_post && $this->set_flash_for_failure) {
                $this->Session->setFlash($this->login_failed_message, 'flash_login_error', array(), $this->flash_key);
            }
            if ($flash) {
                $this->Session->setFlash($flash, 'flash_login_error');
            }

            // Throw exception for JSON requests
            if($this->controller->params['ext'] == 'json'){
                throw new UnauthorizedException("Unauthorized", '401');
            }

            exit($this->controller->render($this->login_view));
        } else {
            if ($this->from_post) {
                // user just authed, so redirect to avoid post data refresh.
                $this->controller->redirect($this->here, null, null, true);
                return;
            }
            // User is authenticated, so we just need to check against the groups.
            if (empty($groups)) {
                // No Groups specified so we are good to go!
                $deny = false;
            } else {
                $deny = !$this->isAllowed($groups);
            }
            if ($deny) {
                // Current User Doesn't Have Access! DENY
                // Throw exception for JSON requests
                if($this->controller->params['ext'] == 'json'){
                    throw new ForbiddenException("Forbidden", '403');
                }

                if ($deny_redirect) {
                    $this->controller->redirect($deny_redirect);
                } else {
                    exit($this->controller->render($this->deny_view));
                }
            }
        }
        return true;
    }
    /**
     * Checks for access control on current user in given groups.
     */
    function isAllowed($groups = array()) {
        if (empty($this->current_user)) {
            // No information about the user! FALSE
            return false;
        } else {

            // User is authenticated, so we just need to check against the groups.
            if (empty($groups)) {
                // No Groups specified so we are good to go! TRUE
                return true;
            }
            if (!is_array($groups)) {
                //if a string passed, turn to an array with one element
                $groups = array($groups);
            }
            $access = $this->getAccessList();
            // Check the superuser group
            if (!empty($this->superuser_group) //we are using a superuser group
                 && array_key_exists($this->superuser_group, $access) //and that group exists in the access array.
                 && $access[$this->superuser_group]//and the auth'd user has superuser access!
            ) {
                return true;
            }
            // Now we have to check whether the user has one of the groups specified.
            foreach ($groups as $g) {
                if (array_key_exists($g, $access) && $access[$g]) {
                    return true;
                }
            }
        }
    }

    /**
     * Get the info stored in the DarkAuthCookie and validates it's expiry and tamperproofness
     */
    function getCookieInfo() {
        if (!array_key_exists('DarkAuth', $_COOKIE)) {
            //No cookie
            return false;
        }
        list($hash, $data) = explode("|||", $_COOKIE['DarkAuth']);
        if ($hash != md5($data . $this->secure_key())) {
            //Cookie has been tampered with
            return false;
        }
        $crumbs = unserialize(base64_decode($data));
        if (!array_key_exists('username', $crumbs) ||
            !array_key_exists('password', $crumbs) ||
            !array_key_exists('expiry', $crumbs)) {
            //Cookie doesn't contain the correct info.
            return false;
        }
        if (!isset($crumbs['expiry']) || $crumbs['expiry'] <= time()) {
            //Cookie is out of date!
            return false;
        }
        //All checks passed, cookie is genuine. remove expiry time and return
        unset($crumbs['expiry']);
        return $crumbs;
    }

    /**
     * Set a tamper-proof cookie with user login details (yes password is hashed!)
     */
    function setCookieInfo($data, $expiry = 0) {
        if ($data === false) {
            //remove cookie!
            $cookie = false;
            $expiry = 100; //should be in the past enough!
        } else {
            $serial = base64_encode(serialize($data));
            $hash = md5($serial . $this->secure_key());
            $cookie = $hash . "|||" . $serial;
        }
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $domain = null;
        } else {
            $domain = '.' . $_SERVER['SERVER_NAME'];
        }
        return setcookie('DarkAuth', $cookie, $expiry, $this->controller->base, $domain);
    }
    /**
     * Set post type authentication and authenticate!
     */
    function authenticate_from_post($data) {
        $this->from_post = true;
        return $this->authenticate($data);
    }
    /**
     * Set session type authentication and authenticate!
     */
    function authenticate_from_session($data) {
        $this->from_session = true;
        return $this->authenticate($data);
    }
    /**
     * Set cookie type authentication and authenticate!
     */
    function authenticate_from_cookie() {
        $this->from_cookie = true;
        return $this->authenticate($this->getCookieInfo());
    }
    /**
     * Use the database to authenticate.
     */
    function authenticate($data, $force_regenerate = false) {
        if ($data === false) {
            $this->destroyData();
            return false;
        }
        if ($this->from_session || $this->from_cookie || $force_regenerate) {
            $hashed_password = $data['password'];
        } else {
            $hashed_password = $this->hasher($data['password']);
        }
        switch ($this->user_name_case_folding) {
            case 'lower':
                $data['username'] = strtolower($data['username']);
                break;
            case 'upper';
                $data['username'] = strtoupper($data['username']);
                break;
            default:break;
        }
        $conditions = array(
            $this->user_model_name . "." . $this->user_name_field => $data['username'],
            $this->user_model_name . "." . $this->user_pass_field => $hashed_password,
        );
        if ($this->user_live_field) {
            $field = $this->user_model_name . "." . $this->user_live_field;
            $conditions[$field] = $this->user_live_value;
        };

        $this->controller->loadModel($this->user_model_name);
        $check = $this->controller->{$this->user_model_name}->find('first', ['conditions' => $conditions]);

        if ($check) {
            if (!$this->Session->write($this->secure_key(), $check)) {
                echo "Writing session data failed!";
            }
            if (
                $this->allow_cookie && //check we're allowing cookies
                ($this->from_post || $force_regenerate) && //check this was a posted login attempt.
                array_key_exists('remember_me', $data) && //check they where given the option!
                $data['remember_me'] == true//check they WANT a cookie set
            ) {
                // set our cookie!
                if (array_key_exists('cookie_expiry', $data)) {
                    $this->cookie_expiry = $data['cookie_expiry'];
                } else {
                    $this->cookie_expiry;
                }
                if (strtotime($this->cookie_expiry) <= time()) {
                    // Session cookie? might as well not set at all...
                } else {
                    $expiry = strtotime($this->cookie_expiry);
                    $this->setCookieInfo(array('username' => $data['username'], 'password' => $hashed_password, 'expiry' => $expiry), $expiry);
                }
            }
            if ($this->log_the_login && ($this->from_post || $this->from_cookie)) {
                //new login, write to user model.
                //Store the old info!
                $last_login = array(
                    'time' => $check[$this->user_model_name][$this->log_the_login_time_field],
                    'ip' => $check[$this->user_model_name][$this->log_the_login_ip_field],
                );
                $this->Session->write('LastLogin', $last_login);
                $data = array($this->user_model_name => array($this->log_the_login_time_field => date("Y-m-d H:i:s"), $this->log_the_login_ip_field => env('REMOTE_ADDR')));
                $this->controller->{$this->user_model_name}->{$this->controller->{$this->user_model_name}->primaryKey} = $check[$this->user_model_name][$this->controller->{$this->user_model_name}->primaryKey];
                $this->controller->{$this->user_model_name}->save($data);
            }
            $this->current_user = $check;
            if ($this->from_post && $this->set_flash_for_success) {
                $this->Session->setFlash($this->login_success_message, 'flash_success', array(), $this->flash_key);

                $this->controller->loadModel('Action');

                $log['Action'] = array('user_id' => $this->getUserId(),
                    'action' => 'Успешный вход в панель',
                    'creator' => 'user',
                    'ip' => env('REMOTE_ADDR'),
                    'status' => 'ok',
                );
                $this->controller->Action->save($log);
            }
            Configure::write('DarkAuth.User', $this->getUserInfo());
            return true;
        } else {
            if ($this->from_post && $this->set_flash_for_failure) {
                $this->Session->setFlash($this->login_failed_message, 'flash_error', array(), $this->flash_key);
            }
            $this->destroyData();
            return false;
        }
    }

    /**
     * Returns the current loggin user info (just User Model)
     */
    function getUserInfo($all = false) {
        return ($all) ? $this->current_user : $this->current_user[$this->user_model_name];
    }
    /**
     * returns Current logged in user ID
     */
    function getUserId() {
        return $this->current_user[$this->user_model_name]['id'];
    }
    /**
     * Get all user info, includiung associated model info
     */
    function getAllUserInfo() {
        return $this->current_user;
    }
    /**
     * Get the access control matrix.
     */
    function getAccessList() {
        static $access_list = false;
        if (!$access_list) {
            $access_list = $this->_generateAccessList();
        }
        return $access_list;
    }
    /**
     * Generates the Access control list for the currently logged in user.
     */
    function _generateAccessList() {
        if (!$this->group_model_name) {
            return array();
        }
        $this->controller->loadModel($this->user_model_name);

        $all_groups = $this->controller->{$this->user_model_name}->{$this->group_model_name}->find('list');
        if (!count($all_groups)) {return array();}
        $access = array_combine($all_groups, array_fill(0, count($all_groups), 0)); //create empty array.

        if (empty($this->current_user)) {
            // NO AUTHENTICATION, SO EMTPY ARRAY!
            return $access;
        }
        if ($this->HABTM) {
            // could be many groups
            $ugroups = Set::combine($this->current_user[$this->group_model_name], '{n}.id', '{n}.' . $this->group_name_field);
            foreach ($all_groups as $id => $role) {
                if (in_array($role, $ugroups)) {
                    $access[$role] = 1;
                } else {
                    $access[$role] = 0;
                }
            }
        } else {
            // single group assoc, id = user.group_id
            $foreign_key = $this->controller->{$this->user_model_name}->belongsTo[$this->group_model_name]['foreignKey'];
            foreach ($all_groups as $id => $role) {
                if ($this->current_user[$this->user_model_name][$foreign_key] == $id) {
                    $access[$role] = 1;
                } else {
                    $access[$role] = 0;
                }
            }
        }
        return $access;
    }

    /**
     * Destroys all session/cookie data to do with DarkAuth
     */
    function destroyData() {
        $this->Session->delete($this->secure_key());
        if ($this->allow_cookie) {
            $this->setcookieInfo(false);
        }
        $this->current_user = null;
    }

    /**
     * Securely Log out a user.
     */
    function logout($redirect = false) {

        $userId = $this->getUserId();

        $this->destroyData();
        if (!$redirect) {
            $redirect = $this->logout_page;
        }
        if ($this->set_flash_for_logout) {
            $this->Session->setFlash($this->logout_message, 'flash_login_success', array(), $this->flash_key);
        }

        $this->controller->loadModel('Action');

        $log['Action'] = array('user_id' => $userId,
            'action' => 'Успешный выход из панели',
            'creator' => 'user',
            'ip' => env('REMOTE_ADDR'),
            'status' => 'ok',
        );
        $this->controller->Action->save($log);

        $this->controller->redirect($redirect, null, true);
        exit();
    }

    /**
     * Try and find existing user info from Session or Cookie.
     */
    function getUserInfoFromSessionOrCookie() {
        if (!empty($this->current_user)) {
            return false;
        }
        if ($this->Session->valid() && $this->Session->check($this->secure_key())) {
            $this->current_user = $this->Session->read($this->secure_key());
            return $this->authenticate_from_session(array(
                'username' => $this->current_user[$this->user_model_name][$this->user_name_field],
                'password' => $this->current_user[$this->user_model_name][$this->user_pass_field],
            ));
        } elseif ($this->allow_cookie) {
            return $this->authenticate_from_cookie();
        }
    }
}
?>
