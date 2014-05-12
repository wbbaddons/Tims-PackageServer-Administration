<?php
namespace wcf\util;
use wcf\data\user\group\UserGroup;
use wcf\data\user\UserList;
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
	 * generates and save the auth.json-file
	 */
	public static function generateAuthFile() {
		self::writeAuthFile(self::buildAuth());
	}
	
	/**
	 * writes an auth file with the given content
	 * 
	 * @param array $content
	 */
	public static function writeAuthFile(array $content) {
		// generate temporary auth file
		$temporaryFile = FileUtil::getTemporaryFilename();
		file_put_contents($temporaryFile, JSON::encode($content));
		
		rename($temporaryFile, self::getPackageServerPath().self::AUTH_FILENAME); 
	}
	
	/**
	 * returns the packageServer-dir with trailing slash
	 * 
	 * @return string
	 */
	public static function getPackageServerPath() {
		return FileUtil::addTrailingSlash(PACKAGESERVER_DIR);
	}
	
	/**
	 * get the current auth file as array
	 * if there isn't a auth file, the method returns a empty auth-array
	 * 
	 * @return array<mixed>
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
	 * build a auth-file-array
	 * 
	 * @return array<mixed>
	 */
	public static function buildAuth() {
		return array(
			self::USERS_DIR => self::buildUsersAuth(), 
			self::GROUPS_DIR => array(), 
			self::PACKAGES_DIR => array()
		); 
	}
	
	/**
	 * build a array for the auth.json with all users
	 */
	public static function buildUsersAuth() {
		$list = new UserList(); 
		$list->readObjects(); 
		
		$users = array(); 
		
		foreach ($list->getObjects() as $user) {
			$users[$user->username] = self::buildUserAuth($user); 
		}  
		
		return $users; 
	}
	
	/**
	 * returns the user auth array for the auth.json
	 * 
	 * @param \wcf\util\wcf\data\user\User $user
	 * @return array<mixed>
	 */
	public static function buildUserAuth(\wcf\data\user\User $user) {
		return array(
			'passwd' => $user->password, 
			'groups' => array_map(function ($group) {
				return self::GROUPID_PREFIX.intval($group);
			}, $user->getGroupIDs(true)), 
			'packages' => array(), // @TODO 
		); 
	}
	
	/**
	 * updates the user in the auth.json
	 * 
	 * @param \wcf\data\user\User $user
	 */
	public static function updateUserAuth(\wcf\data\user\User $user) {
		$auth = self::getAuthFileArray(); 
		
		$auth[self::USERS_DIR][$user->username] = self::buildUserAuth($user); // update user auth
		
		self::writeAuthFile($auth); 
	}
	
	/**
	 * returns a identifer for usergroups
	 * 
	 * @param \wcf\data\user\group\UserGroup $group
	 * @return string
	 */
	public static function getGroupIdentifer(UserGroup $group) {
		return self::GROUPID_PREFIX.$group->getObjectID(); 
	}
	
	/**
	 * transform the package version into a filename
	 * 
	 * @param string $version
	 * @return string
	 */
	public static function transformPackageVersion($version) {
		return mb_strtolower(str_replace(' ', '_', $version));
	}
	
	private function __construct() { }
}
