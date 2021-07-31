<?php

namespace wcf\acp\form;

use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Shows the general package permission edit form.
 *
 * @author  Maximilian Mader
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
class PackageServerPackageGeneralPermissionEditForm extends PackageServerPackageGeneralPermissionAddForm
{
    /**
     * @see \wcf\page\AbstractPage::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver';

    /**
     * The permission value
     * @var string
     */
    public $permissionEntry;

    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['packageIdentifier'])) {
            $this->packageIdentifier = \wcf\util\StringUtil::trim($_REQUEST['packageIdentifier']);
        }

        $sql = "SELECT  *
                FROM    wcf" . WCF_N . "_packageserver_package_permission_general
                WHERE   packageIdentifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->packageIdentifier,
        ]);

        $this->permissionEntry = $statement->fetchArray();

        if (!$this->permissionEntry) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate()
    {
        AbstractForm::validate();

        if (empty($this->permissionString)) {
            throw new UserInputException('permissionString');
        }
    }

    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData()
    {
        parent::readData();

        if (empty($_POST)) {
            $this->permissionString = $this->permissionEntry['permissionString'];
        }
    }

    /**
     * @see \wcf\form\IForm::save()
     */
    public function save()
    {
        AbstractForm::save();

        $sql = "UPDATE  wcf" . WCF_N . "_packageserver_package_permission_general
                SET     permissionString = ?
                WHERE   packageIdentifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->permissionString,
            $this->packageIdentifier,
        ]);

        PackageServerUtil::generateAuthFile();

        $this->saved();

        // show success
        WCF::getTPL()->assign([
            'success' => true,
        ]);
    }

    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'action' => 'edit',
        ]);
    }
}
