<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Adapter\PhpSession;
use Zend\Expressive\Authentication\Adapter\PhpSessionFactory;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class PhpSessionFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new PhpSessionFactory();
        $this->userRegister = $this->prophesize(UserRepositoryInterface::class);
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithEmptyContainer()
    {
        $phpSession = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithContainerEmptyConfig()
    {
        $this->container->has(UserRepositoryInterface::class)
                        ->willReturn(true);
        $this->container->get(UserRepositoryInterface::class)
                        ->willReturn($this->userRegister->reveal());
        $this->container->get('config')
                        ->willReturn([]);

        $phpSession = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithContainerAndConfig()
    {
        $this->container->has(UserRepositoryInterface::class)
                        ->willReturn(true);
        $this->container->get(UserRepositoryInterface::class)
                        ->willReturn($this->userRegister->reveal());
        $this->container->get('config')
                        ->willReturn([
                            'authentication' => ['redirect' => '/login']
                        ]);

        $phpSession = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(PhpSession::class, $phpSession);
    }
}