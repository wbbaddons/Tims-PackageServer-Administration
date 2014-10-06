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
	 * @see	\wcf\page\IAction::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_GET['packageIdentifier'])) $this->packageIdentifier = \wcf\util\StringUtil::trim($_GET['packageIdentifier']);
		if (isset($_GET['type'])) $this->type = \wcf\util\StringUtil::trim($_GET['type']);
		if (isset($_GET['beneficiaryID'])) $this->beneficiaryID = intval($_GET['beneficiaryID']);
		
		if ($this->packageIdentifier === null || $this->beneficiaryID === null) {
			throw new \wcf\system\exception\IllegalLinkException();
		}
		
		switch ($this->type) {
			case 'general':
				$sqlData = array(
					$this->packageIdentifier
				);
				
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_permission_general
					WHERE	packageIdentifier = ?";
			break;
			
			case 'user':
				$sqlData = array(
					$this->packageIdentifier,
					$this->beneficiaryID
				);
				
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_to_user
					WHERE	packageIdentifier = ?
					AND	userID = ?";
			break;
			
			case 'group':
				$sqlData = array(
					$this->packageIdentifier,
					$this->beneficiaryID
				);
				
				$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_packageserver_package_to_group
					WHERE	packageIdentifier = ?
					AND	groupID = ?";
			break;
			
			default:
				throw new \wcf\system\exception\IllegalLinkException();
		}
		
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute($sqlData);
		
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
				$sqlData = array(
					$this->packageIdentifier
				);
				
				$sql = "DELETE FROM wcf".WCF_N."_packageserver_package_permission_general
					WHERE	packageIdentifier = ?";
			break;
			
			case 'user':
				$sqlData = array(
					$this->packageIdentifier,
					$this->beneficiaryID
				);
				
				$sql = "DELETE FROM wcf".WCF_N."_packageserver_package_to_user
					WHERE	packageIdentifier = ?
					AND	userID = ?";
			break;
			
			case 'group':
				$sqlData = array(
					$this->packageIdentifier,
					$this->beneficiaryID
				);
				
				$sql = "DELETE FROM wcf".WCF_N."_packageserver_package_to_group
					WHERE	packageIdentifier = ?
					AND	groupID = ?";
			break;
			
			default:
				throw new \wcf\system\exception\IllegalLinkException();
		}
		
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute($sqlData);
		
		HeaderUtil::redirect(\wcf\system\request\LinkHandler::getInstance()->getLink('PackageServerPackagePermissionOverview'));
		exit;
	}
}