<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * A form for add package permissions
 *
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageGeneralPermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addGeneralPermission';
	
	public $neededPermissions = array('admin.packageServer.canAddPermissions');
	
	public $packageIdentifer = '';
	
	public $permission = '';
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (isset($_GET['package'])) {
			$this->packageIdentifer = $_GET['package'];
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['package'])) {
			$this->packageIdentifer = $_POST['package'];
		}
		
		if (isset($_POST['permission'])) {
			$this->permission = $_POST['permission'];
		}
	}
	
	public function validate() {
		parent::validate();
		
		if (empty($this->packageIdentifer)) {
			throw new \wcf\system\exception\UserInputException('package');
		}
		
		if (empty($this->permission)) {
			throw new \wcf\system\exception\UserInputException('permission');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_permission_general
				(packageIdentifier, permissions)
			VALUES
				(?, ?)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->packageIdentifer, $this->permission));
		
		// regenerate auth file @TODO, better solution work in progress
		PackageServerUtil::generateAuthFile();
		
		$this->saved();
	}
	
	public function saved() {
		parent::saved();
		
		$this->packageIdentifer = $this->permission = "";
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'permission' => $this->permission,
			'package' => $this->packageIdentifer
		));
	}
}
