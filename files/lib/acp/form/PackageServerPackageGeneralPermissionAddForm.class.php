<?php

namespace wcf\acp\form;

use wcf\data\package\Package;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * A form for add package permissions
 *
 * @author  Tim Düsterhus, Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerPackageGeneralPermissionAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addGeneralPermission';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.packageServer.canManagePackages'];

    /**
     * Package identifier
     * @var string
     */
    public $packageIdentifier = '';

    /**
     * Permission string
     * @var string
     */
    public $permissionString = '';

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_POST['packageIdentifier'])) {
            $this->packageIdentifier = $_POST['packageIdentifier'];
        }
        if (isset($_POST['permissionString'])) {
            $this->permissionString = $_POST['permissionString'];
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        if (empty($this->packageIdentifier)) {
            throw new UserInputException('packageIdentifier');
        }

        if (!Package::isValidPackageName($this->packageIdentifier)) {
            throw new UserInputException('packageIdentifier', 'notValid');
        }

        if (empty($this->permissionString)) {
            throw new UserInputException('permissionString');
        }

        $sql = "SELECT  COUNT(*)
                FROM    wcf" . WCF_N . "_packageserver_package_permission_general
                WHERE   packageIdentifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->packageIdentifier,
        ]);

        if ($statement->fetchColumn()) {
            throw new UserInputException('packageIdentifier', 'existing');
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $sql = "INSERT INTO wcf" . WCF_N . "_packageserver_package_permission_general
                    (packageIdentifier, permissionString)
                VALUES
                    (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->packageIdentifier,
            $this->permissionString,
        ]);

        // regenerate auth file @TODO, better solution work in progress
        PackageServerUtil::generateAuthFile();

        $this->saved();

        $this->packageIdentifier = $this->permissionString = "";

        // show success
        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'permissionString' => $this->permissionString,
            'packageIdentifier' => $this->packageIdentifier,
            'action' => 'add',
        ]);
    }
}
