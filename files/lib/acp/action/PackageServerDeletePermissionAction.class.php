<?php
namespace wcf\acp\action;

use wcf\action\AbstractAction;
use wcf\util\HeaderUtil;
use wcf\system\WCF;

/**
 * Deletes package permissions.
 *
 * @author	Maximilian Mader
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerDeletePermissionAction extends AbstractAction {
	/**
	 * @see	\wcf\action\AbstractAction::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	/**
	 * Package identifier
	 * @var	string
	 */
	public $packageIdentifier = '';
	
	/**
	 * The type of the permission
	 * @var	string
	 */
	public $type = '';
	
	/**
	 * The id of the benefited user or user group
	 * @var	integer
	 */
	public $beneficiaryID = 0;
	
	/**
	 * @see	\wcf\page\IAction::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_GET['packageIdentifier'])) $this->packageIdentifier = \wcf\util\StringUtil::trim($_GET['packageIdentifier']);
		if (isset($_GET['type'])) $this->type = \wcf\util\StringUtil::trim($_GET['type']);
		if (isset($_GET['beneficiaryID'])) $this->beneficiaryID = intval($_GET['beneficiaryID']);
		
		switch ($this->type) {
			case 'general':
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_permission_general
					WHERE	packageIdentifier = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier
				));
			break;
			
			case 'user':
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_to_user
					WHERE		packageIdentifier = ?
						AND	userID = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier,
					$this->beneficiaryID
				));
			break;
			
			case 'group':
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_to_group
					WHERE		packageIdentifier = ?
						AND	groupID = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier,
					$this->beneficiaryID
				));
			break;
			
			default:
				throw new \wcf\system\exception\IllegalLinkException();
		}
		
		if (!$stmt->fetchColumn()) {
			throw new \wcf\system\exception\IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\action\IAction::execute()
	 */
	public function execute() {
		parent::execute();
		
		switch ($this->type) {
			case 'general':
				$sql = "DELETE FROM	wcf".WCF_N."_packageserver_package_permission_general
					WHERE		packageIdentifier = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier
				));
			break;
			
			case 'user':
				$sql = "DELETE FROM	wcf".WCF_N."_packageserver_package_to_user
					WHERE		packageIdentifier = ?
						AND	userID = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier,
					$this->beneficiaryID
				));
			break;
			
			case 'group':
				$sql = "DELETE FROM	wcf".WCF_N."_packageserver_package_to_group
					WHERE		packageIdentifier = ?
						AND	groupID = ?";
				$stmt = WCF::getDB()->prepareStatement($sql);
				$stmt->execute(array(
					$this->packageIdentifier,
					$this->beneficiaryID
				));
			break;
			
			default:
				throw new \wcf\system\exception\IllegalLinkException();
		}
		
		PackageServerUtil::generateAuthFile();
		
		HeaderUtil::redirect(\wcf\system\request\LinkHandler::getInstance()->getLink('PackageServerPackagePermissionOverview'));
		exit;
	}
}
