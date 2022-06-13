<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Builders;

use Ifthenpay\Contracts\Builders\DataBuilderInterface;

interface GatewayDataBuilderInterface extends DataBuilderInterface
{
    public function setSubEntidade(string $value): GatewayDataBuilderInterface;
    public function setMbwayKey(string $value): GatewayDataBuilderInterface;
    public function setPayshopKey(string $value): GatewayDataBuilderInterface;
    public function setCCardKey(string $value): GatewayDataBuilderInterface;   
}
