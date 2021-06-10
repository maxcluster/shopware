<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Components\Api\Resource;

use PHPUnit\Framework\TestCase;
use Shopware\Components\Api\Resource\Article;

class ArticleTest extends TestCase
{
    private const TAX_RATE = 19;
    private const PRICE_NET = 16.798319327731;
    private const PRICE_GROSS = self::PRICE_NET * ((self::TAX_RATE + 100) / 100);
    private const PSEUDOPRICE_NET = 25.201680672269;
    private const PSEUDOPRICE_GROSS = self::PSEUDOPRICE_NET * ((self::TAX_RATE + 100) / 100);

    private Article $articleResource;

    protected function setUp(): void
    {
        $this->articleResource = new Article($this->createMock(\Shopware_Components_Translation::class));

        parent::setUp();
    }

    /**
     * @dataProvider priceProvider
     *
     * @param array<string, mixed> $price
     */
    public function testGetTaxPrice(array $price): void
    {
        $considerTaxInput = $price['customerGroup']['taxInput'];

        if ($price['price']) {
            static::assertEquals(
                $considerTaxInput ? self::PRICE_GROSS : self::PRICE_NET,
                $this->articleResource->getTaxPrices([$price], self::TAX_RATE)[0]['price']
            );
        }

        if ($price['pseudoPrice']) {
            static::assertEquals(
                $considerTaxInput ? self::PSEUDOPRICE_GROSS : self::PSEUDOPRICE_NET,
                $this->articleResource->getTaxPrices([$price], self::TAX_RATE)[0]['pseudoPrice']
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function priceProvider(): iterable
    {
        yield 'price only' => [
            [
                'id' => 1,
                'articleId' => 1,
                'articleDetailsId' => 1,
                'customerGroupKey' => 'EK',
                'from' => 1,
                'to' => 'beliebig',
                'price' => 16.798319327731,
                'pseudoPrice' => 0.0,
                'percent' => 0.0,
                'customerGroup' => [
                    'id' => 1,
                    'key' => 'EK',
                    'name' => 'Shopkunden',
                    'tax' => true,
                    'taxInput' => false,
                    'mode' => false,
                    'discount' => 0.0,
                    'minimumOrder' => 0.0,
                    'minimumOrderSurcharge' => 0.0,
                ],
            ],
        ];

        yield 'price only, consider tax input' => [
            [
                'id' => 1,
                'articleId' => 1,
                'articleDetailsId' => 1,
                'customerGroupKey' => 'EK',
                'from' => 1,
                'to' => 'beliebig',
                'price' => 16.798319327731,
                'pseudoPrice' => 0.0,
                'percent' => 0.0,
                'customerGroup' => [
                    'id' => 1,
                    'key' => 'EK',
                    'name' => 'Shopkunden',
                    'tax' => true,
                    'taxInput' => true,
                    'mode' => false,
                    'discount' => 0.0,
                    'minimumOrder' => 0.0,
                    'minimumOrderSurcharge' => 0.0,
                ],
            ],
        ];

        yield 'price, pseudoprice' => [
            [
                'id' => 1,
                'articleId' => 1,
                'articleDetailsId' => 1,
                'customerGroupKey' => 'EK',
                'from' => 1,
                'to' => 'beliebig',
                'price' => 16.798319327731,
                'pseudoPrice' => 25.201680672269,
                'percent' => 0.0,
                'customerGroup' => [
                    'id' => 1,
                    'key' => 'EK',
                    'name' => 'Shopkunden',
                    'tax' => true,
                    'taxInput' => false,
                    'mode' => false,
                    'discount' => 0.0,
                    'minimumOrder' => 0.0,
                    'minimumOrderSurcharge' => 0.0,
                ],
            ],
        ];

        yield 'price, pseudoprice, consider tax input' => [
            [
                'id' => 1,
                'articleId' => 1,
                'articleDetailsId' => 1,
                'customerGroupKey' => 'EK',
                'from' => 1,
                'to' => 'beliebig',
                'price' => 16.798319327731,
                'pseudoPrice' => 25.201680672269,
                'percent' => 0.0,
                'customerGroup' => [
                    'id' => 1,
                    'key' => 'EK',
                    'name' => 'Shopkunden',
                    'tax' => true,
                    'taxInput' => true,
                    'mode' => false,
                    'discount' => 0.0,
                    'minimumOrder' => 0.0,
                    'minimumOrderSurcharge' => 0.0,
                ],
            ],
        ];
    }
}
