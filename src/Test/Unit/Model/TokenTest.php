<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Test\Unit\Model;

use Siteimprove\Magento\Model\Token;

/**
 * Test fetching of the token from the flag class
 *
 * @see \Siteimprove\Magento\Model\Token
 * @package Siteimprove\Magento\Test\Unit\Model
 */
class TokenTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Siteimprove\Magento\Model\Flag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flag;

    /**
     * @var Token
     */
    protected $model;

    protected function setUp()
    {
        $this->flag = $this->createPartialMock(
            \Siteimprove\Magento\Model\Flag::class,
            [
                'loadSelf',
                'getFlagData',
            ]
        );

        $this->model = new Token($this->flag);
    }

    /**
     * If token is stored then ensure it is returned
     */
    public function testGetToken()
    {
        $testToken = 'mscwt8t3ppemj474z2hnhnjqj288tmxm';
        $this->flag->expects($this->at(0))->method('getFlagData')->willReturn(null);
        $this->flag->expects($this->at(1))->method('loadSelf');
        $this->flag->expects($this->at(2))->method('getFlagData')->willReturn(
            $testToken
        );

        $this->assertEquals($testToken, $this->model->getToken());
    }

    /**
     * If no token stored ensure empty string is returned
     */
    public function testGetEmptyToken()
    {
        $this->flag->expects($this->at(0))->method('getFlagData')->willReturn(null);
        $this->flag->expects($this->at(1))->method('loadSelf');
        $this->flag->expects($this->at(2))->method('getFlagData')->willReturn(null);

        $this->assertSame('', $this->model->getToken());
    }
}
