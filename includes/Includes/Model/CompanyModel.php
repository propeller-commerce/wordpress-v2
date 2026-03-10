<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class CompanyModel extends BaseModel
{
    public function __construct() {}

    public function create($company_args)
    {
        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            mutation WPCompanyCreateMutation(
                \$company_input: CreateCompanyInput!
                $company_track_attrs_arg 
            ){
                companyCreate(input: \$company_input) {
                    ... WPCompanyFragment
                }
            }
        ";

        $queries = [
            self::company_fragment()->query,
            !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'company_input' => $company_args
        ];

        if (!empty($company_track_attrs_arg))
            $return->variables['company_track_attrs_filter'] = $company_track_attributes->variables;

        return $return;
    }

    public function get($company_args)
    {
        $company_track_attributes = self::company_track_attributes();
        $company_track_attrs_arg = !empty($company_track_attributes->query) ? "\$company_track_attrs_filter: AttributeResultSearchInput!" : "";

        $gql = "
            query WPCompanyQuery(
                \$company_id: Fload
                $company_track_attrs_arg 
            ){
                company(companyId: \$company_id) {
                    ... WPCompanyFragment
                }
            }
        ";

        $queries = [
            self::company_fragment()->query,
            !empty($company_track_attrs_arg) ? self::attributes_fragment()->query : '',
            AddressModel::address_fragment()->query,
            $gql
        ];

        if (!empty($company_track_attrs_arg))
            $queries[] = $company_track_attributes->query;

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'company_id' => $company_args['company_id']
        ];

        if (!empty($company_track_attrs_arg))
            $return->variables['company_track_attrs_filter'] = $company_track_attributes->variables;

        return $return;
    }

    public static function company_fragment()
    {
        $company_track_attributes = self::company_track_attributes();

        $gql = "
            fragment WPCompanyFragment on Company {
                companyId
                name
                taxNumber
                cocNumber
                addresses {
                    ... WPAddressFragment
                }
                $company_track_attributes->query
            }
        ";

        $queries = [
            AddressModel::address_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }
}
