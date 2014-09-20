<?php
namespace wcf\acp\form;
use wcf\data\package\Package;
use wcf\form\AbstractForm;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\package\PackageArchive;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\PackageServerUtil;

/**
 * A form for uploading packages
 *
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageServerPackageAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.add';
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	/**
	 * the temporary package-file
	 *
	 * @var String
	 */
	public $package = null;
	
	/**
	 * the uploaded file
	 *
	 * @var array<mixed>
	 */
	public $upload = null;
	
	/**
	 * @var \wcf\system\package\PackageArchive
	 */
	public $archive = null;
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		if (!is_dir(PackageServerUtil::getPackageServerPath())) {
			throw new \wcf\system\exception\NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.acp.packageserver.error.invalidPath'));
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_FILES['package'])) {
			$this->upload = $_FILES['package'];
		}
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->upload['name'])) {
			throw new UserInputException('package', 'empty');
		}

		if (empty($this->upload['tmp_name'])) {
			throw new UserInputException('package', 'upload');
		}
		
		// @TODO validate whether the file is a .tar-file
		// compressed files are not allowed
		$extensionPaths = explode('.', $this->upload['name']);
		$extension = array_pop($extensionPaths); // because php is to stupid :(
		
		if ($extension != 'tar') {
			throw new UserInputException('package', 'tar');
		}

		// get filename
		$this->package = FileUtil::getTemporaryFilename('package_', preg_replace('!^.*(?=\.(?:tar\.gz|tgz|tar)$)!i', '', basename($this->upload['name'])));

		if (!@move_uploaded_file($this->upload['tmp_name'], $this->package)) {
			throw new UserInputException('package', 'upload');
		}

		$this->archive = new PackageArchive($this->package, null);

		try {
			$this->archive->openArchive();
		}
		catch (SystemException $e) {
			throw new UserInputException('package', 'validation');
		}

		if (!Package::isValidVersion($this->archive->getPackageInfo('version'))) {
			throw new UserInputException('package', 'validation');
		}
		
		if (is_file($this->buildPackageLink())) {
			throw new UserInputException('package', 'double');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		if (!file_exists(PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name'))) {
			if (FileUtil::makePath(PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name')) === false) {
				throw new SystemException('cannot create package-dir');
			}
		}
		
		if (rename($this->package, $this->buildPackageLink()) === false) {
			throw new SystemException('cannot move package');
		}
		
		$this->saved();
		
		// clean up
		$this->archive->deleteArchive();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * build the link for the package, if $this->archive
	 * is null then the method returns null
	 *
	 * @return String
	 */
	public function buildPackageLink() {
		if ($this->archive !== null) {
			return $this->buildPackageDirLink() . PackageServerUtil::transformPackageVersion($this->archive->getPackageInfo('version')) . '.tar';
		}
		
		return null;
	}
	
	/**
	 * build the dir-link for the package, if $this->archive
	 * is null then the method returns null
	 *
	 * @param boolean $trailingSlash
	 * @return String
	 */
	public function buildPackageDirLink($trailingSlash = true) {
		if ($this->archive !== null) {
			return PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name') . (($trailingSlash) ? '/' : '');
		}
		
		return null;
	}
}
