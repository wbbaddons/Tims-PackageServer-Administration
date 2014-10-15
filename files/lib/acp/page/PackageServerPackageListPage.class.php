<?php
namespace wcf\acp\page;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Represents a list of all permissions
 *
 * @author	Joshua Rüsweg
 * @package	be.bastelstu.josh.ps
 * @subpackage	acp.page
 */
class PackageServerPackageListPage extends \wcf\page\AbstractPage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.list';
	
	/**
	 * all packages with versions
	 * @var array<mixed>
	 */
	public $items = array();
	
	public $packageCount = 0;
	public $versionCount = 0;
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		try {
			// is_dir may throw an exception because of open_basedir restrictions,
			// therefore we throw a simple exception here and catch it afterwards to throw the correct exception
			if (!is_dir(PackageServerUtil::getPackageServerPath())) throw new \Exception();
		}
		catch (\Exception $e) {
			throw new \wcf\system\exception\NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.acp.packageserver.error.invalidPath'));
		}
		
		$handle = \wcf\util\DirectoryUtil::getInstance(PackageServerUtil::getPackageServerPath());
		$files = $handle->getFileObjects(SORT_ASC, new \wcf\system\Regex('[a-zA-Z]+\.[a-zA-Z]+\.[a-zA-Z]+/.*\.tar'));
		
		foreach ($files as $file) {
			$package = $file->getPathInfo()->getBasename();
			
			if (!isset($this->items[$package])) {
				$this->items[$package] = array();
				$this->packageCount++;
			}
			
			$this->items[$package][] = substr($file->getBasename(), 0, -4);
			$this->versionCount++;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'items' => $this->items,
			'packageCount' => $this->packageCount,
			'versionCount' => $this->versionCount
		));
	}
}
