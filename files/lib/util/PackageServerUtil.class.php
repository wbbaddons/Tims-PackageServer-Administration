<?php
namespace wcf\util;
use wcf\data\user\group\UserGroup;
use wcf\data\user\UserList;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\JSON;

/**
 * Contains functions, which are related for "Tims-PackageServer".
 *
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
final class PackageServerUtil {
	const AUTH_FILENAME = 'auth.json';
	const GROUPID_PREFIX = 'group-';
	const USERS_DIR = 'users';
	const GROUPS_DIR = 'groups';
	const PACKAGES_DIR = 'packages';
	
	/**
	 * Generates and save the auth.json-file.
	 */
	public static function generateAuthFile() {
		self::writeAuthFile(self::buildAuth());
	}
	
	/**
	 * Performs an atomic write to the authentication file.
	 *
	 * @param	array	$content
	 */
	public static function writeAuthFile(array $content) {
		if (!is_dir(self::getPackageServerPath())) return false;
		
		// generate temporary auth file
		$temporaryFile = FileUtil::getTemporaryFilename();
		file_put_contents($temporaryFile, json_encode($content, JSON_PRETTY_PRINT));
		
		rename($temporaryFile, self::getPackageServerPath().self::AUTH_FILENAME);
	}
	
	/**
	 * Returns the path to the packages folder of the package server.
	 *
	 * @return string
	 */
	public static function getPackageServerPath() {
		return FileUtil::addTrailingSlash(PACKAGESERVER_DIR);
	}
	
	/**
	 * Returns the current authentication information. If
	 * no authentication file exists yet, this methods returns empty records.
	 *
	 * @return array
	 */
	public static function getAuthFileArray() {
		if (file_exists(self::getPackageServerPath().self::AUTH_FILENAME)) {
			return JSON::decode(file_get_contents(self::getPackageServerPath().self::AUTH_FILENAME), true);
		}
		
		return array(
			self::USERS_DIR => array(),
			self::GROUPS_DIR => array(),
			self::PACKAGES_DIR => array()
		);
	}
	
	/**
	 * Returns all the authentication information.
	 *
	 * @return array
	 */
	public static function buildAuth() {
		return array(
			self::USERS_DIR => self::buildUsersAuth(),
			self::GROUPS_DIR => self::buildGroupsAuth(),
			self::PACKAGES_DIR => self::buildPackagesAuth()
		);
	}
	
	/**
	 * Returns all the user permission records.
	 *
	 * @return	array
	 */
	public static function buildUsersAuth() {
		$list = new UserList();
		$list->readObjects();
		
		$users = array();
		foreach ($list as $user) {
			$users[$user->username] = self::buildUserAuth($user);
		}
		
		return $users;
	}
	
	/**
	 * Returns all the group permission records.
	 *
	 * @return	array
	 */
	public static function buildGroupsAuth() {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_packageserver_package_to_group";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute();
		
		$groups = array();
		
		while ($row = $stmt->fetchArray()) {
			$groups[self::GROUPID_PREFIX.$row['groupID']][$row['packageIdentifier']] = $row['permissions'];
		}
		
		return $groups;
	}
	
	/**
	 * Returns the general permission record.
	 *
	 * @return	array<string>
	 */
	public static function buildPackagesAuth() {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_packageserver_package_permission_general";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute();
		
		$general = array();
		
		while ($row = $stmt->fetchArray()) {
			$general[$row['packageIdentifier']] = $row['permissions'];
		}
		
		return $general;
	}
	
	/**
	 * Returns the user record for the given user.
	 *
	 * @param	\wcf\data\user\User	$user
	 * @return	array<mixed>
	 */
	public static function buildUserAuth(\wcf\data\user\User $user) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_packageserver_package_to_user
			WHERE	userID = ?";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($user->getObjectID()));
		
		$permssions = array();
		while ($row = $stmt->fetchArray()) {
			$permssions[$row['packageIdentifier']] = $row['permissions'];
		}
		
		return array(
			'passwd' => ($user->isBanned) ? '-' : $user->password,
			'groups' => array_map(function ($group) {
				return self::GROUPID_PREFIX.intval($group);
			}, $user->getGroupIDs(true)),
			'packages' => $permssions
		);
	}
	
	/**
	 * Updates a user record in auth.json
	 *
	 * @param	\wcf\data\user\User $user
	 */
	public static function updateUserAuth(\wcf\data\user\User $user) {
		$auth = self::getAuthFileArray();
		
		$auth[self::USERS_DIR][$user->username] = self::buildUserAuth($user); // update user auth
		
		self::writeAuthFile($auth);
	}
	
	/**
	 * Returns a the internal name for the given user group.
	 *
	 * @param	\wcf\data\user\group\UserGroup $group
	 * @return	string
	 */
	public static function getGroupIdentifer(UserGroup $group) {
		return self::GROUPID_PREFIX.$group->getObjectID();
	}
	
	/**
	 * Transform the package version into a package filename.
	 *
	 * @param	string	$version
	 * @return	string
	 */
	public static function transformPackageVersion($version) {
		return mb_strtolower(str_replace(' ', '_', $version));
	}
	
	private function __construct() { }
}
