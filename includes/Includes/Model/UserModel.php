<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\AddressController;
use stdClass;

class UserModel extends BaseModel
{
    public function __construct() {}

    public function start_session($site_id)
    {
        $gql = "
            mutation WPStartSession(
                $site_id: Int
            ){
                startSession(siteId: $site_id) {
                    session {
                        ... WPSessionFragment
                    }
                }
            }
        ";

        $queries = [
            self::session_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'site_id' => $site_id
        ];

        return $return;
    }

    public static function session_fragment()
    {
        $gql = '
            fragment WPSessionFragment on GCIPUser {
                uid
                email
                emailVerified
                displayName
                photoUrl
                phoneNumber
                disabled
                isAnonymous
                metadata {
                    lastSignInTime
                    creationTime
                    lastRefreshTime
                }
                tokensValidAfterTime
                tenantId
                passwordHash
                passwordSalt
                authDomain
                lastLoginAt
                createdAt
                accessToken
                refreshToken
                expirationTime
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function base_user_fragment()
    {
        $gql = '
            fragment WPBaseUserFragment on IBaseUser {
                __typename
                firstName
                middleName
                lastName
                email
                gender
                phone
                mobile
                login
                primaryLanguage
                dateOfBirth
                isLoggedIn
                mailingList
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function viewer()
    {
        $user_track_attributes = $this->user_track_attributes();
        $user_track_attrs_arg = !empty($user_track_attributes->query) ? "\$user_track_attrs_filter: AttributeResultSearchInput!" : "";

        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            query WPViewerQuery(
                $user_track_attrs_arg
                $company_track_attrs_arg
                \$contact_companies_input: ContactCompaniesSearchInput
            ){
                viewer {
                    ... WPBaseUserFragment
                    ... on Contact {
                        userId: contactId
                        debtorId
                        company {
                            companyId
                            name
                            taxNumber
                            cocNumber
                            debtorId 
                            email
                            addresses {
                                ... WPAddressFragment
                            }
                            $company_track_attributes->query
                        }
                        companies (input: \$contact_companies_input) {
                            itemsFound
                            items {
                                companyId
                                name
                                taxNumber
                                cocNumber
                                debtorId 
                                email
                                addresses {
                                    ... WPAddressFragment
                                }
                                $company_track_attributes->query
                            }
                        }
                        purchaseAuthorizationConfigs {
                            itemsFound
                            items {
                                purchaseRole
                                authorizationLimit
                                company {
                                    companyId
                                }
                            }
                        }
                        $user_track_attributes->query
                    }
                    ... on Customer {
                        userId: customerId
                        debtorId
                        addresses {
                            ... WPAddressFragment
                        }
                        $user_track_attributes->query
                    }
                }
            }             
        ";

        $queries = [
            !empty($user_track_attrs_arg) ? self::attributes_fragment()->query : "",
            self::base_user_fragment()->query,
            empty($user_track_attrs_arg) && !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            AddressModel::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'user_track_attrs_filter' => $user_track_attributes->variables,
            'company_track_attrs_filter' => $company_track_attributes->variables,
            'contact_companies_input' => [
                'page' => 1,
                'offset' => 50
            ]
        ];

        return $return;
    }

    public function purchase_authorizations_contacts($company_id, $purchase_authorizations_input, $contacts_input)
    {
        $gql = "
            query WPPurchaseAuthorizationsContactsQuery(
                \$companyId: Int!
                \$purchase_authorizations_input: ContactPurchaseAuthorizationConfigSearchInput
                \$contacts_input: ContactSearchArguments
            ){
                company(id: \$companyId) {
                    companyId
                    contacts(input: \$contacts_input) {
                        itemsFound
                        page
                        pages
                        items {
                            contactId 
                            firstName
                            middleName
                            lastName
                            email
                            login
                            purchaseAuthorizationConfigs (input: \$purchase_authorizations_input)  {
                                itemsFound
                                items {
                                    id
                                    purchaseRole
                                    authorizationLimit
                                    company {
                                        companyId
                                    }
                                }
                            }
                        } 
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'companyId' => $company_id,
            'purchase_authorizations_input' => $purchase_authorizations_input,
            'contacts_input' => $contacts_input
        ];

        return $return;
    }

    public function get_purchase_authorization_config($purchase_authorizations_input)
    {
        $gql = "
            query WPGetPurchaseAuthorizationConfigQuery(
                \$purchase_authorizations_input: PurchaseAuthorizationConfigSearchInput
            ){
                purchaseAuthorizationConfigs (input: \$purchase_authorizations_input)  {
                    itemsFound
                    items {
                        id
                        purchaseRole
                        authorizationLimit
                        company {
                            companyId
                        }
                        contact {
                            contactId
                            firstName
                            middleName
                            lastName
                        }
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'purchase_authorizations_input' => $purchase_authorizations_input
        ];

        return $return;
    }

    public function create_purchase_authorization_config($args)
    {
        $gql = "
            mutation WPPurchaseAuthorizationConfigCreateMutation(
                \$input: PurchaseAuthorizationConfigCreateInput
            ){
                purchaseAuthorizationConfigCreate(input: \$input) {
                    id
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'input' => $args
        ];

        return $return;
    }

    public function update_purchase_authorization_config($args)
    {
        $gql = "
            mutation WPPurchaseAuthorizationConfigUpdateMutation(
                \$id: String!
                \$input: PurchaseAuthorizationConfigUpdateInput
            ){
                purchaseAuthorizationConfigUpdate(id: \$id input: \$input) {
                    id
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'id' => $args['id'],
            'input' => [
                'authorizationLimit' => $args['authorizationLimit'],
                'purchaseRole' => $args['purchaseRole']
            ]
        ];

        return $return;
    }

    public function delete_purchase_authorization_config($args)
    {
        $gql = "
            mutation WPPurchaseAuthorizationConfigDeleteMutation(
                \$id: String!
            ){
                purchaseAuthorizationConfigDelete(id: \$id)
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $args;

        return $return;
    }

    public function contact_create($contact_args)
    {
        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            mutation WPContactRegisterMutation(
                \$contact_input: RegisterContactInput!
                $company_track_attrs_arg
            ){
                contactRegister(input: \$contact_input) {
                    session {
                        accessToken
                        refreshToken
                        expirationTime
                    }
                    contact {
                        ... WPBaseUserFragment
                        ... on Contact {
                            userId: contactId
                            company {
                                ... WPCompanyFragment
                            }
                        }
                    }
                }
            }
        ";

        $queries = [
            self::base_user_fragment()->query,
            !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            CompanyModel::company_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'contact_input' => $contact_args
        ];

        if (!empty($company_track_attrs_arg))
            $return->variables['company_track_attrs_filter'] = $company_track_attributes->variables;

        return $return;
    }

    public function customer_create($customer_args)
    {
        $gql = '
            mutation WPCustomerRegisterMutation(
                $customer_input: RegisterCustomerInput!
            ){
                customerRegister(input: $customer_input) {
                    session {
                        accessToken
                        refreshToken
                        expirationTime
                    }
                    customer {
                        ... WPBaseUserFragment
                        ... on Customer {
                            userId: customerId
                            addresses {
                                ... WPAddressFragment
                            }
                        }
                    }
                }
            }
        ';

        $queries = [
            self::base_user_fragment()->query,
            AddressModel::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'customer_input' => $customer_args
        ];

        return $return;
    }

    public function create_contact_account($args)
    {
        $gql = '
            mutation WPContactCreateAccount(
                $id: Int!
                $input: CreateAccountInput
            ){
                contactCreateAccount(id: $id input: $input) {
                    contact {
                        ... on Contact {
                            contactId
                        }
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'id' => $args['id'],
            'input' => $args['input']
        ];

        return $return;
    }

    public function delete_contact_account($args)
    {
        $gql = '
            mutation WPContactDeleteAccount(
                $id: Int!
            ){
                contactDeleteAccount(id: $id)
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'id' => $args['id']
        ];

        return $return;
    }

    public function forgot_password($pass_reset_args)
    {
        $gql = '
            mutation WPPasswordResetMutation(
                $email: String
                $redirectUrl: String
                $input_params: PasswordRecoveryLinkInput
            ){
                passwordResetLink(email: $email redirectUrl: $redirectUrl input: $input_params)
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'redirectUrl' => $pass_reset_args['redirectUrl'],
            'email' => $pass_reset_args['email'],
            'input_params' => $pass_reset_args['input']
        ];

        return $return;
    }

    public function trigger_password_init($pass_init_args)
    {
        $gql = '
            mutation WPPasswordResetMutation(
                $input: PasswordRecoveryLinkInput!
            ){
                triggerPasswordSendInitEmailEvent(input: $input)
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'input' => $pass_init_args
        ];

        return $return;
    }

    public function get_user_data($user_args)
    {
        $user_track_attributes = $this->user_track_attributes();
        $user_track_attrs_arg = !empty($user_track_attributes->query) ? "\$user_track_attrs_filter: AttributeResultSearchInput!" : "";

        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            query WPGetUserQuery(
                \$user_id: Int
                $user_track_attrs_arg
                $company_track_attrs_arg
                \$contact_companies_input: ContactCompaniesSearchInput
            ){
                user(id: \$user_id) {
                    ... WPBaseUserFragment
                    ... on Contact {
                        userId: contactId
                        debtorId
                        company {
                            companyId
                            name
                            taxNumber
                            cocNumber
                            debtorId 
                            email
                            addresses {
                                ... WPAddressFragment
                            }
                            $company_track_attributes->query
                        }
                        companies (input: \$contact_companies_input) {
                            itemsFound
                            items {
                                companyId
                                name
                                taxNumber
                                cocNumber
                                debtorId 
                                email
                                addresses {
                                    ... WPAddressFragment
                                }
                                $company_track_attributes->query
                            }
                        }
                        purchaseAuthorizationConfigs {
                            itemsFound
                            items {
                                purchaseRole
                                authorizationLimit
                                company {
                                    companyId
                                }
                            }
                        }
                        $user_track_attributes->query
                    }
                    ... on Customer {
                        userId: customerId
                        debtorId
                        addresses {
                            ... WPAddressFragment
                        }
                        $user_track_attributes->query
                    }
                }
            }             
        ";

        $queries = [
            !empty($user_track_attrs_arg) ? self::attributes_fragment()->query : '',
            self::base_user_fragment()->query,
            empty($user_track_attrs_arg) && !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            AddressModel::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'user_id' => intval($user_args['user_id']),
            'user_track_attrs_filter' => $user_track_attributes->variables,
            'company_track_attrs_filter' => $company_track_attributes->variables,
            'contact_companies_input' => [
                'page' => 1,
                'offset' => 50
            ]
        ];

        return $return;
    }

    public function get_contacts($contacts_args)
    {
        $user_track_attributes = $this->user_track_attributes();
        $user_track_attrs_arg = !empty($user_track_attributes->query) ? "\$user_track_attrs_filter: AttributeResultSearchInput!" : "";

        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            query WPGetContactsQuery(
                \$input: ContactSearchArguments
                $user_track_attrs_arg
                $company_track_attrs_arg
                \$contact_companies_input: ContactCompaniesSearchInput
            ){
                contacts(input: \$input) {
                    itemsFound
                    items {
                        userId: contactId
                        debtorId
                        company {
                            companyId
                            name
                            taxNumber
                            cocNumber
                            debtorId 
                            email
                            addresses {
                                ... WPAddressFragment
                            }
                            $company_track_attributes->query
                        }
                        companies (input: \$contact_companies_input) {
                            itemsFound
                            items {
                                companyId
                                name
                                taxNumber
                                cocNumber
                                debtorId 
                                email
                                addresses {
                                    ... WPAddressFragment
                                }
                                $company_track_attributes->query
                            }
                        }
                        purchaseAuthorizationConfigs {
                            itemsFound
                            items {
                                purchaseRole
                                authorizationLimit
                                company {
                                    companyId
                                }
                            }
                        }
                        $user_track_attributes->query
                    }
                }
            }             
        ";

        $queries = [
            !empty($user_track_attrs_arg) ? self::attributes_fragment()->query : '',
            // self::base_user_fragment()->query,
            empty($user_track_attrs_arg) && !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            AddressModel::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'input' => $contacts_args,
            'user_track_attrs_filter' => $user_track_attributes->variables,
            'company_track_attrs_filter' => $company_track_attributes->variables,
            'contact_companies_input' => [
                'page' => 1,
                'offset' => 50
            ]
        ];

        return $return;
    }

    public function trigger_customer_welcome($user_args)
    {
        $gql = "
            mutation WPCustomerSendWelcome(
                \$customer_welcome_input: TriggerCustomerSendWelcomeEmailEventInput!
            ) {
                triggerCustomerSendWelcomeEmailEvent(input: \$customer_welcome_input)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'customer_welcome_input' => $user_args
        ];

        return $return;
    }

    public function trigger_contact_welcome($user_args)
    {
        $gql = "
            mutation WPContactSendWelcome(
                \$contact_welcome_input: TriggerContactSendWelcomeEmailEventInput!
            ) {
                triggerContactSendWelcomeEmailEvent(input: \$contact_welcome_input)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'contact_welcome_input' => $user_args
        ];

        return $return;
    }

    public function trigger_password_reset($user_args)
    {
        $gql = "
            mutation WPTriggerInitResetPassword (
                \$pass_reset_input: PasswordRecoveryLinkInput!
            ) {
                triggerPasswordSendResetEmailEvent(input: \$pass_reset_input)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'pass_reset_input' => $user_args
        ];

        return $return;
    }

    public function assign_to_pricesheet($user_args)
    {
        $gql = "
            mutation WPPricesheetAssign (
                \$id: String!
                \$pricesheet_input: PricesheetAssignInput!
            ) {
                pricesheetAssign(id: \$id input: \$pricesheet_input) {
                    id
                }
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $user_args;

        return $return;
    }

    public function create_magic_token($magic_token_input) {
        $gql = "
            mutation WPMagicTokenCreate (
                \$magic_token_input: MagicTokenCreateInput!
            ) {
                magicTokenCreate(input: \$magic_token_input) {
                    id
                    expiresAt
                    extra
                }
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $magic_token_input;

        return $return;
    }
}
