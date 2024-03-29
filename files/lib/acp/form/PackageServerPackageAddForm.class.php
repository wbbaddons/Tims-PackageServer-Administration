<?php

namespace wcf\acp\form;

use wcf\data\package\Package;
use wcf\form\AbstractForm;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\UserInputException;
use wcf\system\package\PackageArchive;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\PackageServerUtil;

/**
 * A form for uploading packages
 *
 * @author  Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerPackageAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.packageServer.canManagePackages'];

    /**
     * The temporary package-file
     *
     * @var String
     */
    public $tempFile;

    /**
     * The uploaded file
     *
     * @var array<mixed>
     */
    public $upload;

    /**
     * @var PackageArchive
     */
    public $archive;

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        try {
            // is_dir may throw an exception because of open_basedir restrictions,
            // therefore we throw a simple exception here and catch it afterwards to throw the correct exception
            if (!\is_dir(PackageServerUtil::getPackageServerPath())) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new NamedUserException(
                WCF::getLanguage()->getDynamicVariable('wcf.acp.packageserver.error.invalidPath')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_FILES['package'])) {
            $this->upload = $_FILES['package'];
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        if (empty($this->upload['name'])) {
            throw new UserInputException('package', 'empty');
        }

        if (empty($this->upload['tmp_name']) || !\is_file($this->upload['tmp_name'])) {
            throw new UserInputException('package', 'upload');
        }

        $mimeType = FileUtil::getMimeType($this->upload['tmp_name']);

        if ($mimeType === '') {
            // the returned MIME type may be empty if “finfo” is unavailable
            // therefore we will look at the file extension … still better than nothing!

            $extension = \pathinfo($this->upload['name'], \PATHINFO_EXTENSION);

            if (!$extension || \mb_strtolower($extension) !== 'tar') {
                throw new UserInputException('package', 'tar');
            }
        } elseif ($mimeType === 'application/x-gzip' || $mimeType === 'application/gzip') {
            throw new UserInputException('package', 'gzip');
        } elseif ($mimeType !== 'application/x-tar' && $mimeType !== 'application/tar') {
            throw new UserInputException('package', 'tar');
        }

        // get filename
        $this->tempFile = FileUtil::getTemporaryFilename(
            'package_',
            \preg_replace('!^.*(?=\.(?:tar\.gz|tgz|tar)$)!i', '', \basename($this->upload['name']))
        );

        if (!@\move_uploaded_file($this->upload['tmp_name'], $this->tempFile)) {
            throw new UserInputException('package', 'upload');
        }

        $this->archive = new PackageArchive($this->tempFile, null);

        try {
            $this->archive->openArchive();
        } catch (\Exception $e) {
            throw new UserInputException('package', 'validation');
        }

        if (!Package::isValidVersion($this->archive->getPackageInfo('version'))) {
            throw new UserInputException('package', 'validation');
        }

        if (\is_file($this->getPackagePath())) {
            throw new UserInputException('package', 'duplicate');
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        if (!\file_exists(PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name'))) {
            if (FileUtil::makePath(PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name')) === false) {
                throw new \RuntimeException('cannot create package-dir');
            }
        }

        if (\rename($this->tempFile, $this->getPackagePath()) === false) {
            throw new \RuntimeException('cannot move package');
        }

        $this->saved();

        // clean up
        $this->archive->deleteArchive();

        // show success
        WCF::getTPL()->assign('success', true);
    }

    /**
     * Returns the full path of the package.
     *
     * @return  string|null null is returned if an error arises
     */
    public function getPackagePath()
    {
        if ($this->archive !== null) {
            return $this->getPackageDirectory() . PackageServerUtil::transformPackageVersion($this->archive->getPackageInfo('version')) . '.tar';
        }

        return null;
    }

    /**
     * Returns the directory in which the package will be saved.
     *
     * @param   boolean     $addTrailingSlash
     * @return  string|null             null is returned if an error arises
     */
    public function getPackageDirectory($addTrailingSlash = true)
    {
        if ($this->archive !== null) {
            $path = PackageServerUtil::getPackageServerPath() . $this->archive->getPackageInfo('name');

            if ($addTrailingSlash) {
                $path = FileUtil::addTrailingSlash($path);
            }

            return $path;
        }

        return null;
    }
}
