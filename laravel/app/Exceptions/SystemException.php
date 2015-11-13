<?php namespace App\Exceptions;

use Exception;

class SystemException extends Exception
{
    const SystemNotInstall            = 0x01;
    const MasterRoleNotDefined        = 0x02;
    const AdministratorRoleNotDefined = 0x03;
    const EditorRoleNotDefined        = 0x04;
    const DefaultRoleNotDefined       = 0x05;

    public function __construct($code = 0, array $data = [], Exception $previous = null)
    {
        switch ($code) {
            case self::SystemNotInstall:
                $message = trans('exception.system.not_install');
                break;
            case self::MasterRoleNotDefined:
                $message = trans('exception.system.master_role_not_defined');
                break;
            case self::AdministratorRoleNotDefined:
                $message = trans('exception.system.administrator_role_not_defined');
                break;
            case self::DefaultRoleNotDefined:
                $message = trans('exception.system.default_role_not_defined');
                break;
            default:
                $message = trans('str.unknown_exception');
                break;
        }

        parent::__construct($message, $code, $previous);
    }

}