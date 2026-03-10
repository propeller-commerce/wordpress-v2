<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<?php if (isset($product->bulkPrices) && sizeof($product->bulkPrices) >= 1) {

    // Filter bulk prices based on date validity and remove duplicates
    $filteredBulkPrices = [];
    $currentDate = new DateTime();
    $quantityGroups = [];

    // Group bulk prices by quantity first
    foreach ($product->bulkPrices as $bulkPrice) {
        $quantityFrom = $bulkPrice->from ?? ($bulkPrice->discount->quantityFrom ?? null);

        if (!isset($quantityGroups[$quantityFrom])) {
            $quantityGroups[$quantityFrom] = [];
        }
        $quantityGroups[$quantityFrom][] = $bulkPrice;
    }

    // Process each quantity group
    foreach ($quantityGroups as $quantity => $prices) {
        $validPrices = [];
        $nullDatePrices = [];

        foreach ($prices as $bulkPrice) {
            // If no discount information, keep original logic
            if (!isset($bulkPrice->discount)) {
                $filteredBulkPrices[] = $bulkPrice;
                continue;
            }

            $validFrom = $bulkPrice->discount->validFrom ?? null;
            $validTo = $bulkPrice->discount->validTo ?? null;
            $hasNullDates = ($validFrom === null && $validTo === null);

            if ($hasNullDates) {
                $nullDatePrices[] = $bulkPrice;
            } else {
                // Check if this bulk price is currently valid
                $isValid = true;
                if ($validFrom !== null) {
                    $validFromDate = new DateTime($validFrom);
                    if ($currentDate < $validFromDate) {
                        $isValid = false;
                    }
                }
                if ($validTo !== null && $isValid) {
                    $validToDate = new DateTime($validTo);
                    if ($currentDate > $validToDate) {
                        $isValid = false;
                    }
                }

                if ($isValid) {
                    $validPrices[] = $bulkPrice;
                }
            }
        }

        // Selection logic for ALL quantities:
        // 1. If there are valid date-based prices, use the first one found
        // 2. Otherwise, use null date prices (permanently valid)
        if (!empty($validPrices)) {
            $filteredBulkPrices[] = $validPrices[0];
        } elseif (!empty($nullDatePrices)) {
            $filteredBulkPrices[] = $nullDatePrices[0];
        }
    }

    // Recalculate 'to' values based on filtered results
    usort($filteredBulkPrices, function ($first, $second) {
        $firstFrom = $first->from ?? ($first->discount->quantityFrom ?? 0);
        $secondFrom = $second->from ?? ($second->discount->quantityFrom ?? 0);
        return $firstFrom <=> $secondFrom;
    });

    for ($i = 0; $i < count($filteredBulkPrices); $i++) {
        // Ensure 'from' is set correctly
        $filteredBulkPrices[$i]->from = $filteredBulkPrices[$i]->from ?? ($filteredBulkPrices[$i]->discount->quantityFrom ?? null);

        // Recalculate 'to' based on filtered results
        if ($i < count($filteredBulkPrices) - 1) {
            $nextFrom = $filteredBulkPrices[$i + 1]->from ?? ($filteredBulkPrices[$i + 1]->discount->quantityFrom ?? null);
            $filteredBulkPrices[$i]->to = $nextFrom - 1;
        } else {
            // Last item has no upper limit
            unset($filteredBulkPrices[$i]->to);
        }
    }

    // Additional check: Don't show bulk prices if there's only one price for quantity 1
    if (count($filteredBulkPrices) === 1 && $filteredBulkPrices[0]->from == 1) {
        return;
    }
?>
    <div class="bulk-prices-wrapper d-none d-md-block">
        <div class="row bulk-prices">
            <div class="col-12">
                <?php foreach ($filteredBulkPrices as $bulkPrice) { ?>
                    <div class="bulk-price-row row justify-content-between g-0" data-min="<?php echo esc_html($bulkPrice->from); ?>" data-max="<?php echo esc_html($bulkPrice->to); ?>">
                        <div class="col"><?php echo esc_html($bulkPrice->from); ?><?php if (isset($bulkPrice->to)) { ?>-<?php echo esc_html($bulkPrice->to); ?><?php } else {
                                                                                                                                                                echo '+';
                                                                                                                                                            } ?></div>
                        <div class="col-6 d-flex justify-content-end"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($user_prices ? $bulkPrice->net : $bulkPrice->gross)); ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
