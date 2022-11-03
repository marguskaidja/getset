<?php

/**
 * This file is part of the margusk/accessors package.
 *
 * @author  Margus Kaidja <margusk@gmail.com>
 * @link    https://github.com/marguskaidja/php-accessors
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

declare(strict_types=1);

namespace margusk\Accessors\Tests;

use margusk\Accessors\Accessible;
use margusk\Accessors\Attr\{Get, ICase as CI, Set};
use margusk\Accessors\Exception\InvalidArgumentException;

class CombinedTest extends TestCase
{
    public function test_direct_access_with_case_sensitivity_and_no_match_must_fail(): void
    {
        $obj = new #[Get] class {
            use Accessible;

            protected string $PropertY = 'some value';
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/tried to get unknown property/');

        /**
         * @noinspection PhpExpressionResultUnusedInspection
         * @phpstan-ignore-next-line
         */
        $obj->propertY;
    }

    public function test_flexible_method_call_access_with_case_sensitivity_and_no_match_must_fail(): void
    {
        $obj = new #[Get] class {
            use Accessible;

            protected string $PropertY = 'some value';
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/tried to get unknown property/');

        /** @phpstan-ignore-next-line */
        $obj->get('PROPerty');
    }

    public function test_property_name_is_case_insensitive_when_accessing_in_method_syntax(): void
    {
        $obj = new #[Get, Set] class {
            use Accessible;

            protected string $PropertY = 'some value';
        };

        $this->assertEquals(
            'some value',
            /** @phpstan-ignore-next-line */
            $obj->propertY()
        );

        /** @phpstan-ignore-next-line */
        $obj->setPROPERTY('new value');

        $this->assertEquals(
            'new value',
            /** @phpstan-ignore-next-line */
            $obj->getpRoPertY()
        );
    }

    public function test_property_name_is_case_insensitive_when_accessing_in_direct_syntax(): void
    {
        $obj = new #[Get, Set, CI] class {
            use Accessible;

            protected string $PropertY;
        };

        $value = 'some value';

        /** @phpstan-ignore-next-line */
        $obj->proPerTy = $value;

        $this->assertEquals(
            $value,
            /** @phpstan-ignore-next-line */
            $obj->prOperTy
        );
    }

    public function test_parent_class_is_parsed_correctly_later_when_child_is_accessed_first(): void
    {
        $child = new #[Get, Set, CI] class extends ParentTestClassForAccessOrder {
            use Accessible;
        };

        $parent = new ParentTestClassForAccessOrder();

        $childPropertyValue = 'child property value';
        $parentPropertyValue = 'parent property value';

        $child->parentProperty = $childPropertyValue;
        $parent->parentProperty = $parentPropertyValue;

        $this->assertEquals($childPropertyValue, $child->parentProperty);
        $this->assertEquals($parentPropertyValue, $parent->parentProperty);
    }

    public function test_named_arguments_to_accessor_method_are_handled(): void
    {
        $obj = new #[Get, Set] class {
            use Accessible;

            protected string $a = 'some value';

            public function getAValue(): string
            {
                return $this->a;
            }
        };

        $expectedValue = 'new value';

        /** @phpstan-ignore-next-line */
        $obj->set(a: ['a' => $expectedValue]);

        $this->assertEquals(
            $expectedValue,
            $obj->getAValue()
        );
    }
}
