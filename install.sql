CREATE TABLE wcf1_packageserver_package_to_group (
	packageIdentifier VARCHAR(255),
	groupID INT(10),
	permissionString MEDIUMTEXT,
	PRIMARY KEY(packageIdentifier, groupID),
	KEY (groupID)
);

CREATE TABLE wcf1_packageserver_package_to_user (
	packageIdentifier VARCHAR(255),
	userID INT(10),
	permissionString MEDIUMTEXT,
	PRIMARY KEY(packageIdentifier, userID),
	KEY (userID)
);

CREATE TABLE wcf1_packageserver_package_permission_general (
	packageIdentifier VARCHAR(255) PRIMARY KEY,
	permissionString MEDIUMTEXT
);

-- foreign keys
ALTER TABLE wcf1_packageserver_package_to_group ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;
ALTER TABLE wcf1_packageserver_package_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
