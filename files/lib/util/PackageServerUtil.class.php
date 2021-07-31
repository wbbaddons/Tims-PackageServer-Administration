<?php

namespace wcf\util;

use wcf\system\io\File;
use wcf\system\WCF;

/**
 * Contains functions, which are related for "Tims-PackageServer".
 *
 * @author  Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerUtil
{
    private const AUTH_FILENAME = 'auth.json';

    /**
     * Generates and saves the auth.json-file.
     */
    public static function generateAuthFile()
    {
        // is_dir may throw an exception because of open_basedir restrictions
        try {
            if (!\is_dir(self::getPackageServerPath())) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        // generate a temporary auth file
        $temporaryFile = FileUtil::getTemporaryFilename();

        $file = new File($temporaryFile);
        $file->write("{\n");

        $file->write("\t\"users\": {\n");
        $sql = "SELECT  userID,
                        packageIdentifier,
                        permissionString
                FROM    wcf" . WCF_N . "_packageserver_package_to_user";
        $userPackagesStatement = WCF::getDB()->prepareStatement($sql);
        $userPackagesStatement->execute();
        $userPackages = [];
        while ($row = $userPackagesStatement->fetchArray()) {
            if (!isset($userPackages[$row['userID']])) {
                $userPackages[$row['userID']] = [];
            }
            $userPackages[$row['userID']][$row['packageIdentifier']] = $row['permissionString'];
        }
        $sql = "SELECT      user.userID,
                            MIN(user.username) AS username,
                            MIN(user.password) AS password,
                            MIN(user.banned) AS banned,
                            GROUP_CONCAT(user_to_group.groupID SEPARATOR ',') AS groupIDs
                FROM        wcf" . WCF_N . "_user user
                LEFT JOIN   wcf" . WCF_N . "_user_to_group user_to_group
                ON          user.userID = user_to_group.userID
                GROUP BY    user.userID";
        $userStatement = WCF::getDB()->prepareStatement($sql);
        $userStatement->execute();
        while ($row = $userStatement->fetchArray()) {
            $groups = \array_map(static function ($item) {
                return 'group-' . $item;
            }, \explode(',', $row['groupIDs']));

            $userData = [
                'passwd' => $row['banned'] ? '-' : $row['password'],
                'groups' => $groups,
                'packages' => $userPackages[$row['userID']] ?? new \stdClass(),
            ];
            $file->write("\t\t" . \json_encode($row['username']) . ": " . \json_encode($userData) . ",\n");
        }
        unset($userPackages, $userPackagesStatement, $userStatement, $userData);
        $file->seek($file->tell() - 2);
        $file->write("\n");
        $file->write("\t},\n");

        $file->write("\t\"groups\": {\n");
        $sql = "SELECT  groupID,
                        packageIdentifier,
                        permissionString
                FROM    wcf" . WCF_N . "_packageserver_package_to_group";
        $groupPackagesStatement = WCF::getDB()->prepareStatement($sql);
        $groupPackagesStatement->execute();
        $groupPackages = [];
        while ($row = $groupPackagesStatement->fetchArray()) {
            if (!isset($groupPackages[$row['groupID']])) {
                $groupPackages[$row['groupID']] = [];
            }
            $groupPackages[$row['groupID']][$row['packageIdentifier']] = $row['permissionString'];
        }
        if (!empty($groupPackages)) {
            foreach ($groupPackages as $groupID => $packages) {
                $file->write("\t\t" . \json_encode('group-' . $groupID) . ": " . \json_encode($packages) . ",\n");
            }
            $file->seek($file->tell() - 2);
        }
        unset($groupPackages, $groupPackagesStatement, $packages);
        $file->write("\n");
        $file->write("\t},\n");

        $sql = "SELECT  packageIdentifier,
                        permissionString
                FROM    wcf" . WCF_N . "_packageserver_package_permission_general";
        $packagesStatement = WCF::getDB()->prepareStatement($sql);
        $packagesStatement->execute();
        $packages = [];
        while ($row = $packagesStatement->fetchArray()) {
            $packages[$row['packageIdentifier']] = $row['permissionString'];
        }
        $file->write("\t\"packages\": " . \json_encode($packages) . "\n");
        $file->write("}");
        $file->flush();
        $file->close();

        return \rename($temporaryFile, self::getPackageServerPath() . self::AUTH_FILENAME);
    }

    /**
     * Returns the path to the packages folder of the package server.
     *
     * @return  string
     */
    public static function getPackageServerPath()
    {
        if (!PACKAGESERVER_DIR) {
            return false;
        }

        return FileUtil::addTrailingSlash(PACKAGESERVER_DIR);
    }

    /**
     * Transform the package version into a package filename.
     *
     * @param   string  $version
     * @return  string
     */
    public static function transformPackageVersion($version)
    {
        return \mb_strtolower(\str_replace(' ', '_', $version));
    }

    private function __construct()
    {
    }
}
