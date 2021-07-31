<?php

namespace wcf\acp\action;

use wcf\acp\page\PackageServerPackagePermissionOverviewPage;
use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Deletes package permissions.
 *
 * @author  Maximilian Mader
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerDeletePermissionAction extends AbstractAction
{
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
     * The type of the permission
     * @var string
     */
    public $type = '';

    /**
     * The id of the benefited user or user group
     * @var integer
     */
    public $beneficiaryID = 0;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_GET['packageIdentifier'])) {
            $this->packageIdentifier = StringUtil::trim($_GET['packageIdentifier']);
        }
        if (isset($_GET['type'])) {
            $this->type = StringUtil::trim($_GET['type']);
        }
        if (isset($_GET['beneficiaryID'])) {
            $this->beneficiaryID = \intval($_GET['beneficiaryID']);
        }

        switch ($this->type) {
            case 'general':
                $sql = "SELECT  COUNT(*)
                        FROM    wcf" . WCF_N . "_packageserver_package_permission_general
                        WHERE   packageIdentifier = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                ]);
                break;

            case 'user':
                $sql = "SELECT  COUNT(*)
                        FROM    wcf" . WCF_N . "_packageserver_package_to_user
                        WHERE   packageIdentifier = ?
                            AND userID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                    $this->beneficiaryID,
                ]);
                break;

            case 'group':
                $sql = "SELECT  COUNT(*)
                        FROM    wcf" . WCF_N . "_packageserver_package_to_group
                        WHERE   packageIdentifier = ?
                            AND groupID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                    $this->beneficiaryID,
                ]);
                break;

            default:
                throw new IllegalLinkException();
        }

        if (!$statement->fetchColumn()) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        switch ($this->type) {
            case 'general':
                $sql = "DELETE FROM wcf" . WCF_N . "_packageserver_package_permission_general
                        WHERE       packageIdentifier = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                ]);
                break;

            case 'user':
                $sql = "DELETE FROM wcf" . WCF_N . "_packageserver_package_to_user
                        WHERE       packageIdentifier = ?
                                AND userID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                    $this->beneficiaryID,
                ]);
                break;

            case 'group':
                $sql = "DELETE FROM wcf" . WCF_N . "_packageserver_package_to_group
                        WHERE       packageIdentifier = ?
                                AND groupID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->packageIdentifier,
                    $this->beneficiaryID,
                ]);
                break;

            default:
                throw new IllegalLinkException();
        }

        HeaderUtil::redirect(
            LinkHandler::getInstance()->getControllerLink(PackageServerPackagePermissionOverviewPage::class)
        );

        exit;
    }
}
