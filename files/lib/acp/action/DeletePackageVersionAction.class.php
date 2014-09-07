<?php
namespace wcf\acp\action;
use wcf\action\AbstractAction;
use wcf\util\HeaderUtil;

class DeletePackageVersionAction extends AbstractAction {
	/**
	 * @see	\wcf\action\AbstractAction::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canDeletePackage');

	/**
	 * @see	\wcf\action\IAction::execute()
	 */
	public function execute() {
		parent::execute();
		
		$package = (isset($_GET['package'])) ? $_GET['package'] : null;
		$version = (isset($_GET['version'])) ? $_GET['version'] : null;
		
		if ($package === null || $version === null || !is_file(\wcf\util\PackageServerUtil::getPackageServerPath(). $package .'/'. $version .'.tar')) {
			throw new \wcf\system\exception\IllegalLinkException();
		}
		
		if (@unlink(\wcf\util\PackageServerUtil::getPackageServerPath(). $package .'/'. $version .'.tar') === false) {
			throw new \wcf\system\exception\SystemException('could not delete package');
		}
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('PackageList'));
		exit;
	}
}