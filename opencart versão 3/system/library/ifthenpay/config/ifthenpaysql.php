<?php

declare(strict_types=1);

namespace Ifthenpay\Config;

use Ifthenpay\Contracts\Config\InstallerInterface;

class IfthenpaySql implements InstallerInterface
{
    private $ifthenpayModel;
    private $userPaymentMethods;

    private $ifthenpaySqlTables = [
        'multibanco' => 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'ifthenpay_multibanco` (
            `id_ifthenpay_multibanco` int(10) unsigned NOT NULL auto_increment,
            `entidade` varchar(5) NOT NULL,
            `referencia` varchar(9) NOT NULL,
            `order_id` int(11) NOT NULL,
            `status` varchar(50) NOT NULL,
            PRIMARY KEY  (`id_ifthenpay_multibanco`),
            INDEX `referencia` (`referencia`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
        'mbway' => 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'ifthenpay_mbway` (
            `id_ifthenpay_mbway` int(10) unsigned NOT NULL auto_increment,
            `id_transacao` varchar(20) NOT NULL,
            `telemovel` varchar(20) NOT NULL,
            `order_id` int(11) NOT NULL,
            `status` varchar(50) NOT NULL,
            PRIMARY KEY  (`id_ifthenpay_mbway`),
            INDEX `idTransacao` (`id_transacao`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
        'payshop' => 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'ifthenpay_payshop` (
            `id_ifthenpay_payshop` int(10) unsigned NOT NULL auto_increment,
            `id_transacao` varchar(20) NOT NULL,
            `referencia` varchar(13) NOT NULL,
            `validade` varchar(8) NOT NULL,
            `order_id` int(11) NOT NULL,
            `status` varchar(50) NOT NULL,
            PRIMARY KEY  (`id_ifthenpay_payshop`),
            INDEX `idTransacao` (`id_transacao`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
          'ccard' => 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'ifthenpay_ccard` (
            `id_ifthenpay_ccard` int(10) unsigned NOT NULL auto_increment,
            `requestId` varchar(50) NOT NULL,
            `paymentUrl` varchar(250) NOT NULL,
            `order_id` int(11) NOT NULL,
            `status` varchar(50) NOT NULL,
            PRIMARY KEY  (`id_ifthenpay_ccard`),
            INDEX `requestId` (`requestId`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
    ];

    private function createIfthenpaySql(): void
    {
        foreach ($this->userPaymentMethods as $paymentMethod) {
            $sql = $this->ifthenpayModel->db->query($this->ifthenpaySqlTables[$paymentMethod]);
            if (!$sql) {
                throw new \Exception('Error creating ifthenpay payment table!');
            }
        }
    }

    
    private function deleteIfthenpaySql(): void
    {
        foreach ($this->userPaymentMethods as $paymentMethod) {
                $sql = $this->ifthenpayModel->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . 'ifthenpay_' . $paymentMethod);
            if (!$sql) {
                throw new \Exception('Error deleting ifthenpay payment table!');
            }
        }
    }


    public function install(): void
    {
        $this->createIfthenpaySql();
    }

    public function uninstall(): void
    {
        if ($this->userPaymentMethods) {
            $this->deleteIfthenpaySql();
        }
    }

    /**
     * Set the value of ifthenpayModule
     *
     * @return  self
     */ 
    public function setIfthenpayModel(\ModelExtensionPaymentIfthenpay $ifthenpayModel)
    {
        $this->ifthenpayModel = $ifthenpayModel;

        return $this;
    }

    /**
     * Set the value of userPaymentMethods
     *
     * @return  self
     */ 
    public function setUserPaymentMethods($userPaymentMethods)
    {
        $this->userPaymentMethods = $userPaymentMethods;

        return $this;
    }
}
