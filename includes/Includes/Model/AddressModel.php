<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class AddressModel extends BaseModel
{
    public function __construct() {}

    public function get_addresses($addr_query, $addr_args)
    {
        $variable = isset($addr_args['companyId']) ? 'companyId' : 'customerId';
        $argument = isset($addr_args['companyId']) ? '$companyId' : '$customerId';

        $gql = "
            query WPAddressQuery(
                $argument: Float!
                \$type: AddressType
            ){
                $addr_query(type: \$type $variable: $argument) {
                    ... WPAddressFragment
                }
            }        
        ";

        $queries = [
            self::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $addr_args;

        return $return;
    }

    public function get_addresses_cart($addr_type, $arguments)
    {
        $input_defs = isset($arguments['company_id']) ? '$company_id: Float!' : '$customer_id: Float!';
        $input_vals = isset($arguments['company_id']) ? 'companyId: $company_id' : 'customerId: $customer_id';

        $gql = "
            query WPCartAddressFragment(
                $input_defs
                \$address_type: AddressType
            ) {
                $addr_type(type: \$address_type $input_vals) {
                    id
                    code
                    firstName
                    middleName
                    lastName
                    gender
                    email
                    country
                    city
                    street
                    number
                    numberExtension
                    postalCode
                    company
                    phone
                    notes
                    icp
                    type
                    isDefault
                }
            }
        ";


        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $arguments;

        return $return;
    }

    public static function address_fragment()
    {
        $gql = '
            fragment WPAddressFragment on Address {
                id
                code
                firstName
                middleName
                lastName
                gender
                email
                country
                city
                street
                number
                numberExtension
                postalCode
                company
                phone
                notes
                icp
                type
                isDefault
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function get_external_address($address_args)
    {
        $gql = '
            query WPExternalAddressQuery(
                $id: Float!
            ){
                externalAddress (id: $id) {
                    id
                    code
                    firstName
                    middleName
                    lastName
                    gender
                    email
                    country
                    city
                    street
                    number
                    numberExtension
                    postalCode
                    company
                    phone
                    notes
                    icp
                }
            }        
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $address_args;

        return $return;
    }

    public function add_address($type, $address_args)
    {
        $input_type = isset($address_args['companyId']) ? "CompanyAddressCreateInput" : "CustomerAddressCreateInput";

        $gql = "
            mutation WPAddressCreateMutation(
                \$address_args: $input_type!
            ){
                $type(input: \$address_args) {
                    id
                }
            }        
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'address_args' => $address_args
        ];

        return $return;
    }

    public function update_address($type, $address_args)
    {
        $input_type = isset($address_args['companyId']) ? "CompanyAddressUpdateInput" : "CustomerAddressUpdateInput";

        $gql = "
            mutation WPAddressUpdateMutation(
                \$address_args: $input_type!
            ){
                $type(input: \$address_args) {
                    id
                }
            }           
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'address_args' => $address_args
        ];

        return $return;
    }

    public function delete_address($type, $address_args)
    {
        $input_type = isset($address_args['companyId']) ? "CompanyAddressDeleteInput" : "CustomerAddressDeleteInput";

        $gql = "
            mutation WPAddressDeleteMutation(
                \$address_args: $input_type!
            ){
                $type(input: \$address_args)
            }        
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'address_args' => $address_args
        ];

        return $return;
    }
}
