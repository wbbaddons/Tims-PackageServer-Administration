<?php

namespace wcf\acp\form;

use wcf\data\user\User;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;
use wcf\util\StringUtil;

/**
 * Shows the package user permission edit form.
 *
 * @author  Maximilian Mader
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerPackageUserPermissionEditForm extends PackageServerPackageUserPermissionAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver';

    /**
     * User id
     * @var integer
     */
    public $userID = 0;

    /**
     * Benefited user
     * @var User
     */
    public $user;

    /**
     * The permission value
     * @var string
     */
    public $permissionEntry;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['packageIdentifier'])) {
            $this->packageIdentifier = StringUtil::trim($_REQUEST['packageIdentifier']);
        }
        if (isset($_REQUEST['userID'])) {
            $this->userID = \intval($_REQUEST['userID']);
        }

        $this->user = new User($this->userID);

        if (!$this->user->userID) {
            throw new IllegalLinkException();
        }

        $sql = "SELECT  *
                FROM    wcf" . WCF_N . "_packageserver_package_to_user
                WHERE   packageIdentifier = ?
                    AND userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$this->packageIdentifier, $this->user->userID]);

        $this->permissionEntry = $statement->fetchArray();

        if (!$this->permissionEntry) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        AbstractForm::validate();

        if (empty($this->permissionString)) {
            throw new UserInputException('permissionString');
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        if (empty($_POST)) {
            $this->permissionString = $this->permissionEntry['permissionString'];
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $sql = "UPDATE  wcf" . WCF_N . "_packageserver_package_to_user
                SET     permissionString = ?
                WHERE   packageIdentifier = ?
                    AND userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->permissionString,
            $this->packageIdentifier,
            $this->user->userID,
        ]);

        PackageServerUtil::generateAuthFile();

        $this->saved();

        // show success
        WCF::getTPL()->assign([
            'success' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'action' => 'edit',
            'user' => $this->user,
        ]);
    }
}
