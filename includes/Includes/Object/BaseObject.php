<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\PropellerUtils;

#[\AllowDynamicProperties]
class BaseObject {
    public function __construct(mixed $object) {
        if (is_object($object) && !is_array($object))
            $this->merge_object($object);
    }

    protected function merge_object(object $object): void {
        foreach($object as $key => $value)
            $this->$key = $value;
    }

	/**
	 * Sanitize frontend input
	 *
	 * @param mixed $data
	 *
	 * @return array
	 */
    public function sanitize(mixed $data): array {
		return PropellerUtils::sanitize($data);
    }
}
