<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * HotFix module main class.
 */
class HotFix extends Module
{
    /** @var Array Module's settings. */
    private $settings = array();

    /**
     * Module's constructor.
     */
    public function __construct()
    {
        // Module's base configuration
        $this->name = 'hotfix';
        $this->author = 'PrestaShop';
        $this->version = '0.1';

        parent::__construct();

        // Module's presentation
        $this->displayName = $this->l('HotFix');
        $this->description = $this->l('Security & important updates patcher.');

        // Require the Hotfix classes loader and the main classes
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'HotfixClassesLoader.php';
        HotfixClassesLoader::loadClasses(array(
            'Settings'
        ));

        // Load the settings.
        $this->settings = new HotfixSettings(
            include(dirname(__FILE__).DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'settings.php')
        );
    }

    /**
     * Module installation.
     */
    public function install()
    {
        HotfixClassesLoader::loadClass('Installation');
        $installation = new HotfixInstallation();

        return parent::install()
            && $installation->installTables()
            && $installation->createFolder($this->settings->get('paths/backup'))
            && $installation->createFolder($this->settings->get('paths/patches'))
            && $this->registerHook('displayBackOfficeFooter');


    }

    /**
     * Module uninstallation.
     */
    public function uninstall()
    {
        HotfixClassesLoader::loadClass('Installation');
        $installation = new HotfixInstallation();

        return $installation->removeTables()
            && $installation->removeFolder($this->settings->get('paths/backup'))
            && $installation->removeFolder($this->settings->get('paths/patches'))
            && parent::uninstall();
    }
}
