<?php

declare(strict_types=1);

namespace Sage\Type\Definition;

use Sage\Error\Error;
use Sage\Utils\Utils;
use function floor;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;

class IntType extends ScalarType
{
  /**
   * As per the Sage specification, Integers are only treated as valid when a valid
   * 32-bit signed integer, providing the broadest support across platforms.
   * n.b. JavaScript's integers are safe between -(2^53 - 1) and 2^53 - 1 because
   * they are internally represented as IEEE 754 doubles.
   */

  private const MAX_INT = 2147483647;
  private const MIN_INT = -2147483648;

  /** @var string */
  public $name = Type::INT;

  /** @var string */
  public $description =
  'The `Int` scalar type represents non-fractional signed whole numeric
values. Int can represent values between -(2^31) and 2^31 - 1. ';

  /**
   * @param mixed $value
   *
   * @return int|null
   *
   * @throws Error
   */
  public function serialize($value)
  {
    // Fast path for 90+% of cases:
    if (is_int($value) && $value <= self::MAX_INT && $value >= self::MIN_INT) {
      return $value;
    }

    $float = is_numeric($value) || is_bool($value)
      ? (float) $value
      : null;

    if ($float === null || floor($float) !== $float) {
      throw new Error(
        'Int cannot represent non-integer value: ' .
          Utils::printSafe($value)
      );
    }

    if ($float > self::MAX_INT || $float < self::MIN_INT) {
      throw new Error(
        'Int cannot represent non 32-bit signed integer value: ' .
          Utils::printSafe($value)
      );
    }

    return (int) $float;
  }

  /**
   * @param mixed $value
   *
   * @throws Error
   */
  public function parseValue($value): int
  {
    $isInt = is_int($value) || (is_float($value) && floor($value) === $value);

    if (!$isInt) {
      throw new Error(
        'Int cannot represent non-integer value: ' .
          Utils::printSafe($value)
      );
    }

    if ($value > self::MAX_INT || $value < self::MIN_INT) {
      throw new Error(
        'Int cannot represent non 32-bit signed integer value: ' .
          Utils::printSafe($value)
      );
    }

    return (int) $value;
  }
}
