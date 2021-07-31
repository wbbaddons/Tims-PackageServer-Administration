<?php

namespace wcf\acp\form;

use wcf\data\package\Package;
use wcf\data\user\UserList;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\PackageServerUtil;
use wcf\util\StringUtil;

/**
 * A form for add package permissions
 *
 * @author  Tim Düsterhus, Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerPackageUserPermissionAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addUserPermission';

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
     * List of usernames
     * @var array
     */
    public $usernames = [];

    /**
     * Instance of UserList
     * @var UserList
     */
    public $userList;

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_POST['packageIdentifier'])) {
            $this->packageIdentifier = StringUtil::trim($_POST['packageIdentifier']);
        }
        if (isset($_POST['permissionString'])) {
            $this->permissionString = StringUtil::trim($_POST['permissionString']);
        }
        if (isset($_POST['usernames'])) {
            $this->usernames = \array_filter(ArrayUtil::trim(\explode(',', $_POST['usernames'])));
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

        $this->userList = new UserList();
        $this->userList->getConditionBuilder()->add('username IN (?)', [$this->usernames]);
        $this->userList->readObjects();

        $tmp = [];
        foreach ($this->userList as $user) {
            $tmp[] = \mb_strtolower($user->username);
        }
        $difference = \array_diff(\array_map('mb_strtolower', $this->usernames), $tmp);

        if (!empty($difference)) {
            WCF::getTPL()->assign([
                'unknownUsers' => $difference,
            ]);

            throw new UserInputException('usernames', 'notFound');
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $sql = "INSERT INTO wcf" . WCF_N . "_packageserver_package_to_user
                    (packageIdentifier, permissionString, userID)
                VALUES
                    (?, ?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->userList as $user) {
            $statement->execute([
                $this->packageIdentifier,
                $this->permissionString,
                $user->userID,
            ]);
        }

        // regenerate auth file @TODO, better solution work in progress
        PackageServerUtil::generateAuthFile();

        $this->saved();

        $this->packageIdentifier = $this->permissionString = "";
        $this->usernames = [];

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
            'permissionString' => $this->permissionString,
            'packageIdentifier' => $this->packageIdentifier,
            'usernames' => $this->usernames,
            'action' => 'add',
        ]);
    }
}
