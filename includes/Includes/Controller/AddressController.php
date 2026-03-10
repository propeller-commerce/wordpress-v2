<?php

namespace Propeller\Includes\Controller;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\UserTypes;
use Propeller\Includes\Object\User;
use stdClass;

class AddressController extends BaseController
{
    protected $type = 'address';

    protected $user;
    protected $user_type;
    protected $is_registration;
    protected $model;
    public array $addresses = [];

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('address');

        $this->is_registration = false;
    }

    /*
        Addresses filters
    */
    public function address_box($address, $obj, $title, $show_title = false, $show_modify = false, $show_delete = false, $show_set_default = false)
    {

        $this->assets()->std_requires_asset('propeller-address-default');

        require $this->load_template('partials', '/user/propeller-account-address-box.php');
    }

    public function address_add($type, $title, $obj)
    {
        $address = $this->get_address_obj($type);

        require $this->load_template('partials', '/user/propeller-account-address-add.php');
    }

    public function address_form($address)
    {
        require $this->load_template('partials', '/user/propeller-account-address-form.php');
    }

    public function address_modify($address)
    {
        require $this->load_template('partials', '/user/propeller-account-address-modify.php');
    }

    public function address_delete($address)
    {
        require $this->load_template('partials', '/user/propeller-account-address-delete.php');
    }

    public function address_delete_popup($address)
    {
        require $this->load_template('partials', '/user/propeller-account-address-delete-popup.php');
    }

    public function address_set_default($address)
    {

        $this->assets()->std_requires_asset('propeller-address-default');

        require $this->load_template('partials', '/user/propeller-account-address-set-default.php');
    }

    public function address_popup($address, $type)
    {
        if (!isset($address) || !$address || !is_object($address))
            $address = $this->get_address_obj($type);

        require $this->load_template('partials', '/user/propeller-account-address-popup.php');
    }

    public function set_user_data($user_data)
    {
        $this->user = $user_data;
    }

    public function set_user_type($user_type)
    {
        $this->user_type = $user_type;
    }

    public function set_user()
    {
        if (UserController::is_propeller_logged_in()) {
            $user = new User();

            $this->set_user_data($user);
            $this->set_user_type($user->get_type());
        }
    }

    public function set_is_registration($is_registration)
    {
        $this->is_registration = $is_registration;
    }

    public function account_addresses()
    {
        ob_start();

        $args = [];

        if (UserController::user()) {
            $this->set_user_data(SessionController::get(PROPELLER_USER_DATA));
            $this->set_user_type(UserController::user()->get_type());

            $this->addresses = $this->get_addresses($args);

            require $this->load_template('partials', '/user/propeller-account-addresses.php');

            return ob_get_clean();
        }

        return '';
    }

    public function get_addresses($args = [])
    {
        $type = 'addressesByCompanyId';
        $param_name = 'companyId';
        $param_value = 0;

        switch ($this->user_type) {
            case UserTypes::CUSTOMER:
                $type = 'addressesByCustomerId';
                $param_name = 'customerId';
                $param_value = $this->user->userId;

                break;
            case UserTypes::CONTACT:
                $type = 'addressesByCompanyId';
                $param_name = 'companyId';
                $param_value = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }

        $args[$param_name] = $param_value;

        $gql = $this->model->get_addresses($type, $args);

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $addressesData = $this->query($gql, $type);

        return $addressesData;
    }

    public function get_addresses_cart($args = [])
    {

        $type = 'addressesByCompanyId';
        $param_name = 'company_id';
        $param_value = 0;

        switch ($this->user_type) {
            case UserTypes::CUSTOMER:
                $type = 'addressesByCustomerId';
                $param_name = 'customer_id';
                $param_value = $this->user->userId;

                break;
            case UserTypes::CONTACT:
                $type = 'addressesByCompanyId';
                $param_name = 'company_id';
                $param_value = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }

        $params = [];
        $params[$param_name] = $param_value;

        if (isset($args['type']))
            $params['address_type'] = isset($args['type']) ? sanitize_text_field($args['type']) : AddressType::DELIVERY;

        $gql = $this->model->get_addresses_cart($type, $params);


        $addressesData = $this->query($gql, $type);

        return $addressesData;
    }


    public function get_external_addresses($args)
    {
        $type = 'externalAddress';

        $gql = $this->model->get_external_address($args);

        $addressesData = $this->query($gql, $type);

        return $addressesData;
    }

    public function add_address($args, $userId = null)
    {
        $type = 'companyAddressCreate';
        $param_name = 'companyId';
        $param_value = 0;

        switch ($this->user_type) {
            // case UserTypes::USER:
            //     $type = 'userAddressCreate';
            //     $param_name = 'userId';
            //     $param_value = $this->user->userId;

            //     break;
            case UserTypes::CUSTOMER:
                $type = 'customerAddressCreate';
                $param_name = 'customerId';
                $param_value = $this->user->userId;

                break;
            case UserTypes::CONTACT:
                $type = 'companyAddressCreate';
                $param_name = 'companyId';
                $param_value = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }

        if ($userId)
            $param_value = $userId;

        if (isset($args['save_delivery_address'])) {
            $args['type'] = AddressType::DELIVERY;
            // $args['isDefault'] = 'Y';
        }

        $params = $this->format_params($args);
        $params[$param_name] =  $param_value;

        $gql = $this->model->add_address($type, $params);

        $addressesData = $this->query($gql, $type);

        if (isset($addressesData->id))
            $this->update_user_addresses();

        return $addressesData;
    }

    public function update_address($args, $userId = null)
    {
        $type = 'companyAddressUpdate';
        $param_name = 'companyId';
        $param_value = 0;

        switch ($this->user_type) {
            case UserTypes::CUSTOMER:
                $type = 'customerAddressUpdate';
                $param_name = 'customerId';
                $param_value = $this->user->userId;

                break;
            case UserTypes::CONTACT:
                $type = 'companyAddressUpdate';
                $param_name = 'companyId';
                $param_value = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }

        if ($userId)
            $param_value = $userId;

        $params = $this->format_params($args);
        $params[$param_name] = $param_value;

        if (isset($params['type']))
            unset($params['type']);

        $gql = $this->model->update_address($type, $params);

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        // die;

        $addressesData = $this->query($gql, $type);

        if (isset($addressesData->id))
            $this->update_user_addresses();

        return $addressesData;
    }

    public function delete_address($args)
    {
        $type = 'companyAddressDelete';
        $param_name = 'companyId';
        $param_value = 0;

        switch ($this->user_type) {
            // case UserTypes::USER:
            //     $type = 'userAddressDelete';
            //     $param_name = 'userId';
            //     $param_value = $this->user->userId;

            //     break;
            case UserTypes::CUSTOMER:
                $type = 'customerAddressDelete';
                $param_name = 'customerId';
                $param_value = $this->user->userId;

                break;
            case UserTypes::CONTACT:
                $type = 'companyAddressDelete';
                $param_name = 'companyId';
                $param_value = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }


        $params = [
            $param_name => $param_value,
            'id' => (int) $args['id']
        ];

        $gql = $this->model->delete_address($type, $params);

        $addressesData = $this->query($gql, $type);

        if (isset($addressesData->id))
            $this->update_user_addresses();

        return $addressesData;
    }

    public function get_address_obj($type)
    {
        $address = new stdClass();

        $address->city = '';
        $address->code = '';
        $address->company = '';
        $address->country = '';
        $address->email = '';
        $address->firstName = '';
        $address->id = $this->rand_str();
        $address->lastName = '';
        $address->middleName = '';
        $address->gender = '';
        $address->notes = '';
        $address->number = '';
        $address->numberExtension = '';
        $address->postalCode = '';
        $address->region = '';
        $address->street = '';
        $address->phone = '';
        $address->icp = '';
        $address->type = $type;

        return $address;
    }

    private function rand_str($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public function get_default_address($address_type)
    {
        $args = [];

        if (!is_array($address_type))
            $args['type'] = $address_type;

        $addresses = [];

        if ($this->addresses && is_array($this->addresses) && count($this->addresses))
            $addresses = $this->addresses;
        else {
            if (UserController::is_contact())
                $args['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            else if (UserController::is_customer())
                $args['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;

            $addresses = $this->get_addresses($args);
        }

        $found = array_filter($addresses, function ($obj) use ($address_type) {
            return $obj->isDefault == 'Y' && $obj->type == $address_type;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_non_default_addresses($address_type)
    {
        $args = ['type' => $address_type];

        $addresses = [];

        if (is_array($this->addresses) && count($this->addresses))
            $addresses = $this->addresses;
        else {
            if (UserController::is_contact())
                $args['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            else if (UserController::is_customer())
                $args['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;

            $addresses = $this->get_addresses($args);
        }

        $found = array_filter($addresses, function ($obj) use ($address_type) {
            return $obj->isDefault == 'N' && $obj->type == $address_type;
        });

        return $found;
    }

    public function get_all_addresses($address_type)
    {
        $args = ['type' => $address_type];

        $addresses = [];

        if (is_array($this->addresses) && count($this->addresses))
            $addresses = $this->addresses;
        else {
            if (UserController::is_contact())
                $args['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            else if (UserController::is_customer())
                $args['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;

            $addresses = $this->get_addresses($args);
        }

        $found = array_filter($addresses, function ($obj) use ($address_type) {
            return $obj->type == $address_type;
        });

        return $found;
    }

    public function get_address_by_id($address_id, $address_type)
    {
        if (!is_array($address_type))
            $address_type = ['type' => $address_type];

        $addresses = $this->get_addresses($address_type);

        $found = array_filter($addresses, function ($obj) use ($address_id) {
            return $obj->id == $address_id;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_session_address($address_type)
    {
        $address = null;

        if (UserController::is_contact()) {
            $selected_company_id = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

            $selected_company = array_filter(SessionController::get(PROPELLER_USER_DATA)->companies->items, function ($company) use ($selected_company_id) {
                return $company->companyId == $selected_company_id;
            });

            if (count($selected_company)) {
                $address = array_filter(current($selected_company)->addresses, function ($addr) use ($address_type) {
                    return $addr->isDefault == 'Y' && $addr->type == $address_type;
                });

                if (count($address))
                    $address = current($address);
            }
        } else {
            $address = array_filter(SessionController::get(PROPELLER_USER_DATA)->addresses, function ($addr) use ($address_type) {
                return $addr->isDefault == 'Y' && $addr->type == $address_type;
            });

            if (count($address))
                $address = current($address);
        }

        return $address;
    }

    private function format_params($args)
    {
        $eu_countries = propel_get_countries('EUCountries.php');

        // Ensure we have an array to prevent errors
        if (!is_array($eu_countries)) {
            $eu_countries = [];
        }

        $params = [];

        if (isset($args['city']) && !empty($args['city']))
            $params['city'] = str_replace('"', '\"', $args['city']);

        if (isset($args['code']) && !empty($args['code']))
            $params['code'] = $args['code'];

        if (isset($args['company']) && !empty($args['company']))
            $params['company'] = str_replace('"', '\"', $args['company']);

        if (isset($args['country']) && !empty($args['country']))
            $params['country'] = str_replace('"', '\"', $args['country']);

        if (isset($args['email']) && !empty($args['email']))
            $params['email'] = $args['email'];

        // $params['firstName'] = (isset($args['firstName']) && !empty($args['firstName'])) ? str_replace('"', '\"', $args['firstName']) : SessionController::get(PROPELLER_USER_DATA)->firstName;
        // $params['lastName'] = (isset($args['lastName']) && !empty($args['lastName'])) ? str_replace('"', '\"', $args['lastName']) : SessionController::get(PROPELLER_USER_DATA)->lastName;

        if (isset($args['firstName']) && !empty($args['firstName']))
            $params['firstName'] = str_replace('"', '\"', $args['firstName']);

        if (isset($args['lastName']) && !empty($args['lastName']))
            $params['lastName'] = str_replace('"', '\"', $args['lastName']);

        if (isset($args['middleName']) && !empty($args['middleName']))
            $params['middleName'] = str_replace('"', '\"', $args['middleName']);

        if (isset($args['gender']) && !empty($args['gender']))
            $params['gender'] = $args['gender'];

        if (isset($args['notes']) && !empty($args['notes']))
            $params['notes'] = str_replace('"', '\"', $args['notes']);

        if (isset($args['number']) && !empty($args['number']))
            $params['number'] = strval($args['number']);

        if (isset($args['numberExtension']) && !empty($args['numberExtension']))
            $params['numberExtension'] = strval($args['numberExtension']);

        if (isset($args['postalCode']) && !empty($args['postalCode']))
            $params['postalCode'] = strval($args['postalCode']);

        if (isset($args['region']) && !empty($args['region']))
            $params['region'] = $args['region'];

        if (isset($args['street']) && !empty($args['street']))
            $params['street'] = str_replace('"', '\"', $args['street']);

        if (isset($args['phone']) && !empty($args['phone']))
            $params['phone'] = strval($args['phone']);

        if (isset($args['type']))
            $params['type'] = $args['type'];

        // Only set isDefault if explicitly provided to preserve existing default status during updates
        if (isset($args['isDefault']) && !empty($args['isDefault']))
            $params['isDefault'] = $args['isDefault'];

        if (
            UserController::is_propeller_logged_in() &&
            UserController::user()->get_type() == UserTypes::CONTACT &&
            isset($args['type']) && $args['type'] == AddressType::DELIVERY
        )
            $params['icp'] = in_array($args['country'], array_keys($eu_countries)) && $args['country'] == PROPELLER_ICP_COUNTRY ? 'N' : 'Y';

        if (isset($args['id']) && is_numeric($args['id']) && (int) $args['id'] > 0)
            $params['id'] = $args['id'];

        return $params;
    }

    private function update_user_addresses()
    {
        if ($this->is_registration)
            return;

        $invoice_addresses = $this->get_addresses(['type' => AddressType::INVOICE]);
        $delivery_addresses = $this->get_addresses(['type' => AddressType::DELIVERY]);

        switch ($this->user_type) {
            case UserTypes::USER:
                $this->user->addresses = array_merge($invoice_addresses, $delivery_addresses);

                break;
            case UserTypes::CUSTOMER:
                $this->user->addresses = array_merge($invoice_addresses, $delivery_addresses);

                break;
            case UserTypes::CONTACT:
                $merged = array_merge($invoice_addresses, $delivery_addresses);
                $this->user->company->addresses = $merged;

                // Also update the matching company in companies->items so
                // User::get_addresses() reads the fresh data from session
                if (isset($this->user->companies->items) && is_array($this->user->companies->items)) {
                    $current_company_id = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                    foreach ($this->user->companies->items as $company) {
                        if (isset($company->companyId) && $company->companyId == $current_company_id) {
                            $company->addresses = $merged;
                            break;
                        }
                    }
                }

                break;
        }

        SessionController::set(PROPELLER_USER_DATA, $this->user);

        // try {
        //     $shoppingCart = new ShoppingCartController();

        //     $shoppingCart->set_user_default_cart_address();
        // }
        // catch (Exception $ex) {}
    }
}
