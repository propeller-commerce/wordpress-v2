<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;
use Propeller\Includes\Controller\AddressController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PurchaseAuthorizationRoles;
use Propeller\Includes\Enum\UserTypes;

class User extends BaseObject {
    /**
     * Contact/Customer CXML shared secret attribute name
     */
    const CXML_SHARED_SECRED_ATTR = 'CXML_SHARED_SECRET';

    public int $userId;
    public array $addresses;
    public stdClass $companies;
    public stdClass $company;
    public mixed $purchaseAuthorizationConfigs;
    public ?stdClass $trackAttributes = null;
    public array $attributes = [];

    public function __construct(mixed $user = null) {
        if (self::is_propeller_logged_in() && !$user)
            parent::__construct(SessionController::get(PROPELLER_USER_DATA));
        else if ($user)
            parent::__construct($user);
    }

    public function is_contact(): bool {
        if (self::is_propeller_logged_in())
            return SessionController::get(PROPELLER_USER_DATA)->__typename == UserTypes::CONTACT;

        return false;
    }

    public function is_customer(): bool {
        if (self::is_propeller_logged_in())
            return SessionController::get(PROPELLER_USER_DATA)->__typename == UserTypes::CUSTOMER;

        return false;
    }

    public function is_purchaser(): bool {
        if (isset($this->purchaseAuthorizationConfigs) && $this->purchaseAuthorizationConfigs->itemsFound > 0) {
            $PURCHASER = PurchaseAuthorizationRoles::PURCHASER;
            $current_company_id = (int) SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

            $found = array_filter($this->purchaseAuthorizationConfigs->items, function($obj) use ($PURCHASER, $current_company_id) {
                return $obj->purchaseRole == $PURCHASER && $obj->company->companyId == $current_company_id;
            });

            if (count($found))
                return true;
        }

        return false;
    }

    public function is_authorization_manager(): bool {
        if (isset($this->purchaseAuthorizationConfigs) && $this->purchaseAuthorizationConfigs->itemsFound > 0) {
            $AUTH_MANAGER = PurchaseAuthorizationRoles::AUTHORIZATION_MANAGER;
            $current_company_id = (int) SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

            $found = array_filter($this->purchaseAuthorizationConfigs->items, function($obj) use ($AUTH_MANAGER, $current_company_id) {
                return $obj->purchaseRole == $AUTH_MANAGER && $obj->company->companyId == $current_company_id;
            });

            if (count($found))
                return true;
        }

        return false;
    }

    public function get_authorization_limit(): mixed {
        if (!$this->is_purchaser())
            return null;

        $PURCHASER = PurchaseAuthorizationRoles::PURCHASER;
        $current_company_id = (int) SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

        $found = array_filter($this->purchaseAuthorizationConfigs->items, function($obj) use ($PURCHASER, $current_company_id) {
            return $obj->purchaseRole == $PURCHASER && $obj->company->companyId == $current_company_id;
        });

        if (count($found)) {
            $current_limit = current($found)->authorizationLimit;

            if (!$current_limit)
                return 0;

            return $current_limit;
        }

        return 0;
    }

    public static function is_logged_in(): bool
    {
        if (defined('PROPELLER_WP_SESSIONS') && PROPELLER_WP_SESSIONS)
            return is_user_logged_in() && self::is_propeller_logged_in();

        return self::is_propeller_logged_in();
    }

    public static function is_propeller_logged_in(): bool
    {
        $logged_in = (SessionController::has(PROPELLER_SESSION) && !SessionController::get(PROPELLER_SESSION)->isAnonymous) &&
            (SessionController::has(PROPELLER_USER_DATA) && (isset(SessionController::get(PROPELLER_USER_DATA)->userId)));

        return $logged_in;
    }

    public static function get_type(): ?string
    {
        if (self::is_propeller_logged_in())
            return SessionController::get(PROPELLER_USER_DATA)->__typename;

        return null;
    }

    public function get_default_company(): mixed {
        if (!self::is_propeller_logged_in())
            return null;

        if (!$this->is_contact())
            return null;

        return $this->company;
    }

    public function get_current_company(): mixed {
        if (!self::is_propeller_logged_in())
            return null;

        if (!$this->is_contact())
            return null;

        $found = array_filter($this->get_companies(), function ($cmp) {
            return $cmp->companyId == SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
        });

        return current($found);
    }

    public function get_company(mixed $company_id): mixed {
        if (self::is_propeller_logged_in())
            return null;

        if (!$this->is_contact())
            return null;

        $found = array_filter($this->get_companies(), function ($cmp) use ($company_id) {
            return $cmp->companyId == $company_id;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_companies(): array {
        if (!self::is_propeller_logged_in())
            return [];

        if (!$this->is_contact())
            return [];

        if ($this->has_companies())
            return $this->companies->items;

        return [];
    }

    public function has_companies(): bool {
        if (!self::is_propeller_logged_in())
            return false;

        if (!$this->is_contact())
            return false;

        return $this->companies->itemsFound > 0;
    }

    public function get_addresses(): mixed {
        $addresses = [];

        switch ($this->get_type()) {
            case UserTypes::CUSTOMER:
                $addresses = $this->addresses;

                break;
            case UserTypes::CONTACT:
                $current_company_id = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                $company = array_filter($this->companies->items, function($obj) use ($current_company_id) {
                    return $obj->companyId == $current_company_id;
                });

                if (count($company)) {
                    $company = current($company);

                    $addresses = $company->addresses;
                } else {
                    $addresses = $this->company->addresses;
                    $address_controller = new AddressController();

                    $address_controller->set_user_data(SessionController::get(PROPELLER_USER_DATA));
                    $address_controller->set_user_type(self::get_type());

                    $addresses = $address_controller->get_addresses();
                }

                break;
        }

        return $addresses;
    }

    public function get_purchase_authorization_role(): void
    {
    }

    public function get_track_attr_by_name(string $name): mixed
    {
        if (!isset($this->trackAttributes) || !is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_track_attr_value(string $name): mixed
    {
        if (!isset($this->trackAttributes) || !is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found)) {
            $attr = new Attribute(current($found));
            return $attr->get_value();
        }

        return null;
    }

    public function get_attr_by_name(string $name): mixed
    {
        if (!is_array($this->attributes))
            return null;

        $found = array_filter($this->attributes, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_attr_value(string $name): mixed
    {
        if (!is_array($this->attributes))
            return null;

        $found = array_filter($this->attributes, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found)->get_value();

        return null;
    }
}
