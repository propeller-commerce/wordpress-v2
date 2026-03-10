<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class WarehouseModel extends BaseModel {
    public function __construct() {
        
    }

    public function get_warehouses($warehouse_args) {
        $gql = '
            query WPWarehousesQuery(
                $warehouses_args: WarehousesSearchInput
            ){
                warehouses(input: $warehouses_args) {
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                    items {
                        ... WPWarehouseFragment
                    }
                }
            }
        ';

        $queries = [
            self::warehouse_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $warehouse_args;
        
        return $return;
    }

    public static function warehouse_fragment() {
        $gql = '
            fragment WPWarehouseFragment on Warehouse {
                id
                address {
                    ... WPWarehouseAddressFragment                            
                }
                name
                description
                notes
                isActive
                isStore
                isPickupLocation
                businessHours {
                    ... WPWarehouseBusinessHoursFragment
                }
            }
        ';

        $queries = [
            self::warehouse_address_fragment()->query,
            self::warehouse_business_hours_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    public static function warehouse_address_fragment() {
        $gql = '
            fragment WPWarehouseAddressFragment on WarehouseAddress {
                id
                code
                name
                url
                firstName
                middleName
                lastName
                email
                gender
                country
                city
                street
                number
                numberExtension
                region
                postalCode
                company
                phone
                notes
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function warehouse_business_hours_fragment() {
        $gql = '
            fragment WPWarehouseBusinessHoursFragment on BusinessHours {
                dayOfWeek
                openingTime
                closingTime
                lunchBeakStartTime
                lunchBeakEndTime
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
}
