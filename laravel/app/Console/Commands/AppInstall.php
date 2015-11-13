<?php namespace App\Console\Commands;

use App;
use App\Http\Requests\SignUpRequest;
use App\Option;
use App\Services\System;
use App\Services\UserManager;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Validator;

class AppInstall extends Command
{

    protected $name        = 'app:install';
    protected $description = 'Install the app';

    public function fire()
    {
        App::setLocale('en');

        try {
            \DB::table('options')->get();
        } catch (QueryException $exception) {
            $this->error('Run artisan migrate before app install.');
            return;
        }

        if (System::isInstalled()) {
            $this->error('App is already installed.');
            return;
        }

        $siteName        = $this->ask('Site Name?');
        $masterUsername  = $this->ask('Master Username?');
        $masterEmail     = $this->ask('Master Email?');
        $masterPassword  = $this->secret('Master Password?');
        $confirmPassword = $this->secret('Confirm Password?');

        $validator = Validator::make([
            'username'                       => $masterUsername,
            'email'                          => $masterEmail,
            'password'                       => $masterPassword,
            'password_confirmation'          => $confirmPassword,
            'verification_code_for_username' => '-',
            'verification_code_for_email'    => '-',
        ], (new SignUpRequest())->rules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return;
        }

        System::setSiteName($siteName);

        $masterRole       = new App\Role();
        $masterRole->name = 'Master';
        $masterRole->save();
        System::setMasterRoleId($masterRole);

        $adminRole       = new App\Role();
        $adminRole->name = 'Administrator';
        $adminRole->save();
        System::setAdministratorRoleId($adminRole);

        $editorRole       = new App\Role();
        $editorRole->name = 'Editor';
        $editorRole->save();
        System::setEditorRoleId($editorRole);

        $defaultRole       = new App\Role();
        $defaultRole->name = 'User';
        $defaultRole->save();
        System::setDefaultRoleId($defaultRole);

        $user = UserManager::signUp($masterUsername, $masterEmail, null, $masterPassword);
        $user->save();

        $user->roles()->attach($masterRole);

        Option::setValueByKey('installed', true);

        $this->info('Install success!');
    }

}
