<?php

namespace be\bastelstu\josh\ps;

use wcf\data\option\OptionEditor;
use wcf\system\WCF;

/**
 * Sets the PACKAGESERVER_DIR option to this installation’s package server path
 *
 * @author  Maximilian Mader
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
// @codingStandardsIgnoreFile
final class Installation
{
    private $optionID;

    public function __construct($packageID)
    {
        $sql = "SELECT  optionID
                FROM    wcf" . WCF_N . "_option
                WHERE   packageID = ?
                    AND optionName = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$packageID, 'packageserver_dir']);
        $this->optionID = $statement->fetchColumn();
    }
    
    public function execute()
    {
        $sql = "UPDATE  wcf" . WCF_N . "_option
                SET     optionValue = ?
                WHERE   optionID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF_DIR . 'acp/be.bastelstu.josh.ps/Tims-PackageServer/packages/', $this->optionID]);
        OptionEditor::resetCache();
    }
}

$installation = new Installation($this->installation->getPackageID());
$installation->execute();
