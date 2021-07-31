<?php

namespace wcf\acp\page;

use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Represents a list of all permissions
 *
 * @author  Joshua Rüsweg
 * @package be.bastelstu.josh.ps
 * @subpackage  acp.page
 */
final class PackageServerPackageListPage extends \wcf\page\AbstractPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.list';

    /**
     * Two-dimensional array containing all packages and their versions
     * @var array<mixed>
     */
    public $items = [];

    /**
     * Number of found packages
     * @var integer
     */
    public $packageCount = 0;

    /**
     * Number of found versions
     * @var integer
     */
    public $versionCount = 0;

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
            throw new \wcf\system\exception\NamedUserException(
                WCF::getLanguage()->getDynamicVariable('wcf.acp.packageserver.error.invalidPath')
            );
        }

        $handle = \wcf\util\DirectoryUtil::getInstance(PackageServerUtil::getPackageServerPath());

        $packageIdentifierRegex = '([a-z0-9_-]+\.[a-z0-9_-]+(?:\.[a-z0-9_-]+)+)';
        $packageVersionRegex = '([0-9]+\.[0-9]+\.[0-9]+(?:_(?:a|alpha|b|beta|d|dev|rc|pl)_[0-9]+)?)';
        $packageRegex = '^' . PackageServerUtil::getPackageServerPath() . $packageIdentifierRegex . '/' . $packageVersionRegex . '\.tar$';

        $files = $handle->getFileObjects(
            \SORT_ASC,
            new Regex($packageRegex, Regex::CASE_INSENSITIVE)
        );

        foreach ($files as $file) {
            $package = $file->getPathInfo()->getBasename();
            $downloads = 0;

            if (!isset($this->items[$package])) {
                $this->items[$package] = [];
                $this->packageCount++;
            }

            $version = $file->getBasename('.tar');
            $counterFile = $file->getPath() . '/' . $version . '.txt';

            if (\is_file($counterFile)) {
                $downloads = \intval(\file_get_contents($counterFile));
            }

            $this->items[$package][\str_replace('_', ' ', $version)] = $downloads;

            $this->versionCount++;
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'items' => $this->items,
            'packageCount' => $this->packageCount,
            'versionCount' => $this->versionCount,
        ]);
    }
}
