<?php

namespace wcf\acp\action;

use wcf\action\AbstractAction;
use wcf\data\package\Package;
use wcf\util\HeaderUtil;
use wcf\util\PackageServerUtil;
use wcf\util\StringUtil;

/**
 * Deletes package versions.
 *
 * @author  Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerDeletePackageVersionAction extends AbstractAction
{
    /**
     * * @inheritDoc
     */
    public $neededPermissions = ['admin.packageServer.canManagePackages'];

    /**
     * Package identifier
     * @var string
     */
    public $packageIdentifier = '';

    /**
     * The package version string
     * @var string
     */
    public $version = '';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_GET['packageIdentifier'])) {
            $this->packageIdentifier = StringUtil::trim($_GET['packageIdentifier']);
        }
        if (isset($_GET['version'])) {
            $this->version = StringUtil::trim($_GET['version']);
        }

        if (
            !Package::isValidPackageName($this->packageIdentifier)
            || !Package::isValidVersion($this->version)
            || !\is_file(PackageServerUtil::getPackageServerPath() . $this->packageIdentifier . '/' . PackageServerUtil::transformPackageVersion($this->version) . '.tar')
        ) {
            throw new \wcf\system\exception\IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        if (@\unlink(PackageServerUtil::getPackageServerPath() . $this->packageIdentifier . '/' . PackageServerUtil::transformPackageVersion($this->version) . '.tar') === false) {
            throw new \wcf\system\exception\SystemException('could not delete package');
        }

        HeaderUtil::redirect(\wcf\system\request\LinkHandler::getInstance()->getLink('PackageServerPackageList'));

        exit;
    }
}
