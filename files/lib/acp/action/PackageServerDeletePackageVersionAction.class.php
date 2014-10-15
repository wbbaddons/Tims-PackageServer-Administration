<?php
namespace wcf\acp\action;
use wcf\action\AbstractAction;
use wcf\util\HeaderUtil;
use wcf\data\package\Package;

/**
 * Deletes package versions.
 *
 * @author	Joshua Rüsweg
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerDeletePackageVersionAction extends AbstractAction {
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
	 * The package version string
	 * @var string
	 */
	public $version = '';
	
	/**
	 * @see	\wcf\page\IAction::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_GET['packageIdentifier'])) $this->packageIdentifier = $_GET['packageIdentifier'];
		if (isset($_GET['version'])) $this->version = $_GET['version'];
		
		$tmpVersion = str_replace('_', ' ', $this->version);
		
		if (!Package::isValidPackageName($this->packageIdentifier) || !Package::isValidVersion($tmpVersion) || !is_file(\wcf\util\PackageServerUtil::getPackageServerPath(). $this->packageIdentifier .'/'. $this->version .'.tar')) {
			throw new \wcf\system\exception\IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\action\IAction::execute()
	 */
	public function execute() {
		parent::execute();
		
		if (@unlink(\wcf\util\PackageServerUtil::getPackageServerPath(). $this->packageIdentifier .'/'. $this->version .'.tar') === false) {
			throw new \wcf\system\exception\SystemException('could not delete package');
		}
		
		HeaderUtil::redirect(\wcf\system\request\LinkHandler::getInstance()->getLink('PackageServerPackageList'));
		exit;
	}
}
