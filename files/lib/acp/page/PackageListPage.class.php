<?php
namespace wcf\acp\page;

use wcf\system\WCF;

/**
 * Represents a list of all permissions
 * 
 * @author	Joshua Rüsweg
 * @package	be.bastelstu.josh.ps
 * @subpackage	acp.page
 */
class PackageListPage extends \wcf\page\AbstractPage {

	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.packageList';
	
	/**
	 * all packages with versions
	 * @var array<mixed> 
	 */
	public $items = array(); 
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$handle = opendir(PackageServerUtil::getPackageServerPath()); 
		
		if (!$handle) {
			throw new \wcf\system\exception\SystemException('could not open package dir');
		}
		
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && is_dir($file)) {
				$this->items[$file] = array(); 
				
				$versionHandle = opendir(PackageServerUtil::getPackageServerPath().$file); 
				
				if (!$versionHandle) {
					throw new \wcf\system\exception\SystemException('could not open package dir ('.$file.')');
				}
				
				while(false !== ($version = readdir($versionHandle))) {
					if (!is_dir($version)) {
						$this->items[$file][] = mb_substr($version, 0, -4); // remove .tar :) 
					}
				}
			}
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'items' => $this->items
		));
	}
}