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
class PackagePermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.addgeneralpermission';
	
	public $neededPermissions = array('admin.packageServer.canAddPermissions');
	
	public $packageIdentifer = ''; 
	
	public $permission = '';
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (isset($_GET['packageIdentifer'])) {
			$this->packageIdentifer = $_GET['packageIdentifer']; 
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['packageIdentifer'])) {
			$this->packageIdentifer = $_POST['packageIdentifer']; 
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
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
}
