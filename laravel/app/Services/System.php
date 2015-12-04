<?php namespace App\Services;

use App\Exceptions\SystemException;
use App\Option;
use App\Role;
use Illuminate\Database\QueryException;

class System
{
    public static function isInstalled()
    {
        try {
            return Option::getValueByKey('installed') != NULL;
        } catch (QueryException $ex) {
            return false;
        }
    }

    public static function getSiteName()
    {
        return array_first([
            Option::getValueByKey('site_name'),
            env('PROJECT_NAME'),
            '#SITE_NAME#',
        ], function ($k, $v) {
            return $v;
        });
    }

    public static function setSiteName($siteName)
    {
        Option::setValueByKey('site_name', $siteName);
    }

    public static function getVersion()
    {
        return array_first([
            Option::getValueByKey('version'),
            env('PROJECT_VERSION'),
            '#VERSION#',
        ], function ($k, $v) {
            return $v;
        });
    }

    public static function setVersion($version)
    {
        Option::setValueByKey('version', $version);
    }

    public static function getCopyrightYear()
    {
        $startYear = env('PROJECT_START_YEAR', 2015);
        if ($startYear == date('Y')) {
            return $startYear;
        } else {
            return $startYear . '-' . date('Y');
        }
    }

    public static function getPoweredName()
    {
        return array_first([
            Option::getValueByKey('powered_name'),
            env('PROJECT_TEAM'),
            '#POWERED_NAME#',
        ], function ($k, $v) {
            return $v;
        });
    }

    public static function setPoweredName($poweredName)
    {
        Option::setValueByKey('powered_name', $poweredName);
    }

    public static function getMasterRole()
    {
        $masterRoleId = Option::getValueByKey('master_role_id');
        /** @var Role $masterRole */
        $masterRole = Role::whereId($masterRoleId)->first();
        if (!$masterRole)
            throw new SystemException(SystemException::MasterRoleNotDefined);

        return $masterRole;
    }

    public static function setMasterRoleId($masterRoleId)
    {
        Option::setValueByKey('master_role_id', $masterRoleId);
    }

    public static function getAdministratorRole()
    {
        $administratorRoleId = Option::getValueByKey('administrator_role_id');
        $administratorRole   = Role::whereId($administratorRoleId)->first();
        if (!$administratorRole)
            throw new SystemException(SystemException::AdministratorRoleNotDefined);

        return $administratorRole;
    }

    public static function setAdministratorRoleId($administratorRoleId)
    {
        Option::setValueByKey('administrator_role_id', $administratorRoleId);
    }

    public static function getEditorRole()
    {
        $editorRoleId = Option::getValueByKey('editor_role_id');
        $editorRole   = Role::whereId($editorRoleId)->first();
        if (!$editorRole)
            throw new SystemException(SystemException::AdministratorRoleNotDefined);

        return $editorRole;
    }

    public static function setEditorRoleId($editorRoleId)
    {
        Option::setValueByKey('editor_role_id', $editorRoleId);
    }

    public static function getDefaultRole()
    {
        $defaultRoleId = Option::getValueByKey('default_role_id');
        $defaultRole   = Role::whereId($defaultRoleId)->first();
        if (!$defaultRole)
            throw new SystemException(SystemException::DefaultRoleNotDefined);

        return $defaultRole;
    }

    public static function setDefaultRoleId($defaultRoleId)
    {
        Option::setValueByKey('default_role_id', $defaultRoleId);
    }
}