<?php

namespace wcf\acp\form;

use wcf\data\package\Package;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;
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
class PackageServerPackageGroupPermissionAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addGroupPermission';

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
     * List of group IDs
     * @var array
     */
    public $groupIDs = [];

    /**
     * Instance of UserGroupList
     * @var UserGroupList
     */
    public $groupList;

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
        if (isset($_POST['groupIDs'])) {
            $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        $this->groupList = new UserGroupList();
        $this->groupList->getConditionBuilder()->add('groupType NOT IN (?)', [UserGroup::EVERYONE]);
        $this->groupList->readObjects();

        parent::readData();
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

        if (empty($this->groupIDs)) {
            throw new UserInputException('groupIDs');
        }

        foreach ($this->groupIDs as $groupID) {
            if ($this->groupList->search($groupID) === null) {
                throw new UserInputException('groupIDs', 'notValid');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $sql = "INSERT INTO wcf" . WCF_N . "_packageserver_package_to_group
                    (packageIdentifier, permissionString, groupID)
                VALUES
                    (?, ?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->groupIDs as $groupID) {
            $statement->execute([
                $this->packageIdentifier,
                $this->permissionString,
                $groupID,
            ]);
        }

        // regenerate auth file @TODO, better solution work in progress
        PackageServerUtil::generateAuthFile();

        $this->saved();

        $this->packageIdentifier = $this->permissionString = "";
        $this->groupIDs = [];

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
            'groupIDs' => $this->groupIDs,
            'availableGroups' => $this->groupList,
            'action' => 'add',
        ]);
    }
}
